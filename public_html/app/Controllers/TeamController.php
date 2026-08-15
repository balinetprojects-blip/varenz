<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Core\Security;
use App\Models\SiteRepository;

final class TeamController extends Controller
{
    private const SITE_NAME = 'Varenz Supplies Ltd';
    private const TEAM_BASE_URL = 'https://varenzsupplies.com/team/';
    private const SLUG_MAX_LENGTH = 160;
    private const META_DESCRIPTION_MAX_LENGTH = 320;

    public function show(string $slug): never
    {
        $repository = new SiteRepository();

        $slug = $this->cleanSlug($slug);

        $member = $slug !== ''
            ? $repository->findTeamBySlug($slug)
            : null;

        if (!$member) {
            $this->profileNotFound();
        }

        $memberSlug = Security::cleanText(
            (string) ($member['slug'] ?? $slug),
            self::SLUG_MAX_LENGTH
        );

        $memberName = Security::cleanText(
            (string) ($member['name'] ?? 'Team Member'),
            180
        );

        $summary = Security::cleanText(
            (string) ($member['summary'] ?? ''),
            self::META_DESCRIPTION_MAX_LENGTH
        );

        $this->view('team/profile', [
            'pageTitle' => $memberName . ' — ' . self::SITE_NAME,
            'metaDescription' => $summary,
            'pageUrl' => self::TEAM_BASE_URL . rawurlencode($memberSlug),
            'member' => $member,
            'site' => $repository->all(),
            'csrf' => Security::csrfToken(),

            /*
             * Preserve the existing profile-page behaviour.
             *
             * The team profile currently opts out of the global app.js bundle.
             * Do not change this until the profile view and its JavaScript
             * dependencies have been audited together.
             */
            'includeAppJs' => false,
        ]);
    }

    /**
     * Sanitize the route slug while preserving the repository's existing
     * slug-based lookup behaviour.
     */
    private function cleanSlug(string $slug): string
    {
        return trim(
            Security::cleanText(
                $slug,
                self::SLUG_MAX_LENGTH
            )
        );
    }

    /**
     * Return a small, standards-compliant fallback page when no matching
     * public team profile exists.
     */
    private function profileNotFound(): never
    {
        Response::html(
            <<<'HTML'
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, follow">
    <title>Profile not found — Varenz Supplies Ltd</title>
</head>
<body>
    <main>
        <h1>Team profile not found</h1>
        <p>The team profile you requested could not be found.</p>
        <p><a href="/">Return to Varenz Supplies Ltd</a></p>
    </main>
</body>
</html>
HTML,
            404
        );
    }
}