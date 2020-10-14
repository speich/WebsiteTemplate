<?php

namespace WebsiteTemplate\html;

/**
 * Class to create a HTMLRadioElement.
 * Often radio buttons occur in group with the same name. Use the setGroup method.
 * @see setGroup() method
 */
class RadioButton extends Form
{
    /** @var string value attribute */
    public $val;

    /**
     * Construct a HtmlRadioButton object.
     * The constructor sets the id and value attribute of the HTMLRadioElement.
     * @param string $id HTMLAttribute id
     * @param string $val HTMLAttribute value
     */
    public function __construct(string $id, string $val)
    {
        $this->setId($id);
        $this->val = $val;
    }

    /**
     * Set the checked attribute to checked.
     * @param bool $checked
     */
    public function setChecked(?bool $checked = null): void
    {
        // TODO: remove method use Checkbox::checked and SelectField. setSelected instead
        $this->checked = $checked ?? true;;
    }

    /**
     * Print out the HTML radio button.
     * @return string Html
     */
    public function render(): string
    {
        $strHtml = '';
        $css = $this->renderCssClass();

        $strLabel = '';
        if ($this->label) {
            $strLabel .= '<label for="'.$this->getId().'" '.$css.'>'.$this->getLabel().'</label>';
        }
        if ($this->labelPosition === Form::LABEL_BEFORE) {
            $strHtml .= $strLabel;
        }
        $strHtml .= '<input id="'.$this->getId().'"';
        if ($this->name !== false) {
            $strHtml .= ' name="'.$this->name.'"';
        }
        $strHtml .= ' type="radio" value="'.$this->val.'"';
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