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
    public function __construct($id, $val)
    {
        $this->setId($id);
        $this->val = $val;
    }

    /**
     * Set the checked attribute to checked.
     * @param bool $checked
     */
    public function setChecked($checked = true)
    {
        // TODO: remove method use Checkbox::checked and SelectField. setSelcted instead
        $this->checked = (bool)$checked;
    }

    /**
     * Print out the HTML radio button.
     * @return string Html
     */
    public function render()
    {
        $strHtml = '';
        $strLabel = '';

        if ($this->label) {
            $strLabel .= '<label for="'.$this->getId().'"';
            if ($this->cssClass) {
                $strLabel .= ' class="label'.$this->cssClass.'"';
            }
            $strLabel .= '>'.$this->getLabel().'</label>';
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
        if ($this->cssClass) {
            $strHtml .= ' class="'.$this->cssClass.'"';
        }
        if ($this->cssStyle) {
            $strHtml .= ' style="'.$this->cssStyle.'"';
        }
        if ($this->required === true) {
            $strHtml .= ' required="required"';
        }
        $strHtml .= ">";
        if ($this->labelPosition === Form::LABEL_AFTER) {
            $strHtml .= $strLabel;
        }

        return $strHtml;
    }
}