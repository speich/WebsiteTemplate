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
        $inputClasses = [$this->bemClass('input')];
        if ($this->checked) {
            $inputClasses[] = $this->bemClass('input', 'checked');
        }
        if ($this->disabled) {
            $inputClasses[] = $this->bemClass('input', 'disabled');
        }
        if ($this->required) {
            $inputClasses[] = $this->bemClass('input', 'required');
        }

        $strInput = '<input';
        if ($this->id !== null) {
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
        if ($this->tabIndex !== null) {
            $strInput .= ' tabindex="'.$this->tabIndex.'"';
        }

        if (self::isLabelWrapped($this->labelPosition)) {
            // WRAPPED MODE: The label is the primary node.
            // We DO NOT call renderCssClass() here. We save the global object classes for the label
            // and only print the specific input classes here.
            $strInput .= ' class="'.implode(' ', $inputClasses).'"';
        } else {
            // Handle the CSS classes based on layout
            // SIBLING MODE: The input is the primary node.
            // Add the base class ('radio') and element classes ('radio__input') to the object state.
            // Calling renderCssClass() outputs them ALL, including 'radio-group__item' injected by RadioGroup.
            $this->addCssClass($this->bemClass());
            foreach ($inputClasses as $cls) {
                $this->addCssClass($cls);
            }
            $strInput .= $this->renderCssClass();
        }

        if ($this->required) {
            $strInput .= ' required="required"';
        }

        $strInput .= '>';

        return $strInput;
    }

}