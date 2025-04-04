<?php

namespace WebsiteTemplate\Html;

use PDOStatement;

/**
 * Class to create HTMLFormElements.
 *
 * This base class defines a number of attributes and methods to deal with
 * HTMLAttributes that are shared by all HTMLFormElements, such as the label, disabled, selected attribute.
 * TODO: add method renderAttributes, which can be reused in child classes to set id, name, etc. attributes
 */
class Form extends Html
{
    /** Render the label before the form element */
    public const LABEL_BEFORE = 1;

    /** Render the label after the form element */
    public const LABEL_AFTER = 2;

    /** @var bool renderAsHtml label attribute */
    protected bool $label = false;

    /** @var string label */
    protected string $labelName = '';

    /** @var string|int|PDOStatement position of label in relation to element */
    protected string|int|PDOStatement $labelPosition = Form::LABEL_BEFORE;

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
     * @param int $index
     */
    public function setTabIndex(int $index): void
    {
        $this->tabIndex = $index;
    }

    /**
     * Set a form element to disabled.
     * If set to true the HTMLFormAttribute disabled="disabled" is rendered
     * and the element is disabled by the browser.
     * @param bool $bool
     */
    public function setDisabled(?bool $bool = null): void
    {
        $this->disabled = $bool ?? true;
    }

    /**
     * Set the form element label.
     * If set then the label attribute is rendered. The position can be set to before or after with the constants
     * HTML_LABEL_BEFORE and HTML_LABEL_AFTER.
     * @param string $label label
     * @param int|null|PDOStatement $position position of label
     */
    public function setLabel(string $label, int|null|PDOStatement $position = null): void
    {
        $this->labelName = $label;
        $this->label = true;
        $this->labelPosition = $position ?? self::LABEL_BEFORE;
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
     * Return the label of the HTMLFormElement.
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
     * Set HTMLAttribute required to true or false.
     * @param bool $bool
     */
    public function setRequired(?bool $bool = null): void
    {
        $this->required = $bool ?? true;
    }

    /**
     * Set the name attribute of the element
     * @param bool|string $name
     */
    public function setName(bool|string $name): void
    {
        $this->name = $name;
    }
}