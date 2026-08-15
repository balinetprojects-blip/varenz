<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Security;
use App\Models\SiteRepository;

final class PageController extends Controller
{
    /**
     * @var array<string, array{title:string,description:string,eyebrow:string,heading:string,intro:string,image:string}>
     */
    private const PAGES = [
        'about' => [
            'title' => 'About Varenz Supplies Ltd | Healthcare Supply Support',
            'description' => 'Learn about Varenz Supplies Ltd, our healthcare supply focus, operating approach and organisations supported across Uganda.',
            'eyebrow' => 'ABOUT VARENZ',
            'heading' => 'A dependable healthcare supply partner built around clarity.',
            'intro' => 'Varenz Supplies Ltd combines selected medical products, structured requirement review, clear documentation and responsive coordination for healthcare organisations.',
            'image' => 'images/hero/final/scene-01-overview.webp',
        ],
        'procurement' => [
            'title' => 'Procurement Support | Varenz Supplies Ltd',
            'description' => 'Follow the Varenz procurement journey from requirement submission and review to quotation, delivery and follow-up.',
            'eyebrow' => 'PROCUREMENT SUPPORT',
            'heading' => 'From an unclear requirement to a coordinated supply response.',
            'intro' => 'A seven-stage procurement pathway keeps product references, quantities, documentation, approvals and delivery expectations visible at every step.',
            'image' => 'images/procurement/requirement-review-20260731.webp',
        ],
        'quality-compliance' => [
            'title' => 'Quality and Compliance | Varenz Supplies Ltd',
            'description' => 'Review the documentation-led, quality-focused and compliance-aware supply approach used by Varenz Supplies Ltd.',
            'eyebrow' => 'QUALITY & COMPLIANCE',
            'heading' => 'Clear documentation supports safer procurement decisions.',
            'intro' => 'Varenz supports product identification, formal quotations, available product information, requirement traceability and dependable communication without making unsupported regulatory claims.',
            'image' => 'images/about/about-verified-documentation.webp',
        ],
        'partners' => [
            'title' => 'Partners and Organisations | Varenz Supplies Ltd',
            'description' => 'Explore the institutions, organisations and healthcare teams connected to Varenz supply support.',
            'eyebrow' => 'PARTNERS & ORGANISATIONS',
            'heading' => 'Professional relationships that strengthen supply confidence.',
            'intro' => 'Varenz works across healthcare, regulatory, customs, technology and institutional procurement environments with clear roles and professional boundaries.',
            'image' => 'images/organizations/hospitals-20260731.webp',
        ],
        'resources' => [
            'title' => 'Resources | Varenz Supplies Ltd',
            'description' => 'Download the Varenz company profile, capability presentation and healthcare supply brochure.',
            'eyebrow' => 'VARENZ RESOURCE CENTRE',
            'heading' => 'Company and procurement information in one trusted place.',
            'intro' => 'Use the approved company resources to review Varenz capabilities, selected products, supply approach and contact information before preparing a request.',
            'image' => 'images/resources/company-profile-cover.webp',
        ],
        'faq' => [
            'title' => 'Frequently Asked Questions | Varenz Supplies Ltd',
            'description' => 'Answers about Varenz quotations, product requests, procurement documents, institutional orders and support.',
            'eyebrow' => 'FREQUENTLY ASKED QUESTIONS',
            'heading' => 'Quick answers before you submit a requirement.',
            'intro' => 'Review the most common questions about quotations, product references, uploaded procurement lists, institutional orders and customer support.',
            'image' => 'images/operations/operations-support-agent.webp',
        ],
        'contact' => [
            'title' => 'Contact Varenz Supplies Ltd | Kampala, Uganda',
            'description' => 'Contact Varenz Supplies Ltd for medical product enquiries, quotations, orders, partnerships and procurement support.',
            'eyebrow' => 'CONTACT VARENZ',
            'heading' => 'Choose the fastest route for your requirement.',
            'intro' => 'Send product details, quantities, references and timelines through the secure Request Centre, or contact the Varenz team directly for urgent clarification.',
            'image' => 'images/hero/final/scene-06-nda-ura.webp',
        ],
    ];

    public function about(): never
    {
        $this->render('about');
    }

    public function procurement(): never
    {
        $this->render('procurement');
    }

    public function qualityCompliance(): never
    {
        $this->render('quality-compliance');
    }

    public function partners(): never
    {
        $this->render('partners');
    }

    public function resources(): never
    {
        $this->render('resources');
    }

    public function faq(): never
    {
        $this->render('faq');
    }

    public function contact(): never
    {
        $this->render('contact');
    }

    private function render(string $pageKey): never
    {
        $page = self::PAGES[$pageKey];
        $repository = new SiteRepository();

        $this->view('pages/show', [
            'pageTitle' => $page['title'],
            'metaDescription' => $page['description'],
            'pageUrl' => url('/' . $pageKey),
            'pageKey' => $pageKey,
            'page' => $page,
            'site' => $repository->all(),
            'csrf' => Security::csrfToken(),
            'includeAppJs' => false,
            'pageStyles' => ['css/pages.css', 'css/effects.css'],
            'pageScripts' => ['js/pages.js', 'js/varenz-effects.js'],
        ]);
    }
}
