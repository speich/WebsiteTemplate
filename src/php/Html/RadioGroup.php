<?php

namespace WebsiteTemplate\Html;

use WebsiteTemplate\Orientation;

/**
 * Class RadioGroup
 * Creates HTMLRadioElements
 * @package WebsiteTemplate\html
 */
class RadioGroup extends Form
{
    public ?string $blockName = 'radio-group';

    /** @var RadioButton[] */
    public array $radios = [];

    /**
     * @var Orientation render the radio group horizontally or vertically
     */
    public Orientation $orientation = Orientation::Horizontal;

    /**
     * RadioGroup constructor.
     * Uses the $name attribute to create the group of radios with the same name.
     *
     * @param string $name The name attribute shared by all radio buttons in the group
     * @param array $values Array format: ['Attr value' => 'Label Text']
     * @param bool $nameOnly if true, only the name attribute is set, otherwise the id attribute is set to the name attribute
     */
    public function __construct(string $name, array $values, bool $nameOnly = true)
    {
        $index = 1;
        foreach ($values as $attrValue => $labelText) {
            $id = $name.$index++;
            $radio = new RadioButton($id, (string)$attrValue, $nameOnly);
            $radio->name = $name;
            $radio->label = $labelText;
            $radio->addCssClass($this->blockClass('item'));
            $this->radios[] = $radio;
        }
    }

    /**
     * @param array $labels
     * @param LabelPosition|null $position
     */
    public function setLabels(array $labels, ?LabelPosition $position = null): void
    {
        $position = $position ?? LabelPosition::WrappedAfter;
        foreach ($labels as $key => $label) {
            $this->radios[$key]->label = $label;
            $this->radios[$key]->labelPosition = $position;
        }
    }

    /**
     * @param array $indices
     */
    public function setTabIndices(array $indices): void
    {
        foreach ($indices as $key => $index) {
            $this->radios[$key]->tabIndex = $index;
        }
    }

    /**
     * Set a radio button of the group to checked state.
     * Sets the radio button, where the value attribute equals the parameter $value to checked.
     * Note: uses strict comparison
     * @param string $value value to set checked
     */
    public function setCheckedVal(string $value): void
    {
        foreach ($this->radios as $radio) {
            $radio->checked = $radio->val === $value;
        }
    }

    /**
     * Set all radios to the disabled state.
     * If set to true, the HTMLFormAttribute disabled="disabled" is rendered
     * and the element is disabled by the browser.
     * @param bool $bool
     */
    public function setDisabled(?bool $bool = null): void
    {
        foreach ($this->radios as $radio) {
            $radio->disabled = $bool;
        }
    }

    /**
     * Render the radio button group as HTML.
     * Sets a CSS class which renders the group horizontally.
     * @param ?Orientation $orientation
     * @return string html
     */
    public function render(?Orientation $orientation = null): string
    {
        if ($orientation !== null) {
            $this->orientation = $orientation;
        }
        $this->addCssClass($this->blockClass(), $this->blockClass(modifier: $this->orientation->value));
        $html = '<div'.($this->id ? ' id="'.$this->id.'"' : '').$this->renderCssClass().'>';
        foreach ($this->radios as $radio) {
            $html .= $radio->render();
        }
        $html .= '</div>';

        return $html;
    }
}