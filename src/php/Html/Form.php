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

        if ($isWrapped) {
            $this->addCssClass($this->blockClass());
        }
        $css = $this->renderCssClass();

        // 2. Safely assemble the label tag
        $labelTag = '<label';
        if ($hasId) {
            $labelTag .= ' for="'.$this->id.'"';
        }
        $labelTag .= $css.'>';
        $label = $this->label ?? '';

        return match ($this->labelPosition) {
            LabelPosition::Before => $labelTag . $label . '</label>' . $strInput,
            LabelPosition::After => $strInput . $labelTag . $label . '</label>',
            LabelPosition::WrappedBefore => $labelTag . $label . $strInput . '</label>',
            LabelPosition::WrappedAfter => $labelTag . $strInput . $label . '</label>',
        };
    }

    /**
     * Check for wrapped label layout
     * @param $position
     * @return bool
     */
    public static function isLabelWrapped($position): bool
    {
        return match ($position) {
            LabelPosition::WrappedBefore, LabelPosition::WrappedAfter => true,
            default => false,
        };
    }

}