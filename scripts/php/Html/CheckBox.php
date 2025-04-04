<?php

namespace WebsiteTemplate\Html;

/**
 * Create a HTMLSelectElement.
 */
class CheckBox extends Form
{
    /** @var string value attribute */
    public string $val;

    /**
     * Construct a CheckBox object.
     * Sets the id attribute and the value attribute.
     * @param string $id
     * @param string $val
     */
    public function __construct(string $id, string $val)
    {
        $this->setId($id);
        $this->val = $val;
        $this->name = $id;
    }

    /**
     * Set the form element or item to be selected.
     * If set then the HTMLFormElement is rendered selected.
     * @param bool $checked
     */
    public function setChecked(?bool $checked = null): void
    {
        // TODO: remove method use Checkbox::checked and SelectField. setSelected instead
        $this->checked = $checked ?? true;
    }


    /**
     * Print the HTMLCheckboxElement.
     * @return string Html
     */
    public function render(): string
    {
        $css = $this->renderCssClass();
        $strHtml = '';

        $strLabel = '';
        if ($this->label) {
            $strLabel .= '<label for="'.$this->getId().'" '.$css.'>'.$this->getLabel().'</label>';
        }
        if ($this->labelPosition === Form::LABEL_BEFORE) {
            $strHtml .= $strLabel;
        }

        $strHtml .= '<input id="'.$this->getId().'" name="'.$this->name.'"';
        $strHtml .= ' type="checkbox" value="'.$this->val.'"';
        if ($this->checked === true) {
            $strHtml .= ' checked="checked"';
        }
        if ($this->disabled === true) {
            $strHtml .= ' disabled="disabled"';
        }
        if ($this->tabIndex) {
            $strHtml .= ' tabindex="'.$this->tabIndex.'"';
        }
        $strHtml .= $css;
        if ($this->required === true) {
            $strHtml .= ' required="required"';
        }
        $strHtml .= '>';
        if ($this->labelPosition === Form::LABEL_AFTER) {
            $strHtml .= $strLabel;
        }

        return $strHtml;
    }
}