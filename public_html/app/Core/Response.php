<?php
declare(strict_types=1);

namespace App\Core;

use JsonException;

final class Response
{
    private const DEFAULT_STATUS = 200;
    private const MIN_STATUS = 100;
    private const MAX_STATUS = 599;

    /**
     * Send a JSON response and terminate execution.
     */
    public static function json(
        array $payload,
        int $status = self::DEFAULT_STATUS
    ): never {
        $status = self::normalizeStatus($status);

        try {
            $body = json_encode(
                $payload,
                JSON_UNESCAPED_SLASHES
                | JSON_UNESCAPED_UNICODE
                | JSON_THROW_ON_ERROR
            );
        } catch (JsonException) {
            /*
             * Avoid exposing encoding internals to the client.
             *
             * A minimal valid JSON response is safer than emitting malformed
             * output if an unexpected payload cannot be encoded.
             */
            $status = 500;
            $body = '{"ok":false,"message":"Unable to generate response."}';
        }

        self::sendStatus($status);

        self::sendHeader(
            'Content-Type: application/json; charset=utf-8'
        );

        self::sendHeader(
            'Cache-Control: no-store, no-cache, must-revalidate, max-age=0'
        );

        self::sendHeader(
            'Pragma: no-cache'
        );

        self::sendHeader(
            'X-Content-Type-Options: nosniff'
        );

        echo $body;
        exit;
    }

    /**
     * Send an HTML response and terminate execution.
     */
    public static function html(
        string $html,
        int $status = self::DEFAULT_STATUS
    ): never {
        $status = self::normalizeStatus($status);

        self::sendStatus($status);

        self::sendHeader(
            'Content-Type: text/html; charset=utf-8'
        );

        self::sendHeader(
            'X-Content-Type-Options: nosniff'
        );

        echo $html;
        exit;
    }

    /**
     * Keep HTTP response codes within the valid protocol range.
     */
    private static function normalizeStatus(int $status): int
    {
        if (
            $status < self::MIN_STATUS
            || $status > self::MAX_STATUS
        ) {
            return 500;
        }

        return $status;
    }

    /**
     * Set the HTTP response status when headers are still available.
     */
    private static function sendStatus(int $status): void
    {
        if (headers_sent()) {
            return;
        }

        http_response_code($status);
    }

    /**
     * Send a response header only when PHP has not started emitting output.
     */
    private static function sendHeader(string $header): void
    {
        if (headers_sent()) {
            return;
        }

        header($header);
    }
}