<?php
declare(strict_types=1);

$pageKey = trim((string) ($pageKey ?? 'about'));
$page = isset($page) && is_array($page) ? $page : [];
$site = isset($site) && is_array($site) ? $site : [];
$why = isset($site['why']) && is_array($site['why']) ? $site['why'] : [];
$procurement = isset($site['procurement']) && is_array($site['procurement']) ? $site['procurement'] : [];
$organizations = isset($site['organizations']) && is_array($site['organizations']) ? $site['organizations'] : [];
$partners = isset($site['partners']) && is_array($site['partners']) ? $site['partners'] : [];
$faqs = isset($site['faqs']) && is_array($site['faqs']) ? $site['faqs'] : [];
$contact = config('contact', []);
$contact = is_array($contact) ? $contact : [];

require __DIR__ . '/../shared/site-header.php';
?>
<main class="vsl-page-main" id="mainContent" data-vsl-page="<?= e($pageKey) ?>">
    <section class="vsl-page-hero">
        <div class="vsl-effect-layer" data-vsl-effect="hero-gradient" aria-hidden="true"></div>
        <div class="vsl-page-hero__copy">
            <span class="vsl-page-eyebrow"><?= e((string) ($page['eyebrow'] ?? 'VARENZ SUPPLIES LTD')) ?></span>
            <h1><?= e((string) ($page['heading'] ?? 'Reliable Supply. Better Care.')) ?></h1>
            <p><?= e((string) ($page['intro'] ?? 'Specialised medical supply support for healthcare organisations.')) ?></p>
            <div class="vsl-page-hero__actions">
                <a class="vsl-page-primary" href="<?= e(url('/')) ?>?intent=quotation#cta">Request a Quote</a>
                <a class="vsl-page-secondary" href="<?= e(url('/products')) ?>">Explore Products</a>
            </div>
            <div class="vsl-page-trust-strip" role="list" aria-label="Varenz service principles">
                <span role="listitem">Quality-focused sourcing</span>
                <span role="listitem">Clear documentation</span>
                <span role="listitem">Responsive coordination</span>
            </div>
        </div>
        <div class="vsl-page-hero__visual">
            <img src="<?= e(asset((string) ($page['image'] ?? 'images/hero/final/scene-01-overview.webp'))) ?>" alt="" width="1200" height="800" fetchpriority="high">
            <div class="vsl-liquid-logo" data-vsl-effect="liquid-logo" aria-hidden="true"></div>
        </div>
    </section>

    <?php if ($pageKey === 'about'): ?>
        <section class="vsl-page-section vsl-page-section--intro" id="approach">
            <div class="vsl-page-section__heading">
                <span>OUR OPERATING APPROACH</span>
                <h2>Clinical clarity expressed through dependable supply support.</h2>
                <p>The Varenz model connects product discovery, specification review, formal quotation, documentation follow-up and delivery coordination in one professional relationship.</p>
            </div>
            <div class="vsl-neumo-grid vsl-neumo-grid--three">
                <article class="vsl-neumo-card"><i class="fa-solid fa-boxes-stacked" aria-hidden="true"></i><h3>What We Do</h3><p>Support selected medical product enquiries, institutional lists and reference-sensitive requirements.</p></article>
                <article class="vsl-neumo-card"><i class="fa-solid fa-hospital" aria-hidden="true"></i><h3>Who We Support</h3><p>Hospitals, clinics, pharmacies, imaging centres, NGOs and research programmes.</p></article>
                <article class="vsl-neumo-card"><i class="fa-solid fa-shield-heart" aria-hidden="true"></i><h3>Why It Matters</h3><p>Clearer requirements reduce avoidable delays and improve procurement confidence.</p></article>
            </div>
        </section>

        <section class="vsl-page-section vsl-page-section--deep">
            <div class="vsl-page-section__heading"><span>WHY VARENZ</span><h2>Six practical reasons organisations choose Varenz.</h2></div>
            <div class="vsl-neumo-grid vsl-neumo-grid--three">
                <?php foreach ($why as $index => $item): ?>
                    <article class="vsl-deep-card"><b><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></b><h3><?= e((string) ($item['title'] ?? 'Varenz advantage')) ?></h3><p><?= e((string) ($item['desc'] ?? '')) ?></p></article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="vsl-page-section" id="organizations">
            <div class="vsl-page-section__heading"><span>ORGANISATIONS SUPPORTED</span><h2>Designed around real healthcare procurement environments.</h2></div>
            <div class="vsl-organisation-grid">
                <?php foreach ($organizations as $organization): ?>
                    <article class="vsl-image-card">
                        <img src="<?= e(asset((string) ($organization['image'] ?? ''))) ?>" alt="" width="720" height="520" loading="lazy">
                        <div><h3><?= e((string) ($organization['name'] ?? 'Healthcare organisation')) ?></h3><p><?= e((string) ($organization['summary'] ?? '')) ?></p></div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

    <?php elseif ($pageKey === 'procurement'): ?>
        <section class="vsl-page-section">
            <div class="vsl-page-section__heading"><span>THE SEVEN-STAGE JOURNEY</span><h2>Every requirement has a visible next step.</h2><p>The journey is designed for progressive clarification. Customers can begin with the information they already have and improve the request during review.</p></div>
            <ol class="vsl-process-grid">
                <?php foreach ($procurement as $index => $step): ?>
                    <li class="vsl-process-card">
                        <img src="<?= e(asset((string) ($step['image'] ?? ''))) ?>" alt="" width="720" height="480" loading="lazy">
                        <div><b><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></b><h2><?= e((string) ($step['step'] ?? 'Procurement step')) ?></h2><p><?= e((string) ($step['detail'] ?? '')) ?></p></div>
                    </li>
                <?php endforeach; ?>
            </ol>
        </section>
        <section class="vsl-page-section vsl-page-section--deep vsl-page-conversion">
            <div><span>START WITH WHAT YOU KNOW</span><h2>Send the product name, reference, image or procurement list.</h2><p>The secure Request Centre helps you add specification, quantity, timing and delivery information before submission.</p></div>
            <a class="vsl-page-primary" href="<?= e(url('/')) ?>?intent=quotation#cta">Open Request Centre</a>
        </section>

    <?php elseif ($pageKey === 'quality-compliance'): ?>
        <section class="vsl-page-section">
            <div class="vsl-page-section__heading"><span>DOCUMENTATION-LED SUPPORT</span><h2>Confidence comes from visible, reviewable information.</h2><p>Varenz separates verified product or order information from assumptions and confirms details at the appropriate stage of the procurement journey.</p></div>
            <div class="vsl-neumo-grid vsl-neumo-grid--three">
                <article class="vsl-neumo-card"><i class="fa-solid fa-file-circle-check" aria-hidden="true"></i><h3>Requirement Traceability</h3><p>Product references, quantities, customer details and timelines remain connected to the request.</p></article>
                <article class="vsl-neumo-card"><i class="fa-solid fa-clipboard-check" aria-hidden="true"></i><h3>Formal Quotations</h3><p>Clear commercial responses support customer review and internal procurement approval.</p></article>
                <article class="vsl-neumo-card"><i class="fa-solid fa-comments" aria-hidden="true"></i><h3>Communication Records</h3><p>Clarifications and order updates are communicated through defined contact channels.</p></article>
                <article class="vsl-neumo-card"><i class="fa-solid fa-box" aria-hidden="true"></i><h3>Product Identity</h3><p>Names, models, sizes, references and available documentation are reviewed before confirmation.</p></article>
                <article class="vsl-neumo-card"><i class="fa-solid fa-truck-medical" aria-hidden="true"></i><h3>Delivery Coordination</h3><p>Confirmed receiving details, location and contact expectations support smoother delivery.</p></article>
                <article class="vsl-neumo-card"><i class="fa-solid fa-rotate" aria-hidden="true"></i><h3>Follow-Up</h3><p>Customers can use their request reference to discuss support, feedback or recurring needs.</p></article>
            </div>
        </section>
        <section class="vsl-page-section vsl-compliance-note">
            <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
            <div><h2>Responsible public information</h2><p>This website provides procurement support information and does not replace manufacturer instructions, clinical judgement or applicable regulatory requirements. Product specifications and availability are confirmed during enquiry.</p></div>
        </section>

    <?php elseif ($pageKey === 'partners'): ?>
        <section class="vsl-page-section">
            <div class="vsl-page-section__heading"><span>PUBLIC PARTNER DIRECTORY</span><h2>Each relationship is presented with its correct identity.</h2><p>Logos remain visible by default; names and roles are available without requiring hover, preserving accessibility on touch devices.</p></div>
            <div class="vsl-partner-grid">
                <?php foreach ($partners as $partner): ?>
                    <article class="vsl-partner-card">
                        <div><img src="<?= e(asset((string) ($partner['logo'] ?? ''))) ?>" alt="<?= e((string) ($partner['name'] ?? 'Partner')) ?>" width="560" height="300" loading="lazy"></div>
                        <span><?= e((string) ($partner['label'] ?? 'Professional relationship')) ?></span>
                        <h2><?= e((string) ($partner['name'] ?? 'Partner')) ?></h2>
                        <?php if (trim((string) ($partner['note'] ?? '')) !== ''): ?><p><?= e((string) $partner['note']) ?></p><?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
        <section class="vsl-page-section vsl-page-section--deep">
            <div class="vsl-page-section__heading"><span>HEALTHCARE ORGANISATIONS</span><h2>Support adapted to different operating contexts.</h2></div>
            <div class="vsl-neumo-grid vsl-neumo-grid--three">
                <?php foreach ($organizations as $organization): ?>
                    <article class="vsl-deep-card"><h3><?= e((string) ($organization['name'] ?? 'Organisation')) ?></h3><p><?= e((string) ($organization['summary'] ?? '')) ?></p></article>
                <?php endforeach; ?>
            </div>
        </section>

    <?php elseif ($pageKey === 'resources'): ?>
        <section class="vsl-page-section">
            <div class="vsl-page-section__heading"><span>APPROVED DOWNLOADS</span><h2>Use the current Varenz corporate resource pack.</h2><p>Documents open in a new tab so procurement teams can review or download them without losing their place in the website.</p></div>
            <div class="vsl-resource-grid">
                <article class="vsl-resource-card"><img src="<?= e(asset('images/resources/company-profile-cover.webp')) ?>" alt="Varenz company profile cover" width="720" height="920" loading="lazy"><div><span>PDF</span><h2>Company Profile</h2><p>Company overview, capabilities, selected products and contact information.</p><a href="<?= e(asset('downloads/company-profile.pdf')) ?>" target="_blank" rel="noopener">Open Profile</a></div></article>
                <article class="vsl-resource-card"><img src="<?= e(asset('images/resources/product-portfolio-cover.webp')) ?>" alt="Varenz capability presentation cover" width="720" height="920" loading="lazy"><div><span>PPTX</span><h2>Capability Presentation</h2><p>A presentation-ready summary for meetings and internal review.</p><a href="<?= e(asset('downloads/varenz-capability-presentation.pptx')) ?>">Download Presentation</a></div></article>
                <article class="vsl-resource-card"><img src="<?= e(asset('images/resources/partnership-contact-cover.webp')) ?>" alt="Varenz healthcare supply brochure cover" width="720" height="920" loading="lazy"><div><span>PNG</span><h2>Healthcare Supply Brochure</h2><p>A visual introduction to Varenz supply support and product focus.</p><a href="<?= e(asset('downloads/varenz-healthcare-supply-brochure.png')) ?>" target="_blank" rel="noopener">Open Brochure</a></div></article>
            </div>
        </section>

    <?php elseif ($pageKey === 'faq'): ?>
        <section class="vsl-page-section vsl-faq-layout">
            <div class="vsl-page-section__heading"><span>SUPPORT KNOWLEDGE</span><h2>Answers for better prepared requests.</h2><p>Use these answers to prepare product, quantity, documentation and timing information before contacting the team.</p></div>
            <div class="vsl-page-faq" data-page-faq>
                <?php foreach ($faqs as $index => $faq): ?>
                    <?php $panelId = 'page-faq-panel-' . $index; ?>
                    <article class="vsl-page-faq__item<?= $index === 0 ? ' is-open' : '' ?>">
                        <button type="button" aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>" aria-controls="<?= e($panelId) ?>"><span><?= e((string) ($faq['question'] ?? 'Question')) ?></span><i class="fa-solid fa-plus" aria-hidden="true"></i></button>
                        <div id="<?= e($panelId) ?>"<?= $index === 0 ? '' : ' hidden' ?>><p><?= e((string) ($faq['answer'] ?? '')) ?></p></div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

    <?php elseif ($pageKey === 'contact'): ?>
        <section class="vsl-page-section vsl-contact-grid">
            <article class="vsl-contact-card"><i class="fa-solid fa-file-signature" aria-hidden="true"></i><span>SECURE REQUEST</span><h2>Quotation or Order</h2><p>Send product references, quantities, timelines and a supporting list through the protected form.</p><a href="<?= e(url('/')) ?>?intent=quotation#cta">Open Request Centre</a></article>
            <article class="vsl-contact-card"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i><span>DIRECT MESSAGE</span><h2>WhatsApp</h2><p>Use WhatsApp for quick clarification and include your product or request reference.</p><a href="https://wa.me/256701165527" target="_blank" rel="noopener">Message Varenz</a></article>
            <article class="vsl-contact-card"><i class="fa-solid fa-phone" aria-hidden="true"></i><span>CALL</span><h2>Speak to the Team</h2><p>Call during published business hours for urgent procurement clarification.</p><a href="tel:+256701165527">0701165527</a><a href="tel:+256730850411">0730850411</a></article>
            <article class="vsl-contact-card"><i class="fa-solid fa-envelope" aria-hidden="true"></i><span>EMAIL</span><h2>Send Documents</h2><p>Email formal requirements, procurement schedules or partnership information.</p><a href="mailto:<?= e((string) ($contact['email'] ?? 'info@varenzsupplies.com')) ?>"><?= e((string) ($contact['email'] ?? 'info@varenzsupplies.com')) ?></a></article>
        </section>
        <section class="vsl-page-section vsl-page-section--deep vsl-location-card">
            <div><span>LOCATION</span><h2><?= e((string) ($contact['location'] ?? 'Komamboga, Kampala, Uganda')) ?></h2><p><?= e((string) ($contact['hours'] ?? 'Monday to Saturday, 8:00 AM–6:00 PM')) ?></p></div>
            <a class="vsl-page-primary" href="https://www.google.com/maps/search/?api=1&amp;query=Komamboga%2C%20Kampala%2C%20Uganda" target="_blank" rel="noopener">Open Map</a>
        </section>
    <?php endif; ?>

    <section class="vsl-page-final-cta">
        <div class="vsl-effect-layer" data-vsl-effect="quotation-gradient" aria-hidden="true"></div>
        <div><span>READY TO CONTINUE?</span><h2>Turn your product requirement into a clear procurement request.</h2><p>Share what you know. Varenz will help organise the product, specification, quantity and timeline details.</p></div>
        <a class="vsl-page-primary" href="<?= e(url('/')) ?>?intent=quotation#cta">Request a Quotation</a>
    </section>
</main>
<?php require __DIR__ . '/../shared/site-footer.php'; ?>
