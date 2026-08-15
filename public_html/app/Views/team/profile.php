<?php
declare(strict_types=1);

$contact = isset($contact) && is_array($contact)
    ? $contact
    : (
        is_array(config('contact'))
            ? config('contact')
            : []
    );

$memberName = trim((string) ($member['name'] ?? 'Varenz Team Member'));
$memberRole = trim((string) ($member['role'] ?? ''));
$memberSummary = trim((string) ($member['summary'] ?? ''));

$memberDepartment = trim(
    (string) (
        $member['department']
        ?? $member['dept']
        ?? ''
    )
);

$memberResponsibility = trim(
    (string) ($member['responsibility'] ?? '')
);

$memberPhone = trim(
    (string) ($member['phone'] ?? '')
);

$companyEmail = trim(
    (string) ($contact['email'] ?? '')
);

/*
 * Generate safe telephone and WhatsApp destinations.
 *
 * Display formatting is kept separate from transport formatting.
 */
$phoneDigits = preg_replace(
    '/\D+/',
    '',
    $memberPhone
) ?? '';

$phoneHref = $phoneDigits !== ''
    ? $phoneDigits
    : '';

$whatsApp = $phoneDigits;

/*
 * Prefer the team member's official telephone number for the displayed
 * contact. Fall back to the company email when no direct number is published.
 */
$officialContact = $memberPhone !== ''
    ? $memberPhone
    : $companyEmail;
?>

<div class="profile-page-copy">
    <span class="eyebrow">
        Verified Varenz Team Member
    </span>

    <h1>
        <?= e($memberName) ?>
    </h1>

    <?php if ($memberRole !== ''): ?>
        <h2>
            <?= e($memberRole) ?>
        </h2>
    <?php endif; ?>

    <?php if ($memberSummary !== ''): ?>
        <p>
            <?= e($memberSummary) ?>
        </p>
    <?php endif; ?>

    <div
        class="info-list"
        aria-label="<?= e($memberName) ?> profile information"
    >
        <?php if ($memberDepartment !== ''): ?>
            <div class="info-item">
                <h6>
                    Department
                </h6>

                <p>
                    <?= e($memberDepartment) ?>
                </p>
            </div>
        <?php endif; ?>

        <?php if ($memberResponsibility !== ''): ?>
            <div class="info-item">
                <h6>
                    Area of Responsibility
                </h6>

                <p>
                    <?= e($memberResponsibility) ?>
                </p>
            </div>
        <?php endif; ?>

        <?php if ($officialContact !== ''): ?>
            <div class="info-item">
                <h6>
                    Official Contact
                </h6>

                <p>
                    <?php if ($memberPhone !== '' && $phoneHref !== ''): ?>
                        <a href="tel:<?= e($phoneHref) ?>">
                            <?= e($memberPhone) ?>
                        </a>
                    <?php elseif ($companyEmail !== ''): ?>
                        <a href="mailto:<?= e($companyEmail) ?>">
                            <?= e($companyEmail) ?>
                        </a>
                    <?php endif; ?>
                </p>
            </div>
        <?php endif; ?>
    </div>

    <div
        class="action-row"
        aria-label="Team member contact actions"
    >
        <?php if ($phoneHref !== ''): ?>
            <a
                class="btn btn-secondary"
                href="tel:<?= e($phoneHref) ?>"
                aria-label="Call <?= e($memberName) ?>"
            >
                <svg
                    class="icon"
                    aria-hidden="true"
                    focusable="false"
                >
                    <use href="#i-phone"></use>
                </svg>

                Call
            </a>
        <?php endif; ?>

        <?php if ($whatsApp !== ''): ?>
            <a
                class="btn btn-secondary"
                href="https://wa.me/<?= e($whatsApp) ?>"
                target="_blank"
                rel="noopener noreferrer"
                aria-label="Contact <?= e($memberName) ?> on WhatsApp"
            >
                <svg
                    class="icon"
                    aria-hidden="true"
                    focusable="false"
                >
                    <use href="#i-message"></use>
                </svg>

                WhatsApp
            </a>
        <?php endif; ?>

        <a
            class="btn btn-primary"
            href="<?= e(url('/#cta')) ?>"
        >
            <svg
                class="icon"
                aria-hidden="true"
                focusable="false"
            >
                <use href="#i-file"></use>
            </svg>

            Request a Quote
        </a>
    </div>
</div>