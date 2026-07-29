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

    // Override the base Form default for radio buttons
    public LabelPosition $labelPosition = LabelPosition::WrappedAfter;

    /**
     * Construct an HTMLInputElement of type checkbox.
     * Sets the nane attribute, the value attribute, and optionally the id attribute.
     *
     * @param string $name name attribute
     * @param string $val value attribute
     * @param bool $nameOnly if true, only the name attribute is set, otherwise the id attribute is set to the name attribute
     */
    public function __construct(string $name, string $val, bool $nameOnly = true)
    {
        $this->name = $name;
        $this->val = $val;
        if (!$nameOnly) {
            $this->id = $name;
        }
    }

    /**
     * Print the HTMLCheckboxElement.
     *
     * @return string Html
     */
    public function render(): string
    {
        $strInput = $this->renderInput(InputType::Checkbox);
        if ($this->label !== null) {
            $strHtml = $this->renderLabel($strInput);
        } else {
            $strHtml = $strInput;
        }

        return $strHtml;
    }

}