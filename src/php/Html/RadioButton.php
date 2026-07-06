<?php

namespace WebsiteTemplate\Html;

/**
 * Class to create an HTMLInputElement of type radio.
 * Often radio buttons occur in a group with the same name. Use the setGroup method.
 *
 * @see setGroup() method
 */
class RadioButton extends Form
{

    use InputRadioCheckboxTrait;

    /** @var string value attribute */
    public string $val;

    public ?string $bemBlock = 'radio';

    /**
     * Construct an HTMLInputElement of type radio.
     * The constructor sets the id and value attribute of the HTMLRadioElement.
     *
     * @param string $id HTMLAttribute id
     * @param string $val HTMLAttribute value
     */
    public function __construct(string $id, string $val)
    {
        $this->setId($id);
        $this->val = $val;
        $this->name = $id;
    }

    /**
     * Set the checked attribute to checked.
     *
     * @param bool $checked
     */
    public function setChecked(?bool $checked = null): void
    {
        // TODO: remove method use Checkbox::checked and SelectField. setSelected instead
        $this->checked = $checked ?? true;
    }

    /**
     * Print out the HTML radio button.
     *
     * @return string Html
     */
    public function render(): string
    {
        $strInput = $this->renderInput('radio');
        if ($this->label) {
            $strHtml = $this->renderLabel($strInput);
        } else {
            $strHtml = $strInput;
        }

        return $strHtml;
    }

}