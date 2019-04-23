<?php

namespace WebsiteTemplate\html;

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
    const LABEL_BEFORE = 1;

    /** Render the label after the form element */
    const LABEL_AFTER = 2;

    /** @var bool renderAsHtml label attribute */
    protected $label = false;

    /** @var string label */
    protected $labelName = '';

    /** @var string position of label in relation to element */
    protected $labelPosition = Form::LABEL_BEFORE;

    /** @var bool|string disabled attribute */
    protected $disabled = false;

    /** @var bool|string selected attribute */
    protected $checked = false;

    /** @var bool|string required attribute */
    protected $required = false;

    /** @var bool|integer tab index attribute */
    protected $tabIndex = false;

    /** @var bool|string name attribute */
    protected $name = false;

    /**
     * Set the element's tab index
     * @param integer $index
     */
    public function setTabIndex($index)
    {
        $this->tabIndex = $index;
    }

    /**
     * Set a form element to disabled.
     * If set to true the HTMLFormAttribute disabled="disabled" is rendered
     * and the element is disabled by the browser.
     * @param bool $bool
     */
    public function setDisabled($bool = true)
    {
        $this->disabled = $bool;
    }

    /**
     * Set the form element label.
     * If set then the label attribute is rendered. The position can be set to before or after with the constants
     * HTML_LABEL_BEFORE and HTML_LABEL_AFTER.
     * @param string $label label
     * @param int $position position of label
     */
    public function setLabel($label, $position = Form::LABEL_BEFORE)
    {
        $this->labelName = $label;
        $this->label = true;
        $this->labelPosition = $position;
    }

    /**
     * Remove form element label.
     */
    public function removeLabel()
    {
        $this->labelName = null;
        $this->label = false;
    }

    /**
     * Return the label of the HTMLFormElement.
     * @return string|bool label or false
     */
    public function getLabel()
    {
        if ($this->label) {
            return $this->labelName;
        }

        return false;
    }

    /**
     * Set HTMLAttribute required to true or false.
     * @param $bool
     */
    public function setRequired($bool = true)
    {
        $this->required = $bool;
    }

    /**
     * Set the name attribute of the element
     * @param bool|string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}