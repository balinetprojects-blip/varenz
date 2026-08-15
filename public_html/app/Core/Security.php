<?php
declare(strict_types=1);

namespace App\Core;

use RuntimeException;
use Stringable;

final class Security
{
    private const CSRF_SESSION_KEY = 'vsl_csrf';

    /**
     * 32 random bytes = 256 bits of entropy.
     *
     * The hexadecimal representation is therefore exactly 64 characters.
     */
    private const CSRF_BYTES = 32;
    private const CSRF_HEX_LENGTH = self::CSRF_BYTES * 2;

    private const DEFAULT_TEXT_LIMIT = 4000;
    private const MAX_TEXT_LIMIT = 100000;

    private const DEFAULT_REFERENCE_PREFIX = 'VSL';
    private const MAX_REFERENCE_PREFIX_LENGTH = 16;

    /**
     * Return the session-scoped CSRF token.
     *
     * Existing forms remain compatible because the token is stable for the
     * current session unless explicitly rotated.
     */
    public static function csrfToken(): string
    {
        self::ensureSessionAvailable();

        $existing = $_SESSION[self::CSRF_SESSION_KEY] ?? null;

        if (!self::isValidCsrfToken($existing)) {
            $_SESSION[self::CSRF_SESSION_KEY] = self::generateCsrfToken();
        }

        return (string) $_SESSION[self::CSRF_SESSION_KEY];
    }

    /**
     * Verify a submitted CSRF token using constant-time comparison.
     */
    public static function verifyCsrf(?string $token): bool
    {
        if (
            !is_string($token)
            || !self::isValidCsrfToken($token)
        ) {
            return false;
        }

        $stored = self::csrfToken();

        return hash_equals($stored, $token);
    }

    /**
     * Explicitly rotate the CSRF token.
     *
     * This is additive and does not change existing behavior. It can safely
     * be used later after authentication state changes or other sensitive
     * session transitions.
     */
    public static function rotateCsrfToken(): string
    {
        self::ensureSessionAvailable();

        $_SESSION[self::CSRF_SESSION_KEY] = self::generateCsrfToken();

        return (string) $_SESSION[self::CSRF_SESSION_KEY];
    }

    /**
     * Normalize plain user-supplied text.
     *
     * This function is intended for storage, comparison and validation.
     *
     * It does NOT perform HTML escaping. Output escaping should occur at the
     * rendering boundary because escaping at storage time can result in
     * double-encoding.
     */
    public static function cleanText(
        mixed $value,
        int $max = self::DEFAULT_TEXT_LIMIT
    ): string {
        $max = self::normalizeTextLimit($max);

        if ($max === 0) {
            return '';
        }

        $value = self::scalarToString($value);

        if ($value === '') {
            return '';
        }

        $value = self::normalizeUtf8($value);

        /*
         * Remove ASCII control characters except:
         *
         * \t  horizontal tab
         * \n  line feed
         * \r  carriage return
         *
         * Those three are intentionally retained so textarea content and
         * legitimate multiline requests continue to work.
         */
        $value = preg_replace(
            '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u',
            '',
            $value
        ) ?? '';

        /*
         * Strip the Unicode byte-order mark if supplied at the beginning of
         * user input.
         */
        $value = preg_replace(
            '/^\x{FEFF}/u',
            '',
            $value
        ) ?? $value;

        $value = trim($value);

        if ($value === '') {
            return '';
        }

        return self::truncateUtf8(
            $value,
            $max
        );
    }

    /**
     * Escape text when rendering into an HTML text/attribute context.
     *
     * Existing code does not need to migrate immediately. This helper is
     * available for future view cleanup.
     */
    public static function escapeHtml(mixed $value): string
    {
        return htmlspecialchars(
            self::scalarToString($value),
            ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5,
            'UTF-8'
        );
    }

