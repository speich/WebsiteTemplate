<?php

namespace WebsiteTemplate;

use LogicException;

/**
 * Trait to build CSS class names following BEM naming conventions.
 * Block and element are separated by a double underscore, the modifier by a single underscore.
 * Hyphens are only used inside compound (multi-word) names, e.g., block-name__elem-name_mod-name.
 *
 * Classes using this trait can set $bemBlock, $bemElement, $bemModifier as defaults, used whenever the
 * corresponding argument is omitted (null) in bemClass(). Pass an empty string '' explicitly to opt out
 * of a default for a single call, e.g., bemClass(element: '') for a block-only class name.
 */
trait CssBemTrait
{
    /** @var ?string default BEM block name, e.g. 'menu' */
    public ?string $bemBlock = null;

    /** @var ?string default BEM element name, e.g. 'item' */
    public ?string $bemElement = null;

    /** @var ?string default BEM modifier name, e.g. 'active' */
    public ?string $bemModifier = null;

    /**
     * Build a class name following BEM naming conventions.
     * A null argument falls back to the corresponding $bemBlock/$bemElement/$bemModifier property.
     * Pass an empty string to explicitly omit that part for this call, overriding the default.
     * @param string|null $element element name, defaults to $this->bemElement
     * @param string|null $modifier modifier name, defaults to $this->bemModifier
     * @param string|null $block block name, defaults to $this->bemBlock
     * @return string BEM class name
     */
    protected function bemClass(?string $element = null, ?string $modifier = null, ?string $block = null): string
    {
        $block ??= $this->bemBlock;
        if ($block === null) {
            throw new LogicException(static::class.': BEM block name is not set. Set $bemBlock or pass the $block argument.');
        }
        $element ??= $this->bemElement;
        $modifier ??= $this->bemModifier;

        $class = $block;
        if (!empty($element)) {
            $class .= '__'.$element;
        }
        if (!empty($modifier)) {
            $class .= '_'.$modifier;
        }

        return $class;
    }
}