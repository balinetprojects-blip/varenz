<?php
declare(strict_types=1);

namespace App\Models;

use JsonException;
use RuntimeException;

final class SubmissionRepository
{
    private const STORAGE_DIRECTORY_MODE = 0770;
    private const STORAGE_FILE_MODE = 0660;

    private const UPLOAD_DIRECTORY = 'uploads';
    private const SUBMISSION_FILE_PREFIX = 'submissions-';

    private const DEFAULT_MAX_UPLOAD_BYTES = 8_388_608; // 8 MB

    /**
     * Store a submission record and optional attachment.
     *
     * @param array<string, mixed> $payload
     * @param array<string, mixed>|null $file
     * @return array<string, mixed>
     */
    public function store(array $payload, ?array $file): array
    {
        $storage = $this->storagePath();

        $this->ensureDirectory(
            $storage,
            'The private storage directory could not be created.'
        );

        $reference = $this->safeReference(
            (string) ($payload['reference'] ?? '')
        );

        if ($reference === '') {
            throw new RuntimeException(
                'The submission reference is invalid.'
            );
        }

        $attachment = null;
        $attachmentPath = null;

        try {
            if ($file !== null) {
                [$attachment, $attachmentPath] = $this->storeUpload(
                    $file,
                    $reference
                );
            }

            $record = $payload + [
                'attachment' => $attachment,
                'created_at' => date(DATE_ATOM),
            ];

            $target = $storage
                . DIRECTORY_SEPARATOR
                . self::SUBMISSION_FILE_PREFIX
                . date('Y-m')
                . '.jsonl';

            $this->appendRecord(
                $target,
                $record
            );

            return $record;
        } catch (\Throwable $exception) {
            /*
             * If the attachment was successfully moved but the submission
             * record could not be persisted, remove the orphaned file.
             */
            if (
                is_string($attachmentPath)
                && $attachmentPath !== ''
                && is_file($attachmentPath)
            ) {
                @unlink($attachmentPath);
            }

            if ($exception instanceof RuntimeException) {
                throw $exception;
            }

            throw new RuntimeException(
                'The submission could not be stored.',
                0,
                $exception
            );
        }
    }

    /**
     * Validate and persist an uploaded attachment.
     *
     * @param array<string, mixed> $file
     * @return array{0:?array<string,mixed>,1:?string}
     */
    private function storeUpload(
        array $file,
        string $reference
    ): array {
        $error = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);

        if ($error === UPLOAD_ERR_NO_FILE) {
            return [null, null];
        }

        if ($error !== UPLOAD_ERR_OK) {
            throw new RuntimeException(
                $this->uploadErrorMessage($error)
            );
        }

        $tmpName = (string) ($file['tmp_name'] ?? '');

        if (
            $tmpName === ''
            || !is_uploaded_file($tmpName)
        ) {
            throw new RuntimeException(
                'The attachment upload could not be verified.'
            );
        }

        $size = (int) ($file['size'] ?? 0);
        $maxBytes = $this->maxUploadBytes();

        if ($size <= 0) {
            throw new RuntimeException(
                'The attachment is empty.'
            );
        }

        if ($size > $maxBytes) {
            throw new RuntimeException(
                'The attachment is larger than the allowed upload limit.'
            );
        }

        $original = $this->safeOriginalName(
            (string) ($file['name'] ?? 'attachment')
        );

        $extension = strtolower(
            pathinfo(
                $original,
                PATHINFO_EXTENSION
            )
        );

        if ($extension === '') {
            throw new RuntimeException(
                'The attachment file extension is missing.'
            );
        }

        $allowedExtensions = $this->allowedExtensions();

        if (
            !in_array(
                $extension,
                $allowedExtensions,
                true
            )
        ) {
            throw new RuntimeException(
                'This attachment type is not allowed.'
            );
        }

        $mime = $this->detectMime($tmpName);

        if (
            !$this->mimeMatchesExtension(
                $extension,
                $mime
            )
        ) {
            throw new RuntimeException(
                'The attachment content does not match its file type.'
            );
        }

        $directory = $this->storagePath()
            . DIRECTORY_SEPARATOR
            . self::UPLOAD_DIRECTORY
            . DIRECTORY_SEPARATOR
            . date('Y-m');

