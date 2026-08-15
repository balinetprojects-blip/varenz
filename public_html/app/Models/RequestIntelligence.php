<?php
declare(strict_types=1);

namespace App\Models;

final class RequestIntelligence
{
    private const READINESS_COMPLETE = 'complete';
    private const READINESS_REVIEWABLE = 'reviewable';
    private const READINESS_BASIC = 'basic';

    private const CLASSIFICATION_SUPPORT = 'support_concern';
    private const CLASSIFICATION_SUGGESTION = 'suggestion';
    private const CLASSIFICATION_FEEDBACK = 'feedback';
    private const CLASSIFICATION_QUOTATION = 'quotation_request';

    /**
     * Deterministic request triage.
     *
     * The returned structure is intentionally preserved so this engine can
     * later be replaced or augmented by an AI classifier without changing
     * SubmissionRepository storage or downstream consumers.
     *
     * @param array<string, mixed> $payload
     * @return array{
     *     classification:string,
     *     readiness:string,
     *     signals:array{
     *         product:bool,
     *         specification:bool,
     *         quantity:bool,
     *         timeline:bool,
     *         document:bool
     *     },
     *     missing:list<string>,
     *     next_steps:list<string>
     * }
     */
    public static function fromPayload(array $payload): array
    {
        $text = self::buildSearchText($payload);

        $signals = [
            'product' => self::hasAny(
                $text,
                [
                    'contrast',
                    'contrast media',
                    'pacemaker',
                    'stent',
                    'enfit',
                    'syringe',
                    'ng tube',
                    'nasogastric tube',
                    'giving set',
                    'infusion set',
                    'irrigation',
                    'catheter',
                    'product',
                    'model',
                    'brand',
                ]
            ),

            'specification' => self::hasAny(
                $text,
                [
                    'size',
                    'model',
                    'reference',
                    'ref number',
                    'specification',
                    'specifications',
                    'spec',
                    'strength',
                    'connector',
                    'gauge',
                    'lot',
                    'batch',
                    'diameter',
                    'length',
                    'volume',
                ]
            ) || self::containsSpecificationPattern($text),

            'quantity' => self::containsQuantity($text),

            'timeline' => self::hasAny(
                $text,
                [
                    'urgent',
                    'urgently',
                    'today',
                    'tomorrow',
                    'this week',
                    'next week',
                    'this month',
                    'delivery',
                    'deadline',
                    'required by',
                    'needed by',
                    'before',
                    'as soon as possible',
                    'asap',
                ]
            ) || self::hasStructuredTimeline($payload),

            'document' => self::hasAny(
                $text,
                [
                    'quotation',
                    'quote',
                    'certificate',
                    'certificate of analysis',
                    'certificate of conformity',
                    'conformity',
                    'invoice',
                    'proforma',
                    'documentation',
                    'documents',
                    'coa',
                    'technical data',
                    'data sheet',
                    'datasheet',
                    'brochure',
                ]
            ),
        ];

        $score = self::signalScore($signals);

        $readiness = match (true) {
            $score >= 4 => self::READINESS_COMPLETE,
            $score >= 2 => self::READINESS_REVIEWABLE,
            default => self::READINESS_BASIC,
        };

        $missing = self::missingSignals($signals);

        return [
            'classification' => self::classify($text),
            'readiness' => $readiness,
            'signals' => $signals,
            'missing' => $missing,
            'next_steps' => self::nextSteps($missing),
        ];
    }

    /**
     * Build normalized text from both narrative and structured request fields.
     *
     * Structured values are deliberately included because users may provide
     * useful procurement details through dropdowns without repeating them in
     * the message body.
     *
     * @param array<string, mixed> $payload
     */
    private static function buildSearchText(array $payload): string
    {
        $parts = [
            self::scalar($payload['type'] ?? ''),
            self::scalar($payload['category'] ?? ''),
            self::scalar($payload['message'] ?? ''),
            self::scalar($payload['required_by'] ?? ''),
            self::scalar($payload['request_quality'] ?? ''),
            self::scalar($payload['intelligence_summary'] ?? ''),
        ];

        $text = implode(' ', array_filter(
            $parts,
            static fn(string $part): bool => $part !== ''
        ));

        $text = self::lower($text);

        /*
         * Normalize punctuation and repeated whitespace while retaining
         * characters useful for specifications such as slash, hyphen,
         * decimal points and percentage symbols.
         */
        $text = preg_replace(
            '/[^\p{L}\p{N}\s.\/%+\-_]/u',
            ' ',
            $text
        ) ?? $text;

        $text = preg_replace(
            '/\s+/u',
            ' ',
            $text
        ) ?? $text;

        return trim($text);
    }

