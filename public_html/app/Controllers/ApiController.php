<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\RateLimiter;
use App\Core\Response;
use App\Core\Security;
use App\Models\RequestIntelligence;
use App\Models\SiteRepository;
use App\Models\SubmissionRepository;
use RuntimeException;

final class ApiController extends Controller
{
    private const SUBMISSION_RATE_LIMIT = 10;
    private const SUBMISSION_RATE_WINDOW = 3600;
    private const MINIMUM_FORM_COMPLETION_SECONDS = 2;

    private const LIMIT_DEPARTMENT = 80;
    private const LIMIT_SEARCH_TERM = 120;
    private const LIMIT_PRODUCT_CATEGORY = 80;
    private const LIMIT_NAME = 120;
    private const LIMIT_PHONE = 60;
    private const LIMIT_EMAIL = 180;
    private const LIMIT_ORGANIZATION = 180;
    private const LIMIT_TYPE = 80;
    private const LIMIT_CATEGORY = 120;
    private const LIMIT_PREFERRED_CONTACT = 80;
    private const LIMIT_REQUIRED_BY = 40;
    private const LIMIT_REQUEST_QUALITY = 40;
    private const LIMIT_INTELLIGENCE_SUMMARY = 500;
    private const LIMIT_MESSAGE = 5000;
    private const LIMIT_SOURCE = 100;
    private const LIMIT_HONEYPOT = 120;

    private ?SiteRepository $siteRepository = null;

    /**
     * Lazily instantiate the site repository so the same instance can be reused
     * throughout the lifetime of this controller.
     */
    private function repository(): SiteRepository
    {
        return $this->siteRepository ??= new SiteRepository();
    }

    /**
     * Return a single challenge.
     */
    public function challenge(string $id): never
    {
        $this->respondWithItem(
            $this->repository()->find('challenges', $id)
        );
    }

    /**
     * Return a single product/category entry.
     */
    public function category(string $id): never
    {
        $this->respondWithItem(
            $this->repository()->find('categories', $id)
        );
    }

    /**
     * Return a single procurement entry.
     */
    public function procurement(string $id): never
    {
        $this->respondWithItem(
            $this->repository()->find('procurement', $id)
        );
    }

    /**
     * Return a single supported-organization entry.
     */
    public function organization(string $id): never
    {
        $this->respondWithItem(
            $this->repository()->find('organizations', $id)
        );
    }

    /**
     * Return team members, optionally filtered by department.
     */
    public function team(): never
    {
        $department = $this->cleanInput(
            'department',
            'all',
            self::LIMIT_DEPARTMENT
        );

        Response::json([
            'ok' => true,
            'data' => $this->repository()->team($department),
        ]);
    }

    /**
     * Search public website content.
     */
    public function search(): never
    {
        $term = $this->cleanInput(
            'q',
            '',
            self::LIMIT_SEARCH_TERM
        );

        Response::json([
            'ok' => true,
            'data' => $this->repository()->search($term),
        ]);
    }

    /**
     * Return the canonical product catalogue with safe public filters.
     */
    public function products(): never
    {
        $term = $this->cleanInput('q', '', self::LIMIT_SEARCH_TERM);
        $category = $this->cleanInput('category', '', self::LIMIT_PRODUCT_CATEGORY);

        Response::json([
            'ok' => true,
            'data' => $this->repository()->products($term, $category),
        ]);
    }

    /**
     * Return one canonical product.
     */
    public function product(string $slug): never
    {
        $this->respondWithItem(
            $this->repository()->findProductBySlug($slug)
        );
    }

