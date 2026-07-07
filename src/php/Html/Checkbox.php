<?php

namespace WebsiteTemplate\Html;

/**
 * Create an HTMLInputElement of type checkbox.
 */
class Checkbox extends Form
{

    use InputRadioCheckboxTrait;

    /** @var string value attribute */
    public string $val;

    public ?string $bemBlock = 'checkbox';

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