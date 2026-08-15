<?php
declare(strict_types=1);

namespace App\Models;

use RuntimeException;

final class SiteRepository
{
    private const DATA_FILE = '/data/site.php';
    private const SEARCH_RESULT_LIMIT = 12;

    /**
     * Public groups currently included in site search.
     *
     * Keeping this explicit prevents unrelated future data structures in
     * data/site.php from accidentally becoming searchable.
     */
    private const SEARCH_GROUPS = [
        'categories',
        'products',
        'organizations',
        'team',
        'faqs',
    ];

    private array $data;

    public function __construct()
    {
        $this->data = $this->loadData();
    }

    /**
     * Return the complete public site dataset.
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * Find one item inside a top-level data group.
     */
    public function find(
        string $group,
        string $id,
        string $key = 'id'
    ): ?array {
        $group = trim($group);
        $key = trim($key);
        $id = trim($id);

        if (
            $group === ''
            || $key === ''
            || $id === ''
        ) {
            return null;
        }

        $items = $this->groupItems($group);

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            if ((string) ($item[$key] ?? '') === $id) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Resolve a team member by canonical slug or any configured alias.
     */
    public function findTeamBySlug(string $value): ?array
    {
        $slug = $this->normalizeSlug($value);

        if ($slug === '') {
            return null;
        }

        foreach ($this->groupItems('team') as $member) {
            if (!is_array($member)) {
                continue;
            }

            $candidates = [
                (string) ($member['slug'] ?? ''),
            ];

            $aliases = $member['aliases'] ?? [];

            if (is_array($aliases)) {
                foreach ($aliases as $alias) {
                    if (
                        is_string($alias)
                        || is_int($alias)
                        || is_float($alias)
                    ) {
                        $candidates[] = (string) $alias;
                    }
                }
            }

            foreach ($candidates as $candidate) {
                if (
                    $candidate !== ''
                    && $this->normalizeSlug($candidate) === $slug
                ) {
                    return $member;
                }
            }
        }

        return null;
    }

    /**
     * Return all team members or filter by department.
     */
    public function team(string $department = 'all'): array
    {
        $team = array_values(
            array_filter(
                $this->groupItems('team'),
                static fn(mixed $member): bool => is_array($member)
            )
        );

        $department = $this->normalizeComparisonText($department);

        if ($department === '' || $department === 'all') {
            return $team;
        }

        return array_values(
            array_filter(
                $team,
                function (array $member) use ($department): bool {
                    $departmentName = $this->normalizeComparisonText(
                        (string) ($member['department'] ?? '')
                    );

                    $departmentShort = $this->normalizeComparisonText(
                        (string) ($member['dept'] ?? '')
                    );

                    return $departmentName === $department
                        || $departmentShort === $department;
                }
            )
        );
    }

    /**
     * Return the canonical product catalogue with optional public filters.
     */
    public function products(string $term = '', string $category = ''): array
    {
        $products = array_values(array_filter(
            $this->groupItems('products'),
            static fn (mixed $item): bool => is_array($item)
        ));

        $term = $this->normalizeSearchText($term);
        $category = $this->normalizeSearchText($category);

        $products = array_values(array_filter(
            $products,
            function (array $product) use ($term, $category): bool {
                $matchesCategory = $category === ''
                    || $category === 'all'
                    || $this->normalizeSearchText((string) ($product['category'] ?? '')) === $category;

                $matchesTerm = $term === ''
                    || $this->itemMatchesSearch($product, $term);

                return $matchesCategory && $matchesTerm;
            }
        ));

        usort($products, static fn (array $a, array $b): int =>
            ((int) ($a['sort_order'] ?? 999)) <=> ((int) ($b['sort_order'] ?? 999))
        );

        return $products;
    }

    public function findProductBySlug(string $slug): ?array
    {
        return $this->find('products', $this->normalizeSlug($slug), 'slug');
    }

    public function productCategories(): array
    {
        $categories = [];

        foreach ($this->groupItems('products') as $product) {
            if (!is_array($product)) {
                continue;
            }

            $category = trim((string) ($product['category'] ?? ''));

            if ($category !== '') {
                $categories[] = $category;
            }
        }

        $categories = array_values(array_unique($categories));
        sort($categories, SORT_NATURAL | SORT_FLAG_CASE);

        return $categories;
    }

    /**
     * Search selected public website content.
     *
     * The public return structure is intentionally preserved:
     *
     * [
     *     'type' => ...,
     *     'title' => ...,
     *     'summary' => ...,
     *     'url' => ...,
     * ]
     */
    public function search(string $term): array
    {
        $term = $this->normalizeSearchText($term);

        if ($term === '') {
            return [];
        }

        $results = [];

        foreach (self::SEARCH_GROUPS as $group) {
            foreach ($this->groupItems($group) as $item) {
                if (!is_array($item)) {
                    continue;
                }

                if (!$this->itemMatchesSearch($item, $term)) {
                    continue;
                }

                $results[] = $this->makeSearchResult(
                    $group,
                    $item
                );

                if (count($results) >= self::SEARCH_RESULT_LIMIT) {
                    return $results;
                }
            }
        }

        return $results;
    }

    /**
     * Load and validate the site's PHP data file.
     */
    private function loadData(): array
    {
        $file = ROOT_PATH . self::DATA_FILE;

        if (!is_file($file) || !is_readable($file)) {
            throw new RuntimeException(
                'Site data file is unavailable.'
            );
        }

        $data = require $file;

        if (!is_array($data)) {
            throw new RuntimeException(
                'Site data file must return an array.'
            );
        }

        return $data;
    }

    /**
     * Return a top-level group as an array.
     */
    private function groupItems(string $group): array
    {
        $items = $this->data[$group] ?? [];

        return is_array($items)
            ? $items
            : [];
    }

    /**
     * Determine whether an item contains the requested public search term.
     */
    private function itemMatchesSearch(
        array $item,
        string $term
    ): bool {
        $searchable = [];

        foreach ($item as $key => $value) {
            /*
             * Skip fields that should not influence public search relevance.
             */
            if (
                in_array(
                    (string) $key,
                    [
                        'image',
                        'photo',
                        'avatar',
                        'url',
                        'href',
                        'slug',
                        'id',
                        'aliases',
                    ],
                    true
                )
            ) {
                continue;
            }

            $this->collectSearchableValues(
                $value,
                $searchable
            );
        }

        foreach ($searchable as $value) {
            if (
                str_contains(
                    $this->normalizeSearchText($value),
                    $term
                )
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Recursively collect scalar values from a public content record.
     *
     * @param list<string> $values
     */
    private function collectSearchableValues(
        mixed $value,
        array &$values
    ): void {
        if (
            is_string($value)
            || is_int($value)
            || is_float($value)
            || is_bool($value)
        ) {
            $values[] = (string) $value;
            return;
        }

        if (!is_array($value)) {
            return;
        }

        foreach ($value as $nested) {
            $this->collectSearchableValues(
                $nested,
                $values
            );
        }
    }

    /**
     * Build one public search-result object.
     */
    private function makeSearchResult(
        string $group,
        array $item
    ): array {
        $title = $item['title']
            ?? $item['name']
            ?? $item['question']
            ?? 'Result';

        $summary = $item['summary']
            ?? $item['desc']
            ?? $item['description']
            ?? $item['answer']
            ?? '';

        return [
            'type' => $group,
            'title' => $this->scalarString($title, 'Result'),
            'summary' => $this->scalarString($summary),
            'url' => $this->searchResultUrl(
                $group,
                $item
            ),
        ];
    }

    /**
     * Generate the public destination for a search result.
     */
    private function searchResultUrl(
        string $group,
        array $item
    ): string {
        $slug = $this->scalarString(
            $item['slug'] ?? ''
        );

        if ($group === 'products' && $slug !== '') {
            return url('/products/' . rawurlencode($slug));
        }

        if ($group === 'team' && $slug !== '') {
            return url(
                '/team/' . rawurlencode($slug)
            );
        }

        $anchor = match ($group) {
            'faqs' => 'faq',
            'organizations' => 'organizations',
            'categories' => 'categories',
            'products' => 'products',
            default => $group,
        };

        return url('/#' . $anchor);
    }

    /**
     * Normalize team-profile slugs and aliases.
     */
    private function normalizeSlug(string $value): string
    {
        $value = rawurldecode(
            trim($value)
        );

        $value = $this->lower($value);

        $value = preg_replace(
            "/['’]/u",
            '',
            $value
        ) ?? $value;

        $value = str_replace(
            '&',
            ' and ',
            $value
        );

        $value = preg_replace(
            '/[^a-z0-9]+/u',
            '-',
            $value
        ) ?? $value;

        return trim(
            $value,
            '-'
        );
    }

    /**
     * Normalize values used for department comparisons.
     */
    private function normalizeComparisonText(string $value): string
    {
        $value = $this->lower(
            trim($value)
        );

        $value = preg_replace(
            '/\s+/u',
            ' ',
            $value
        ) ?? $value;

        return trim($value);
    }

    /**
     * Normalize text used for public search matching.
     */
    private function normalizeSearchText(string $value): string
    {
        $value = $this->lower(
            trim($value)
        );

        $value = preg_replace(
            '/\s+/u',
            ' ',
            $value
        ) ?? $value;

        return trim($value);
    }

    /**
     * Safely turn public content values into display strings.
     */
    private function scalarString(
        mixed $value,
        string $default = ''
    ): string {
        if (
            is_string($value)
            || is_int($value)
            || is_float($value)
            || is_bool($value)
        ) {
            return trim((string) $value);
        }

        return $default;
    }

    /**
     * Unicode-aware lowercase helper.
     */
    private function lower(string $value): string
    {
        return function_exists('mb_strtolower')
            ? mb_strtolower($value, 'UTF-8')
            : strtolower($value);
    }
}
