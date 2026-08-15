<?php
declare(strict_types=1);

namespace App\Core;

use RuntimeException;
use Throwable;

abstract class Controller
{
    private const VIEW_ROOT = '/app/Views/';
    private const LAYOUT_ROOT = '/app/Views/layouts/';
    private const TEMPLATE_EXTENSION = '.php';

    public function __construct(
        protected readonly Request $request
    ) {}


    /**
     * Render a view inside an application layout and send the final HTML
     * response.
     *
     * Existing controller usage is intentionally preserved:
     *
     * $this->view('home/index', [...]);
     * $this->view('team/profile', [...], 'main');
     *
     * IMPORTANT:
     *
     * PHP method scopes are isolated.
     *
     * Variables extracted inside this method are NOT automatically visible
     * inside captureTemplate().
     *
     * Therefore controller data must be explicitly passed into both:
     *
     * 1. The requested page template.
     * 2. The surrounding layout template.
     *
     * This preserves variables such as:
     *
     * - $site
     * - $pageTitle
     * - $metaDescription
     * - $pageUrl
     * - $csrf
     * - $initialTeamSlug
     *
     * and any future controller-provided template variables.
     */
    protected function view(
        string $view,
        array $data = [],
        string $layout = 'main'
    ): never {
        $viewFile = $this->resolveViewFile(
            $view
        );

        $layoutFile = $this->resolveLayoutFile(
            $layout
        );

        if (
            !is_file($viewFile)
            || !is_readable($viewFile)
        ) {
            $this->renderingFailure(
                'View not found.'
            );
        }

        if (
            !is_file($layoutFile)
            || !is_readable($layoutFile)
        ) {
            $this->renderingFailure(
                'Layout not found.'
            );
        }

        try {
            /*
             * Pass controller variables DIRECTLY into the page template.
             *
             * This is the critical correction.
             */
            $content = $this->captureTemplate(
                $viewFile,
                $data
            );

            /*
             * The layout also requires the original controller variables.
             *
             * Examples:
             *
             * $pageTitle
             * $metaDescription
             * $pageUrl
             * $site
             * $csrf
             * $initialTeamSlug
             *
             * Add rendered page content last so controller data cannot
             * accidentally replace the layout's $content variable.
             */
            $layoutData = array_merge(
                $data,
                [
                    'content' => $content,
                ]
            );

            $html = $this->captureTemplate(
                $layoutFile,
                $layoutData
            );
        } catch (Throwable $exception) {
            /*
             * Do not expose filesystem paths, stack traces, template source,
             * configuration details or exception internals to visitors.
             *
             * The original exception may later be connected to the approved
             * application logging layer, but the public response remains
             * deliberately generic.
             */
            $this->renderingFailure(
                'Unable to render this page.'
            );
        }

        Response::html(
            $html
        );
    }


    /**
     * Resolve a normal application view such as:
     *
     * home/index
     * team/profile
     * documents/archive
     */
    private function resolveViewFile(
        string $view
    ): string {
        $view = $this->normalizeTemplateIdentifier(
            $view
        );

        if ($view === '') {
            $this->renderingFailure(
                'View not found.'
            );
        }

        return ROOT_PATH
            . self::VIEW_ROOT
            . $view
            . self::TEMPLATE_EXTENSION;
    }


    /**
     * Resolve an application layout.
     */
    private function resolveLayoutFile(
        string $layout
    ): string {
        $layout = $this->normalizeTemplateIdentifier(
            $layout
        );

        if ($layout === '') {
            $this->renderingFailure(
                'Layout not found.'
            );
        }

        return ROOT_PATH
            . self::LAYOUT_ROOT
            . $layout
            . self::TEMPLATE_EXTENSION;
    }


    /**
     * Restrict template identifiers to application-relative PHP template
     * paths while preserving nested view structures.
     *
     * Valid examples:
     *
     * home/index
     * team/profile
     * admin/users/index
     * main
     *
     * Rejected examples include:
     *
     * ../config/app
     * /etc/passwd
     * home/../../config
     * null-byte paths
     */
    private function normalizeTemplateIdentifier(
        string $identifier
    ): string {
        $identifier = trim(
            $identifier
        );

        if ($identifier === '') {
            return '';
        }

        $identifier = str_replace(
            '\\',
            '/',
            $identifier
        );

        $identifier = trim(
            $identifier,
            '/'
        );

        if (
            $identifier === ''
            || str_contains(
                $identifier,
                '..'
            )
            || str_contains(
                $identifier,
                "\0"
            )
        ) {
            return '';
        }

        if (
            preg_match(
                '/^[A-Za-z0-9_-]+(?:\/[A-Za-z0-9_-]+)*$/',
                $identifier
            ) !== 1
        ) {
            return '';
        }

        return $identifier;
    }


    /**
     * Render a PHP template into a string.
     *
     * Template variables are deliberately extracted inside THIS method,
     * because this is the exact scope in which require executes.
     *
     * Example:
     *
     * captureTemplate(
     *     '/app/Views/home/index.php',
     *     [
     *         'site' => $site,
     *         'csrf' => $csrf,
     *     ]
     * );
     *
     * makes the following variables available inside the required template:
     *
     * $site
     * $csrf
     *
     * EXTR_SKIP prevents controller-provided keys from replacing internal
     * renderer variables such as $templateFile, $variables or $bufferLevel.
     */
    private function captureTemplate(
        string $templateFile,
        array $variables = []
    ): string {
        if ($variables !== []) {
            extract(
                $variables,
                EXTR_SKIP
            );
        }

        $bufferLevel = ob_get_level();

        ob_start();

        try {
            require $templateFile;

            $output = ob_get_clean();

            if ($output === false) {
                throw new RuntimeException(
                    'Unable to read rendered output.'
                );
            }

            return (string) $output;
        } catch (Throwable $exception) {
            /*
             * Clean only buffers created after entering this renderer.
             *
             * This avoids corrupting an outer application/server buffer.
             */
            while (
                ob_get_level()
                > $bufferLevel
            ) {
                ob_end_clean();
            }

            throw $exception;
        }
    }


    /**
     * Keep public rendering failures generic and safe.
     */
    private function renderingFailure(
        string $message
    ): never {
        Response::html(
            $message,
            500
        );
    }
}