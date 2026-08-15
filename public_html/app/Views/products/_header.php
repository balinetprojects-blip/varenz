<?php
declare(strict_types=1);

require __DIR__ . '/../shared/site-header.php';
?>

<div class="vsl-catalog-request-bar" role="region" aria-label="Product request tools">
    <p><strong>Build a request list.</strong> Add products, then continue to the secure Request Centre.</p>
    <button class="vsl-request-list-button" type="button" data-request-list-open aria-controls="vslRequestList" aria-expanded="false">
        Request List <span data-request-count>0</span>
    </button>
</div>

<aside class="vsl-request-list" id="vslRequestList" data-request-list hidden aria-labelledby="requestListTitle">
    <div class="vsl-request-list__scrim" data-request-list-close></div>
    <section class="vsl-request-list__panel" role="dialog" aria-modal="true" aria-labelledby="requestListTitle">
        <div class="vsl-request-list__head">
            <div>
                <span>PROCUREMENT REQUEST</span>
                <h2 id="requestListTitle">Your product request list</h2>
            </div>
            <button type="button" data-request-list-close>Close</button>
        </div>
        <div data-request-list-items></div>
        <p class="vsl-request-list__empty" data-request-list-empty>Add products from the catalogue, then send the list through the secure Request Centre.</p>
        <a class="vsl-catalog-primary vsl-request-list__submit" data-request-list-submit href="<?= e(url('/')) ?>?intent=quotation#cta">Continue to Request Centre</a>
    </section>
</aside>
