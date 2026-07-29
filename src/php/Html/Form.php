<?php

namespace WebsiteTemplate\Html;

use LogicException;

/**
 * Class to create HTMLFormElements.
 *
 * This base class defines a number of attributes and methods to deal with
 * HTMLAttributes that are shared by all HTMLFormElements, such as the label, disabled, selected attribute.
 */
class Form extends Html
{

    /** @var string|null label */
    public ?string $label = null;

    /** @var LabelPosition position of label in relation to the element */
    public LabelPosition $labelPosition = LabelPosition::Before;

    /** @var bool|string disabled attribute */
    public bool $disabled = false;

    /** @var bool|string selected attribute */
    public bool $checked = false;

    /** @var bool|string required attribute */
    public bool $required = false;

    /** @var int|null tab index attribute */
    public ?int $tabIndex = null;

    /** @var string|null name attribute */
    public ?string $name = null;

    /**
     * @param string $strInput
     *
     * @return string
     */
    protected function renderLabel(string $strInput): string
    {
        $isWrapped = self::isLabelWrapped($this->labelPosition);
        $hasId = ($this->id !== null && $this->id !== '');

        // 1. Check for the impossible accessibility state
        if (!$isWrapped && !$hasId) {
            throw new LogicException(
                'A form element using a sibling label layout must have an ID set to generate a valid "for" attribute.'
            );
        }

        if ($isWrapped) {
            $this->addCssClass($this->bemClass());
            $css = $this->renderCssClass();
        } else {
            $css = ' class="'.$this->bemClass('label').'"';
        }

        // 2. Safely assemble the label tag
        $labelTag = '<label';
        if ($hasId) {
            $labelTag .= ' for="'.$this->id.'"';
        }
        $labelTag .= $css.'>';

        return match ($this->labelPosition) {
            LabelPosition::After => $strInput.$labelTag.$this->label.'</label>',
            LabelPosition::Before => $labelTag.$this->label.$strInput.'</label>',
            LabelPosition::WrappedAfter => $labelTag.$strInput.$this->label.'</label>',
            LabelPosition::WrappedBefore => $labelTag.$this->label.'</label>'.$strInput,
        };
    }

    public static function isLabelWrapped($position): bool
    {
        return in_array($position, [LabelPosition::WrappedBefore, LabelPosition::WrappedAfter], true);
    }

}