<?php

/**
 * @author          jtotal <support@jtotal.org>
 * @link            https://jtotal.org
 * @copyright       Copyright © jtotal.org
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 *
 * Página de error autocontenida: no depende de assets externos
 * (Bootstrap/Font Awesome) para que funcione siempre, incluso en una
 * instalación donde 'templates/buf/libs' no está publicado.
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

$app       = Factory::getApplication();
$errorCode = (int) $this->error->getCode();
$sitename  = htmlspecialchars((string) $app->get('sitename'), ENT_QUOTES, 'UTF-8');
$lang      = htmlspecialchars((string) $this->language, ENT_QUOTES, 'UTF-8');
$dir       = htmlspecialchars((string) ($this->direction ?? 'ltr'), ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $dir; ?>">
<head>
<meta charset="utf-8">
<title><?php echo $errorCode; ?> &ndash; <?php echo htmlspecialchars((string) $this->title, ENT_QUOTES, 'UTF-8'); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<style>
    :root {
        --buf-bg: #f5f6f8;
        --buf-surface: #ffffff;
        --buf-text: #1f2430;
        --buf-muted: #5b6472;
        --buf-accent: #2b6cb0;
        --buf-accent-contrast: #ffffff;
        --buf-border: rgba(0, 0, 0, .08);
        --buf-shadow: 0 10px 40px rgba(0, 0, 0, .08);
    }

    @media (prefers-color-scheme: dark) {
        :root {
            --buf-bg: #14171c;
            --buf-surface: #1c2027;
            --buf-text: #eceef1;
            --buf-muted: #a0a8b4;
            --buf-accent: #5b9bd8;
            --buf-accent-contrast: #10131a;
            --buf-border: rgba(255, 255, 255, .1);
            --buf-shadow: 0 10px 40px rgba(0, 0, 0, .5);
        }
    }

    * { box-sizing: border-box; }

    body {
        margin: 0;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
        background: var(--buf-bg);
        color: var(--buf-text);
        font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
        line-height: 1.6;
    }

    .buf-error {
        max-width: 34rem;
        width: 100%;
        text-align: center;
        background: var(--buf-surface);
        border: 1px solid var(--buf-border);
        border-radius: 1rem;
        box-shadow: var(--buf-shadow);
        padding: 2.5rem 2rem;
    }

    .buf-error__icon {
        width: 4rem;
        height: 4rem;
        margin: 0 auto 1.25rem;
        color: var(--buf-accent);
    }

    .buf-error__icon svg { width: 100%; height: 100%; }

    .buf-error__code {
        font-size: .875rem;
        font-weight: 600;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--buf-muted);
        margin: 0 0 .5rem;
    }

    .buf-error__title {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0 0 .75rem;
    }

    .buf-error__message {
        color: var(--buf-muted);
        margin: 0 0 1.75rem;
        word-wrap: break-word;
    }

    .buf-error__home {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .75rem 1.5rem;
        border-radius: .625rem;
        background: var(--buf-accent);
        color: var(--buf-accent-contrast);
        text-decoration: none;
        font-weight: 600;
        transition: filter .2s ease;
    }

    .buf-error__home:hover { filter: brightness(1.08); }

    .buf-error__home:focus-visible {
        outline: 3px solid var(--buf-accent);
        outline-offset: 3px;
    }

    @media (prefers-reduced-motion: reduce) {
        .buf-error__home { transition: none; }
    }
</style>
</head>
<body>
    <main class="buf-error" role="main">
        <div class="buf-error__icon" aria-hidden="true">
            <?php if ($errorCode === 404) : ?>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="7"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            <?php else : ?>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="9"></circle>
                    <line x1="12" y1="8" x2="12" y2="13"></line>
                    <line x1="12" y1="16.5" x2="12.01" y2="16.5"></line>
                </svg>
            <?php endif; ?>
        </div>

        <p class="buf-error__code"><?php echo $sitename; ?> &middot; <?php echo Text::_('JERROR'); ?> <?php echo $errorCode; ?></p>

        <h1 class="buf-error__title"><?php echo htmlspecialchars((string) $this->error->getMessage(), ENT_QUOTES, 'UTF-8'); ?></h1>

        <?php if ($errorCode === 404) : ?>
            <p class="buf-error__message"><?php echo Text::_('JERROR_LAYOUT_REQUESTED_RESOURCE_WAS_NOT_FOUND'); ?></p>
        <?php endif; ?>

        <a class="buf-error__home" href="<?php echo htmlspecialchars((string) $this->baseurl, ENT_QUOTES, 'UTF-8'); ?>/">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M3 11.5 12 4l9 7.5"></path>
                <path d="M5 10v10h14V10"></path>
            </svg>
            <?php echo Text::_('TPL_BUF_GOTO_HOMEPAGE'); ?>
        </a>
    </main>
</body>
</html>
