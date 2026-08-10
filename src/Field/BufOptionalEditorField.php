<?php

declare(strict_types=1);

namespace Jtotal\BUF\Site\Field;

use Joomla\CMS\Form\Field\EditorField;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

class BufOptionalEditorField extends EditorField
{
    public $type = 'BufOptionalEditor';

    protected function getInput()
    {
        if ($this->shouldUsePlainTextarea()) {
            return rtrim($this->getRenderer($this->layout)->render($this->collectLayoutData()), PHP_EOL);
        }

        return parent::getInput();
    }

    private function shouldUsePlainTextarea(): bool
    {
        return $this->countSprintfPlaceholders(Text::_('PLG_CODEMIRROR_TOGGLE_FULL_SCREEN')) > 1;
    }

    private function countSprintfPlaceholders(string $format): int
    {
        $normalized = preg_replace('/\[\[%([0-9]+):[^\]]*\]\]/', '%$1$s', $format) ?? $format;

        preg_match_all(
            '/%(?:\d+\$)?[-+ 0#\']*(?:\*|\d+)?(?:\.(?:\*|\d+))?[bcdeEfFgGosuxX]/',
            $normalized,
            $matches
        );

        return count($matches[0]);
    }
}