    /**
     * Handle website quotation, enquiry and support submissions.
     */
    public function submit(): never
    {
        $this->enforceSubmissionRateLimit();
        $this->verifySubmissionCsrf();
        $this->handleHoneypot();
        $this->validateSubmissionTiming();

        $name = $this->cleanInput(
            'name',
            '',
            self::LIMIT_NAME
        );

        $phone = $this->cleanInput(
            'phone',
            '',
            self::LIMIT_PHONE
        );

        $email = $this->validatedEmail(
            $this->cleanInput(
                'email',
                '',
                self::LIMIT_EMAIL
            )
        );

        $message = $this->cleanInput(
            'message',
            '',
            self::LIMIT_MESSAGE
        );

        if (
            $name === ''
            || ($phone === '' && $email === '')
            || $message === ''
        ) {
            $this->error(
                'Complete your name, contact information and request details.',
                422
            );
        }

        $reference = Security::reference('VSL');

        $payload = [
            'reference' => $reference,

            'type' => $this->cleanInput(
                'type',
                'quotation',
                self::LIMIT_TYPE
            ),

            'name' => $name,

            'organization' => $this->cleanInput(
                'organization',
                '',
                self::LIMIT_ORGANIZATION
            ),

            'email' => $email,

            'phone' => $phone,

            'category' => $this->cleanInput(
                'category',
                '',
                self::LIMIT_CATEGORY
            ),

            'preferred_contact' => $this->cleanInput(
                'preferred_contact',
                '',
                self::LIMIT_PREFERRED_CONTACT
            ),

            'required_by' => $this->cleanInput(
                'required_by',
                '',
                self::LIMIT_REQUIRED_BY
            ),

            'request_quality' => $this->cleanInput(
                'request_quality',
                'basic',
                self::LIMIT_REQUEST_QUALITY
            ),

            'intelligence_summary' => $this->cleanInput(
                'intelligence_summary',
                '',
                self::LIMIT_INTELLIGENCE_SUMMARY
            ),

            'message' => $message,

            'source' => $this->cleanInput(
                'source',
                'website',
                self::LIMIT_SOURCE
            ),

            'ip_hash' => $this->hashedClientIp(),
        ];

        $payload['request_intelligence'] = RequestIntelligence::fromPayload(
            $payload
        );

        try {
            (new SubmissionRepository())->store(
                $payload,
                $this->request->files['attachment'] ?? null
            );

            Response::json([
                'ok' => true,
                'message' => 'Your request has been received.',
                'reference' => $reference,
                'intelligence' => $payload['request_intelligence'],
            ], 201);
        } catch (RuntimeException $exception) {
            $this->error(
                $exception->getMessage(),
                422
            );
        }
    }

    /**
     * Protect the submission endpoint from excessive repeated requests.
     */
    private function enforceSubmissionRateLimit(): void
    {
        $key = 'submission:' . $this->request->ip();

        if (
            RateLimiter::allow(
                $key,
                self::SUBMISSION_RATE_LIMIT,
                self::SUBMISSION_RATE_WINDOW
            )
        ) {
            return;
        }

        $this->error(
            'Too many submissions. Please try again later.',
            429
        );
    }

    /**
     * Validate the existing CSRF token.
     */
    private function verifySubmissionCsrf(): void
    {
        if (
            Security::verifyCsrf(
                $this->request->input('csrf_token')
            )
        ) {
            return;
        }

        $this->error(
            'Your session token is invalid. Refresh the page and try again.',
            419
        );
    }

    /**
     * Preserve the existing honeypot behaviour.
     *
     * Automated submissions filling the hidden website field receive a generic
     * successful response without entering the normal submission pipeline.
     */
    private function handleHoneypot(): void
    {
        $website = $this->cleanInput(
            'website',
            '',
            self::LIMIT_HONEYPOT
        );

        if ($website === '') {
            return;
        }

        Response::json([
            'ok' => true,
            'message' => 'Submission received.',
        ]);
    }

    /**
     * Reject submissions completed suspiciously quickly while allowing older
     * forms and clients that do not provide the timestamp.
     */
    private function validateSubmissionTiming(): void
    {
        $started = filter_var(
            $this->request->input('form_started_at', 0),
            FILTER_VALIDATE_INT
        );

        if ($started === false || $started <= 0) {
            return;
        }

        $elapsed = time() - $started;

        if ($elapsed >= self::MINIMUM_FORM_COMPLETION_SECONDS) {
            return;
        }

        $this->error(
            'Please review the form before submitting.',
            422
        );
    }

    /**
     * Normalize and validate an optional email address.
     */
    private function validatedEmail(string $email): string
    {
        if ($email === '') {
            return '';
        }

        $validated = filter_var(
            $email,
            FILTER_VALIDATE_EMAIL
        );

        return is_string($validated)
            ? $validated
            : '';
    }

    /**
     * Produce a one-way representation of the client IP rather than storing the
     * raw address with the submission.
     */
    private function hashedClientIp(): string
    {
        return hash(
            'sha256',
            $this->request->ip()
        );
    }

    /**
     * Clean a request value consistently using the application's existing
     * security utility.
     */
    private function cleanInput(
        string $key,
        string $default,
        int $maxLength
    ): string {
        return Security::cleanText(
            $this->request->input($key, $default),
            $maxLength
        );
    }

    /**
     * Return a repository item or the existing API 404 response.
     */
    private function respondWithItem(?array $item): never
    {
        if ($item === null || $item === []) {
            $this->error(
                'Item not found.',
                404
            );
        }

        Response::json([
            'ok' => true,
            'data' => $item,
        ]);
    }

    /**
     * Keep API error responses consistent.
     */
    private function error(string $message, int $status): never
    {
        Response::json([
            'ok' => false,
            'message' => $message,
        ], $status);
    }
}
