<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Core\Security;
use App\Models\SiteRepository;

final class ProductController extends Controller
{
    private const LIMIT_TERM = 120;
    private const LIMIT_CATEGORY = 80;

    public function index(): never
    {
        $repository = new SiteRepository();
        $term = Security::cleanText((string) $this->request->input('q', ''), self::LIMIT_TERM);
        $category = Security::cleanText((string) $this->request->input('category', ''), self::LIMIT_CATEGORY);

        $this->view('products/index', [
            'pageTitle' => 'Medical Product Catalogue | Varenz Supplies Ltd',
            'metaDescription' => 'Explore selected Varenz medical products and prepare a secure procurement request for specifications, documentation and availability.',
            'pageUrl' => url('/products'),
            'site' => $repository->all(),
            'products' => $repository->products($term, $category),
            'categories' => $repository->productCategories(),
            'activeTerm' => $term,
            'activeCategory' => $category,
            'pageKey' => 'products',
            'includeAppJs' => false,
            'pageStyles' => ['css/products.css', 'css/pages.css', 'css/effects.css'],
            'pageScripts' => ['js/pages.js', 'js/products.js', 'js/varenz-effects.js'],
        ]);
    }

    public function show(string $slug): never
    {
        $repository = new SiteRepository();
        $product = $repository->findProductBySlug($slug);

        if ($product === null) {
            Response::html('<!doctype html><html lang="en"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Product not found</title><main><h1>Product not found</h1><p><a href="' . e(url('/products')) . '">Return to products</a></p></main></html>', 404);
        }

        $title = (string) ($product['title'] ?? 'Medical Product');
        $description = (string) ($product['short_description'] ?? 'Selected medical product enquiry support from Varenz Supplies Ltd.');

        $related = array_values(array_filter(
            $repository->products('', (string) ($product['category'] ?? '')),
            static fn (array $item): bool => ($item['slug'] ?? '') !== ($product['slug'] ?? '')
        ));

        $this->view('products/show', [
            'pageTitle' => $title . ' | Varenz Supplies Ltd',
            'metaDescription' => $description,
            'pageUrl' => url('/products/' . rawurlencode((string) ($product['slug'] ?? ''))),
            'site' => $repository->all(),
            'product' => $product,
            'relatedProducts' => array_slice($related, 0, 3),
            'pageKey' => 'products',
            'includeAppJs' => false,
            'pageStyles' => ['css/products.css', 'css/pages.css', 'css/effects.css'],
            'pageScripts' => ['js/pages.js', 'js/products.js', 'js/varenz-effects.js'],
        ]);
    }
}