        $this->ensureDirectory(
            $directory,
            'The attachment storage directory could not be created.'
        );

        $safeBaseName = $this->safeBaseName(
            pathinfo(
                $original,
                PATHINFO_FILENAME
            )
        );

        $filename = $this->uniqueUploadFilename(
            $reference,
            $safeBaseName,
            $extension
        );

        $target = $directory
            . DIRECTORY_SEPARATOR
            . $filename;

        if (!move_uploaded_file($tmpName, $target)) {
            throw new RuntimeException(
                'The attachment could not be saved.'
            );
        }

        @chmod(
            $target,
            self::STORAGE_FILE_MODE
        );

        return [
            [
                'original_name' => $original,
                'stored_name' => $filename,
                'size' => $size,
                'mime' => $mime,
            ],
            $target,
        ];
    }

    /**
     * Append one JSON record safely to the monthly JSONL file.
     *
     * @param array<string, mixed> $record
     */
    private function appendRecord(
        string $target,
        array $record
    ): void {
        try {
            $json = json_encode(
                $record,
                JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
                | JSON_THROW_ON_ERROR
            );
        } catch (JsonException $exception) {
            throw new RuntimeException(
                'The submission could not be encoded.',
                0,
                $exception
            );
        }

        $handle = @fopen(
            $target,
            'ab'
        );

        if ($handle === false) {
            throw new RuntimeException(
                'The submission could not be written.'
            );
        }

        $locked = false;

        try {
            $locked = flock(
                $handle,
                LOCK_EX
            );

            if (!$locked) {
                throw new RuntimeException(
                    'The submission storage is temporarily unavailable.'
                );
            }

            $line = $json . PHP_EOL;
            $length = strlen($line);
            $written = 0;

            while ($written < $length) {
                $result = fwrite(
                    $handle,
                    substr(
                        $line,
                        $written
                    )
                );

                if (
                    $result === false
                    || $result === 0
                ) {
                    throw new RuntimeException(
                        'The submission could not be written.'
                    );
                }

                $written += $result;
            }

            if (!fflush($handle)) {
                throw new RuntimeException(
                    'The submission could not be finalized.'
                );
            }

            @chmod(
                $target,
                self::STORAGE_FILE_MODE
            );
        } finally {
            if ($locked) {
                flock(
                    $handle,
                    LOCK_UN
                );
            }

            fclose($handle);
        }
    }

    /**
     * Return the configured private storage path.
     */
    private function storagePath(): string
    {
        $storage = trim(
            (string) config('storage_path')
        );

        if ($storage === '') {
            throw new RuntimeException(
                'The private storage path is not configured.'
            );
        }

        return rtrim(
            $storage,
            '/\\'
        );
    }

    /**
     * Create a private directory if required and confirm it is writable.
     */
    private function ensureDirectory(
        string $directory,
        string $errorMessage
    ): void {
        if (is_dir($directory)) {
            if (!is_writable($directory)) {
                throw new RuntimeException(
                    $errorMessage
                );
            }

            return;
        }

        if (
            !@mkdir(
                $directory,
                self::STORAGE_DIRECTORY_MODE,
                true
            )
            && !is_dir($directory)
        ) {
            throw new RuntimeException(
                $errorMessage
            );
        }

        @chmod(
            $directory,
            self::STORAGE_DIRECTORY_MODE
        );

        if (!is_writable($directory)) {
            throw new RuntimeException(
                $errorMessage
            );
        }
    }

    /**
     * Normalize the public submission reference before using it in a filename.
     */
    private function safeReference(string $reference): string
    {
        $reference = strtoupper(
            trim($reference)
        );

        $reference = preg_replace(
            '/[^A-Z0-9_-]/',
            '',
            $reference
        ) ?? '';

        return substr(
            $reference,
            0,
            80
        );
    }

    /**
     * Preserve a safe display version of the original uploaded filename.
     */
    private function safeOriginalName(string $name): string
    {
        $name = basename(
            str_replace(
                '\\',
                '/',
                trim($name)
            )
        );

        $name = preg_replace(
            '/[\x00-\x1F\x7F]/',
            '',
            $name
        ) ?? '';

        $name = trim($name);

        if ($name === '') {
            return 'attachment';
        }

        return substr(
            $name,
            0,
            255
        );
    }

    /**
     * Sanitize the human-readable portion of the stored filename.
     */
    private function safeBaseName(string $name): string
    {
        $name = preg_replace(
            '/[^a-zA-Z0-9._-]+/',
            '-',
            $name
        ) ?? '';

        $name = trim(
            $name,
            '-.'
        );

        if ($name === '') {
            return 'attachment';
        }

        return substr(
            $name,
            0,
            100
        );
    }

    /**
     * Avoid collisions between multiple uploads with the same reference/name.
     */
    private function uniqueUploadFilename(
        string $reference,
        string $safeBaseName,
        string $extension
    ): string {
        $random = bin2hex(
            random_bytes(4)
        );

        return sprintf(
            '%s-%s-%s.%s',
            $reference,
            $safeBaseName,
            $random,
            $extension
        );
    }

    /**
     * Return the configured maximum upload size.
     */
    private function maxUploadBytes(): int
    {
        $configured = (int) config(
            'upload.max_bytes'
        );

        return $configured > 0
            ? $configured
            : self::DEFAULT_MAX_UPLOAD_BYTES;
    }

    /**
     * Normalize configured extension allow-list.
     *
     * @return list<string>
     */
    private function allowedExtensions(): array
    {
        $configured = config(
            'upload.allowed_extensions'
        );

        if (!is_array($configured)) {
            return [];
        }

        $allowed = [];

        foreach ($configured as $extension) {
            if (!is_string($extension)) {
                continue;
            }

            $extension = strtolower(
                trim(
                    ltrim(
                        $extension,
                        '.'
                    )
                )
            );

            if (
                $extension !== ''
                && preg_match(
                    '/^[a-z0-9]+$/',
                    $extension
                ) === 1
            ) {
                $allowed[] = $extension;
            }
        }

        return array_values(
            array_unique($allowed)
        );
    }

    /**
     * Detect actual MIME type from uploaded content.
     */
    private function detectMime(string $tmpName): string
    {
        if (!class_exists(\finfo::class)) {
            throw new RuntimeException(
                'The server cannot verify attachment file types.'
            );
        }

        $finfo = new \finfo(
            FILEINFO_MIME_TYPE
        );

        $detected = $finfo->file(
            $tmpName
        );

        if (
            !is_string($detected)
            || trim($detected) === ''
        ) {
            throw new RuntimeException(
                'The attachment file type could not be verified.'
            );
        }

        return strtolower(
            trim(
                explode(
                    ';',
                    $detected,
                    2
                )[0]
            )
        );
    }

    /**
     * Compare detected MIME with the expected MIME family.
     */
    private function mimeMatchesExtension(
        string $extension,
        string $mime
    ): bool {
        $mimeMap = [
            'pdf' => [
                'application/pdf',
            ],

            'doc' => [
                'application/msword',
                'application/CDFV2',
                'application/x-ole-storage',
            ],

            'docx' => [
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/zip',
            ],

            'xls' => [
                'application/vnd.ms-excel',
                'application/CDFV2',
                'application/x-ole-storage',
            ],

            'xlsx' => [
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/zip',
            ],

            'csv' => [
                'text/csv',
                'text/plain',
                'application/csv',
                'application/vnd.ms-excel',
            ],

            'jpg' => [
                'image/jpeg',
            ],

            'jpeg' => [
                'image/jpeg',
            ],

            'png' => [
                'image/png',
            ],

            'webp' => [
                'image/webp',
            ],
        ];

        return in_array(
            $mime,
            $mimeMap[$extension] ?? [],
            true
        );
    }

    /**
     * Convert PHP upload errors into safe public-facing messages.
     */
    private function uploadErrorMessage(int $error): string
    {
        return match ($error) {
            UPLOAD_ERR_INI_SIZE,
            UPLOAD_ERR_FORM_SIZE =>
                'The attachment is larger than the allowed upload limit.',

            UPLOAD_ERR_PARTIAL =>
                'The attachment upload was incomplete.',

            UPLOAD_ERR_NO_TMP_DIR,
            UPLOAD_ERR_CANT_WRITE,
            UPLOAD_ERR_EXTENSION =>
                'The attachment could not be stored by the server.',

            default =>
                'The attachment upload failed.',
        };
    }
}