    /**
     * Generate a non-sequential public reference identifier.
     *
     * Example:
     *
     * VSL-20260810-A1B2C3D4
     */
    public static function reference(
        string $prefix = self::DEFAULT_REFERENCE_PREFIX
    ): string {
        $prefix = self::normalizeReferencePrefix($prefix);

        /*
         * Four random bytes provide 32 random bits for the public suffix.
         *
         * This is stronger than the previous three-byte suffix while keeping
         * references short and human-readable.
         */
        $random = strtoupper(
            bin2hex(
                random_bytes(4)
            )
        );

        return sprintf(
            '%s-%s-%s',
            $prefix,
            date('Ymd'),
            $random
        );
    }

    /**
     * Generate a cryptographically secure CSRF token.
     */
    private static function generateCsrfToken(): string
    {
        return bin2hex(
            random_bytes(self::CSRF_BYTES)
        );
    }

    /**
     * Ensure the CSRF session container is available.
     *
     * Session creation should normally happen during application bootstrap.
     * This guard prevents silent operation against an unavailable session.
     */
    private static function ensureSessionAvailable(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        if (headers_sent()) {
            throw new RuntimeException(
                'Unable to initialize security session.'
            );
        }

        if (!session_start()) {
            throw new RuntimeException(
                'Unable to initialize security session.'
            );
        }
    }

    /**
     * Validate both generated and submitted CSRF token shape before using
     * constant-time comparison.
     */
    private static function isValidCsrfToken(mixed $token): bool
    {
        if (!is_string($token)) {
            return false;
        }

        if (strlen($token) !== self::CSRF_HEX_LENGTH) {
            return false;
        }

        return ctype_xdigit($token);
    }

    /**
     * Convert only safe scalar-like values to strings.
     *
     * Arrays and arbitrary objects are rejected instead of producing values
     * such as "Array" or throwing conversion warnings.
     */
    private static function scalarToString(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_string($value)) {
            return $value;
        }

        if (
            is_int($value)
            || is_float($value)
            || is_bool($value)
        ) {
            return (string) $value;
        }

        if ($value instanceof Stringable) {
            try {
                return (string) $value;
            } catch (\Throwable) {
                return '';
            }
        }

        return '';
    }

    /**
     * Keep input limits bounded and predictable.
     */
    private static function normalizeTextLimit(int $max): int
    {
        if ($max <= 0) {
            return 0;
        }

        return min(
            $max,
            self::MAX_TEXT_LIMIT
        );
    }

    /**
     * Repair or reject malformed UTF-8 safely.
     */
    private static function normalizeUtf8(string $value): string
    {
        if ($value === '') {
            return '';
        }

        if (
            function_exists('mb_check_encoding')
            && mb_check_encoding($value, 'UTF-8')
        ) {
            return $value;
        }

        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding(
                $value,
                'UTF-8',
                'UTF-8'
            );
        }

        /*
         * Without mbstring, remove invalid UTF-8 sequences using iconv where
         * available. If neither extension exists, preserve the original input
         * and rely on downstream output escaping.
         */
        if (function_exists('iconv')) {
            $normalized = @iconv(
                'UTF-8',
                'UTF-8//IGNORE',
                $value
            );

            if (is_string($normalized)) {
                return $normalized;
            }
        }

        return $value;
    }

    /**
     * Truncate without cutting through a multibyte character.
     */
    private static function truncateUtf8(
        string $value,
        int $max
    ): string {
        if (function_exists('mb_substr')) {
            return mb_substr(
                $value,
                0,
                $max,
                'UTF-8'
            );
        }

        return substr(
            $value,
            0,
            $max
        );
    }

    /**
     * Restrict public reference prefixes to predictable ASCII identifiers.
     */
    private static function normalizeReferencePrefix(
        string $prefix
    ): string {
        $prefix = strtoupper(
            trim($prefix)
        );

        $prefix = preg_replace(
            '/[^A-Z0-9_-]/',
            '',
            $prefix
        ) ?? '';

        $prefix = substr(
            $prefix,
            0,
            self::MAX_REFERENCE_PREFIX_LENGTH
        );

        return $prefix !== ''
            ? $prefix
            : self::DEFAULT_REFERENCE_PREFIX;
    }
}