<?php

namespace WebsiteTemplate\Html;


/**
 * Trait to handle generating CSS Block and Modifier classes.
 */
trait CssBlockTrait
{
    /** @var string|null The base block name for the component (e.g., 'radio', 'menu') */
    public ?string $blockName = null;

    /**
     * Get the CSS block name, optionally appended with a modifier.
     *
     * @param string $modifier Optional modifier string (e.g., 'vertical', 'large')
     * @return string The class name (e.g., 'radio' or 'radio--vertical')
     */
    public function blockClass(string $modifier = ''): string
    {
        // Fallback in case the blockName property wasn't set on the class
        $block = $this->blockName ?? 'component';

        if ($modifier !== '') {
            // Standard CSS modifier syntax (Block--Modifier)
            return $block . '--' . $modifier;
        }

        return $block;
    }
}