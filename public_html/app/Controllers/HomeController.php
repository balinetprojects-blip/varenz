<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Security;
use App\Models\SiteRepository;

final class HomeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Page Metadata
    |--------------------------------------------------------------------------
    |
    | Keep descriptive metadata here, but never hard-code a deployment
    | hostname into the controller.
    |
    | The same application files must operate unchanged on:
    |
    | - Hostinger temporary/staging domains
    | - varenzsupplies.com
    | - local development
    | - future approved deployment domains
    |
    */

    private const PAGE_TITLE =
        'Varenz Supplies Ltd | Specialised Medical Supplies Uganda';

    private const META_DESCRIPTION =
        'Specialised medical supplies and procurement support for hospitals, clinics and healthcare programmes in Uganda. Request a quotation from Varenz Supplies Ltd.';

    private const TEAM_SLUG_MAX_LENGTH = 120;


    /*
    |--------------------------------------------------------------------------
    | Homepage
    |--------------------------------------------------------------------------
    */

    public function index(): never
    {
        $repository =
            new SiteRepository();

        /*
         * Load the public site dataset once.
         *
         * This should contain:
         *
         * - hero
         * - challenges
         * - categories
         * - featured
         * - procurement
         * - organizations
         * - why
         * - team
         * - faqs
         *
         * PHP-rendered homepage content must remain available even if
         * frontend JavaScript fails.
         */
        $site =
            $repository->all();

        $requestedTeam =
            $this->requestedTeamSlug();

        $member =
            $requestedTeam !== ''
                ? $repository->findTeamBySlug(
                    $requestedTeam
                )
                : null;

        /*
         * pageUrl deliberately uses the same-origin URL helper.
         *
         * The layout is responsible for converting this into an absolute
         * canonical URL when required for metadata.
         *
         * Therefore the controller does NOT know or care which hostname
         * currently serves the application.
         */
        $this->view(
            'home/index',
            [
                'pageTitle' =>
                    self::PAGE_TITLE,

                'metaDescription' =>
                    self::META_DESCRIPTION,

                'pageUrl' =>
                    url('/'),

                'site' =>
                    $site,

                'csrf' =>
                    Security::csrfToken(),

                'initialTeamSlug' =>
                    is_array($member)
                        ? (
                            isset($member['slug'])
                            && is_string($member['slug'])
                                ? $member['slug']
                                : null
                        )
                        : null,

                'pageStyles' => [
                    'css/effects.css',
                ],

                'pageScripts' => [
                    'js/varenz-effects.js',
                ],
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Optional Team Deep Link
    |--------------------------------------------------------------------------
    |
    | Historical public profile links may use either:
    |
    | ?team=member-slug
    |
    | or:
    |
    | ?profile=member-slug
    |
    | Both are deliberately preserved.
    |
    */

    private function requestedTeamSlug(): string
    {
        $requested =
            $this->request->input(
                'team',
                ''
            );

        if (
            $requested === ''
            || $requested === null
        ) {
            $requested =
                $this->request->input(
                    'profile',
                    ''
                );
        }

        return trim(
            Security::cleanText(
                (string) $requested,
                self::TEAM_SLUG_MAX_LENGTH
            )
        );
    }
}