    /**
     * Classify the broad business intent of the request.
     */
    private static function classify(string $text): string
    {
        if (
            self::hasAny(
                $text,
                [
                    'complaint',
                    'concern',
                    'problem',
                    'issue',
                    'damaged',
                    'defective',
                    'wrong item',
                    'wrong product',
                    'late delivery',
                    'delayed',
                    'missing item',
                    'not working',
                ]
            )
        ) {
            return self::CLASSIFICATION_SUPPORT;
        }

        if (
            self::hasAny(
                $text,
                [
                    'suggestion',
                    'suggest',
                    'recommend improvement',
                    'website suggestion',
                    'could improve',
                    'should improve',
                ]
            )
        ) {
            return self::CLASSIFICATION_SUGGESTION;
        }

        if (
            self::hasAny(
                $text,
                [
                    'feedback',
                    'service feedback',
                    'experience',
                    'thank you',
                    'appreciate',
                ]
            )
        ) {
            return self::CLASSIFICATION_FEEDBACK;
        }

        return self::CLASSIFICATION_QUOTATION;
    }

    /**
     * Calculate how many useful procurement signals are present.
     *
     * @param array<string, bool> $signals
     */
    private static function signalScore(array $signals): int
    {
        return count(
            array_filter(
                $signals,
                static fn(bool $value): bool => $value
            )
        );
    }

    /**
     * Identify missing procurement-critical information.
     *
     * The document signal remains useful intelligence but intentionally does
     * not affect the missing-information prompts.
     *
     * @param array<string, bool> $signals
     * @return list<string>
     */
    private static function missingSignals(array $signals): array
    {
        $required = [
            'product',
            'specification',
            'quantity',
            'timeline',
        ];

        $missing = [];

        foreach ($required as $key) {
            if (($signals[$key] ?? false) === false) {
                $missing[] = $key;
            }
        }

        return $missing;
    }

    /**
     * Convert missing signals into practical follow-up guidance.
     *
     * @param list<string> $missing
     * @return list<string>
     */
    private static function nextSteps(array $missing): array
    {
        $map = [
            'product' =>
                'Confirm the product name, brand, image or previous package reference.',

            'specification' =>
                'Add model, size, connector type, strength or other specifications.',

            'quantity' =>
                'Add quantity, units, packs or expected repeat supply volume.',

            'timeline' =>
                'Add delivery location and required timeline.',
        ];

        $steps = [];

        foreach ($missing as $key) {
            if (isset($map[$key])) {
                $steps[] = $map[$key];
            }
        }

        return $steps;
    }

    /**
     * Detect explicit quantities while avoiding generic standalone numbers
     * where possible.
     */
    private static function containsQuantity(string $text): bool
    {
        if ($text === '') {
            return false;
        }

        return preg_match(
            '/\b\d+(?:[.,]\d+)?\s*'
            . '(?:pcs?|pieces?|boxes?|cartons?|units?|packs?|sets?|'
            . 'vials?|bottles?|tubes?|bags?|cases?|kits?|pairs?|rolls?)\b/ui',
            $text
        ) === 1;
    }

    /**
     * Detect common healthcare product specification patterns.
     */
    private static function containsSpecificationPattern(string $text): bool
    {
        if ($text === '') {
            return false;
        }

        return preg_match(
            '/\b\d+(?:\.\d+)?\s*'
            . '(?:ml|l|mg|mcg|g|kg|mm|cm|fr|ch|ga|gauge|mmhg|%)\b/ui',
            $text
        ) === 1;
    }

    /**
     * Structured required-by data should count as timeline information even
     * when the user does not repeat it inside the request message.
     *
     * @param array<string, mixed> $payload
     */
    private static function hasStructuredTimeline(array $payload): bool
    {
        return trim(
            self::scalar(
                $payload['required_by'] ?? ''
            )
        ) !== '';
    }

    /**
     * Look for complete phrases or standalone terms.
     *
     * This is safer than raw str_contains() for short words such as "spec"
     * because it reduces accidental matches inside unrelated words.
     *
     * @param list<string> $needles
     */
    private static function hasAny(
        string $text,
        array $needles
    ): bool {
        if ($text === '') {
            return false;
        }

        foreach ($needles as $needle) {
            $needle = trim(
                self::lower($needle)
            );

            if ($needle === '') {
                continue;
            }

            $pattern = '/(?<![\p{L}\p{N}])'
                . preg_quote($needle, '/')
                . '(?![\p{L}\p{N}])/u';

            if (preg_match($pattern, $text) === 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * Safely normalize mixed payload values without allowing arrays or
     * arbitrary objects to become misleading strings such as "Array".
     */
    private static function scalar(mixed $value): string
    {
        if (
            is_string($value)
            || is_int($value)
            || is_float($value)
            || is_bool($value)
        ) {
            return trim((string) $value);
        }

        return '';
    }

    /**
     * Unicode-aware lowercase helper with a safe fallback.
     */
    private static function lower(string $value): string
    {
        return function_exists('mb_strtolower')
            ? mb_strtolower($value, 'UTF-8')
            : strtolower($value);
    }
}