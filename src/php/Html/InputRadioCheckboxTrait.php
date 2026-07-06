<?php

namespace WebsiteTemplate\Html;

trait InputRadioCheckboxTrait
{

    // enable when >= php8.2
    //public const TYPE_RADIO = 'radio';
    //public const TYPE_CHECKBOX = 'checkbox';

    /**
     * Render the input element.
     *
     * @param string $type radio or checkbox
     *
     * @return string
     */
    public function renderInput(string $type): string
    {
        $this->addCssClass($this->bemClass());
        if ($this->checked) {
            $this->addCssClass($this->bemClass(modifier: 'checked'));
        }
        if ($this->disabled) {
            $this->addCssClass($this->bemClass(modifier: 'disabled'));
        }
        if ($this->required) {
            $this->addCssClass($this->bemClass(modifier: 'required'));
        }

        $strInput = '<input id="'.$this->getId().'"';
        if ($this->name !== false) {
            $strInput .= ' name="'.$this->name.'"';
        }
        $strInput .= ' type="'.$type.'" value="'.$this->val.'"';
        if ($this->checked === true) {
            $strInput .= ' checked="checked"';
        }
        if ($this->disabled === true) {
            $strInput .= ' disabled="disabled"';
        }
        if ($this->tabIndex) {
            $strInput .= ' tabindex="'.$this->tabIndex.'"';
        }
        $strInput .= $this->renderCssClass();
        if ($this->required === true) {
            $strInput .= ' required="required"';
        }
        $strInput .= '>';

        return $strInput;
    }

}