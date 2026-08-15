<?php
declare(strict_types=1);

namespace App\Core;

use JsonException;

final class RateLimiter
{
    private const DEFAULT_LIMIT = 12;
    private const DEFAULT_WINDOW_SECONDS = 3600;
    private const STORAGE_DIRECTORY = 'rate-limits';

    /**
     * Determine whether the supplied rate-limit key may continue.
     *
     * Existing usage remains unchanged:
     *
     * RateLimiter::allow('submission:' . $ip, 10, 3600);
     *
     * The limiter intentionally fails open if its storage layer becomes
     * unavailable. This preserves website availability while still enforcing
     * limits whenever the rate-limit store is operational.
     */
    public static function allow(
        string $key,
        int $limit = self::DEFAULT_LIMIT,
        int $windowSeconds = self::DEFAULT_WINDOW_SECONDS
    ): bool {
        $key = trim($key);

        if ($key === '') {
            return true;
        }

        if ($limit < 1 || $windowSeconds < 1) {
            return true;
        }

        $directory = self::storageDirectory();

        if (!self::ensureDirectory($directory)) {
            return true;
        }

        $file = self::filePath($directory, $key);
        $handle = @fopen($file, 'c+');

        if ($handle === false) {
            return true;
        }

        $locked = false;

        try {
            $locked = flock($handle, LOCK_EX);

            if (!$locked) {
                return true;
            }

            $now = time();

            $data = self::readState(
                $handle,
                $now
            );

            $windowStartedAt = (int) ($data['window'] ?? $now);
            $count = (int) ($data['count'] ?? 0);

            if (
                $windowStartedAt <= 0
                || ($windowStartedAt + $windowSeconds) <= $now
            ) {
                $windowStartedAt = $now;
                $count = 0;
            }

            if ($count >= $limit) {
                return false;
            }

            $state = [
                'window' => $windowStartedAt,
                'count' => $count + 1,
            ];

            if (!self::writeState($handle, $state)) {
                /*
                 * Preserve the original availability-first behaviour:
                 * inability to persist the limiter must not take the public
                 * website offline.
                 */
                return true;
            }

            self::tightenFilePermissions($file);

            return true;
        } finally {
            if ($locked) {
                flock($handle, LOCK_UN);
            }

            fclose($handle);
        }
    }

    /**
     * Build the dedicated rate-limit storage directory.
     */
    private static function storageDirectory(): string
    {
        $storagePath = rtrim(
            (string) config('storage_path'),
            '/\\'
        );

        if ($storagePath === '') {
            return '';
        }

        return $storagePath . DIRECTORY_SEPARATOR . self::STORAGE_DIRECTORY;
    }

    /**
     * Ensure the rate-limit directory exists.
     */
    private static function ensureDirectory(string $directory): bool
    {
        if ($directory === '') {
            return false;
        }

        if (is_dir($directory)) {
            return is_writable($directory);
        }

        if (!@mkdir($directory, 0775, true) && !is_dir($directory)) {
            return false;
        }

        return is_writable($directory);
    }

    /**
     * Generate an opaque filename from the rate-limit key.
     */
    private static function filePath(
        string $directory,
        string $key
    ): string {
        return $directory
            . DIRECTORY_SEPARATOR
            . hash('sha256', $key)
            . '.json';
    }

    /**
     * Read the current limiter state.
     *
     * Invalid, corrupted or empty state is treated as a fresh window rather
     * than causing a public request failure.
     *
     * @param resource $handle
     * @return array{window:int,count:int}
     */
    private static function readState(
        $handle,
        int $now
    ): array {
        rewind($handle);

        $raw = stream_get_contents($handle);

        if (!is_string($raw) || trim($raw) === '') {
            return self::freshState($now);
        }

        try {
            $decoded = json_decode(
                $raw,
                true,
                16,
                JSON_THROW_ON_ERROR
            );
        } catch (JsonException) {
            return self::freshState($now);
        }

        if (!is_array($decoded)) {
            return self::freshState($now);
        }

        $window = filter_var(
            $decoded['window'] ?? null,
            FILTER_VALIDATE_INT
        );

        $count = filter_var(
            $decoded['count'] ?? null,
            FILTER_VALIDATE_INT
        );

        if (
            $window === false
            || $count === false
            || $window <= 0
            || $count < 0
        ) {
            return self::freshState($now);
        }

        return [
            'window' => (int) $window,
            'count' => (int) $count,
        ];
    }

    /**
     * Persist rate-limit state while the caller holds the exclusive lock.
     *
     * @param resource $handle
     * @param array{window:int,count:int} $state
     */
    private static function writeState(
        $handle,
        array $state
    ): bool {
        try {
            $json = json_encode(
                $state,
                JSON_THROW_ON_ERROR
            );
        } catch (JsonException) {
            return false;
        }

        if (ftruncate($handle, 0) === false) {
            return false;
        }

        if (rewind($handle) === false) {
            return false;
        }

        $length = strlen($json);
        $written = 0;

        while ($written < $length) {
            $result = fwrite(
                $handle,
                substr($json, $written)
            );

            if ($result === false || $result === 0) {
                return false;
            }

            $written += $result;
        }

        return fflush($handle);
    }

    /**
     * Fresh state for a new rate-limit window.
     *
     * @return array{window:int,count:int}
     */
    private static function freshState(int $now): array
    {
        return [
            'window' => $now,
            'count' => 0,
        ];
    }

    /**
     * Restrict the rate-limit state file where the host permits chmod().
     */
    private static function tightenFilePermissions(string $file): void
    {
        if (!is_file($file)) {
            return;
        }

        @chmod($file, 0660);
    }
}