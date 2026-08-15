<?php
declare(strict_types=1);

$contactData = config('contact', []);
$contactData = is_array($contactData) ? $contactData : [];
?>
<footer class="vsl-page-footer">
    <div class="vsl-page-footer__brand">
        <img src="<?= e(asset('images/logo/varenz-word-logo-clean.png')) ?>" alt="Varenz Supplies Ltd" width="320" height="96">
        <p>Specialised medical supply support with clear documentation and responsive procurement coordination.</p>
        <strong>Reliable Supply. Better Care.</strong>
    </div>
    <div>
        <h2>Explore</h2>
        <a href="<?= e(url('/about')) ?>">About Varenz</a>
        <a href="<?= e(url('/products')) ?>">Products</a>
        <a href="<?= e(url('/procurement')) ?>">Procurement</a>
        <a href="<?= e(url('/partners')) ?>">Partners</a>
    </div>
    <div>
        <h2>Confidence</h2>
        <a href="<?= e(url('/quality-compliance')) ?>">Quality &amp; Compliance</a>
        <a href="<?= e(url('/resources')) ?>">Resources</a>
        <a href="<?= e(url('/faq')) ?>">FAQs</a>
        <a href="<?= e(url('/contact')) ?>">Contact</a>
    </div>
    <div>
        <h2>Contact</h2>
        <a href="mailto:<?= e((string) ($contactData['email'] ?? 'info@varenzsupplies.com')) ?>"><?= e((string) ($contactData['email'] ?? 'info@varenzsupplies.com')) ?></a>
        <a href="tel:+256701165527">0701165527</a>
        <a href="tel:+256730850411">0730850411</a>
        <span><?= e((string) ($contactData['location'] ?? 'Komamboga, Kampala, Uganda')) ?></span>
    </div>
    <small>© <?= (int) date('Y') ?> Varenz Supplies Ltd. All rights reserved.</small>
</footer>
