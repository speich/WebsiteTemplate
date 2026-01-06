<?php

namespace WebsiteTemplate\Html;

/**
 * Create an HTMLInputElement of type checkbox.
 */
class CheckBox extends Form
{

    use InputRadioCheckboxTrait;

    /** @var string value attribute */
    public string $val;

    /**
     * Construct an HTMLInputElement of type checkbox.
     * Sets the id attribute and the value attribute.
     *
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
     *
     * @param bool $checked
     */
    public function setChecked(?bool $checked = null): void
    {
        // TODO: remove method use Checkbox::checked and SelectField. setSelected instead
        $this->checked = $checked ?? true;
    }

    /**
     * Print the HTMLCheckboxElement.
     *
     * @return string Html
     */
    public function render(): string
    {
        $strInput = $this->renderInput('checkbox');
        if ($this->label) {
            $strHtml = $this->renderLabel($strInput);
        } else {
            $strHtml = $strInput;
        }

        return $strHtml;
    }

}