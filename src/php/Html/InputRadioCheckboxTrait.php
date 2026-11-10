<?php

namespace WebsiteTemplate\Html;


trait InputRadioCheckboxTrait
{
    /**
     * Render the input element.
     *
     * @param InputType $type
     *
     * @return string
     */
    public function renderInput(InputType $type): string
    {
        $strInput = '<input';
        if ($this->id !== null && $this->id !== '') {
            $strInput .= ' id="'.$this->id.'"';
        }
        if ($this->name !== null) {
            $strInput .= ' name="'.$this->name.'"';
        }
        $strInput .= ' type="'.$type->value.'" value="'.htmlspecialchars($this->val, ENT_QUOTES).'"';

        if ($this->checked) {
            $strInput .= ' checked="checked"';
        }
        if ($this->disabled) {
            $strInput .= ' disabled="disabled"';
        }
        if ($this->required) {
            $strInput .= ' required="required"';
        }
        if ($this->tabIndex !== null) {
            $strInput .= ' tabindex="'.$this->tabIndex.'"';
        }
        $strInput .= $this->renderCssClass();
        $strInput .= '>';

        return $strInput;
    }

}