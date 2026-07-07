<?php

namespace WebsiteTemplate\Html;

/**
 * Class to create HTMLFormElements.
 *
 * This base class defines a number of attributes and methods to deal with
 * HTMLAttributes that are shared by all HTMLFormElements, such as the label, disabled, selected attribute.
 */
class Form extends Html
{

    /** Render the label before the form element */
    public const LABEL_BEFORE = 1;

    /** Render the label after the form element */
    public const LABEL_AFTER = 2;

    /** Render the label wrapped around the form field and placed the text before the radio button */
    public const LABEL_WRAPPED_BEFORE = 3;

    /** Render the label wrapped around the form field and placed the text after the radio button */
    public const LABEL_WRAPPED_AFTER = 4;

    /** @var bool renderAsHtml label attribute */
    protected bool $label = false;

    /** @var string label */
    protected string $labelName = '';

    /** @var int position of label in relation to the element */
    protected int $labelPosition = Form::LABEL_BEFORE;

    /** @var bool|string disabled attribute */
    protected string|bool $disabled = false;

    /** @var bool|string selected attribute */
    protected string|bool $checked = false;

    /** @var bool|string required attribute */
    protected string|bool $required = false;

    /** @var bool|int tab index attribute */
    protected int|bool $tabIndex = false;

    /** @var bool|string name attribute */
    protected string|bool $name = false;

    /**
     * Set the element's tab index
     *
     * @param int $index
     */
    public function setTabIndex(int $index): void
    {
        $this->tabIndex = $index;
    }

    /**
     * Set the checked/selected state of the form element.
     */
    public function setChecked(bool $checked = true): void
    {
        $this->checked = $checked;
    }

    /**
     * Set a form element disabled.
     * If set to true, the HTMLFormAttribute disabled="disabled" is rendered
     * and the element is disabled by the browser.
     *
     * @param bool $bool
     */
    public function setDisabled(?bool $bool = null): void
    {
        $this->disabled = $bool ?? true;
    }

    /**
     * Remove form element label.
     */
    public function removeLabel(): void
    {
        $this->labelName = null;
        $this->label = false;
    }

    /**
     * Set HTMLAttribute required to true or false.
     *
     * @param bool $bool
     */
    public function setRequired(?bool $bool = null): void
    {
        $this->required = $bool ?? true;
    }

    /**
     * Set the name attribute of the element
     *
     * @param bool|string $name
     */
    public function setName(bool|string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param string $strInput
     *
     * @return string
     */
    protected function renderLabel(string $strInput): string
    {
        $css = $this->renderCssClass();
        $labelTag = '<label for="'.$this->getId().'"'.$css.'>';
        $label = $this->getLabel();

        return match ($this->labelPosition) {
            self::LABEL_AFTER => $strInput.$labelTag.$label.'</label>',
            self::LABEL_WRAPPED_BEFORE => $labelTag.$label.$strInput.'</label>',
            self::LABEL_WRAPPED_AFTER => $labelTag.$strInput.$label.'</label>',
            default => $labelTag.$label.'</label>'.$strInput,
        };
    }

    /**
     * Return the label of the HTMLFormElement.
     *
     * @return string|bool label or false
     */
    public function getLabel(): bool|string
    {
        if ($this->label) {
            return $this->labelName;
        }

        return false;
    }

    /**
     * Set the form element label.
     * If set, then the label attribute is rendered. The position can be set to before or after with the constants
     * HTML_LABEL_BEFORE and HTML_LABEL_AFTER.
     *
     * @param string $label label
     * @param int|null $position position of label
     */
    public function setLabel(string $label, int|null $position = null): void
    {
        $this->labelName = $label;
        $this->label = true;
        $this->labelPosition = $position ?? $this->labelPosition;
    }

}