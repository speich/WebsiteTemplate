<?php

namespace WebsiteTemplate\html;

/**
 * Class RadioGroup
 * Creates HTMLRadioElements
 * @package WebsiteTemplate\html
 */
class RadioGroup extends Form
{
    const RENDER_HORIZONTALLY = 1;

    const RENDER_VERTICALLY = 2;

    public $cssClass = 'radiogroup';

    public $cssClassVertical = 'layout-vertical';

    /** @var RadioButton[] */
    public $radios = [];

    /**
     * RadioGroup constructor.
     * Uses the $name attribute to create the group of radios with same name.
     * Keys of $values array will be used to index the id attribute together with the name attribute
     * @param string $name
     * @param array $values
     */
    public function __construct($name, $values)
    {
        foreach ($values as $key => $value) {
            $radio = new RadioButton($name.(++$key), $value);
            $radio->setName($name);
            $this->radios[] = $radio;
        }
    }

    /**
     * @param array $labels
     * @param int $position
     */
    public function setLabels($labels, $position = Form::LABEL_BEFORE)
    {
        foreach ($labels as $key => $label) {
            $this->radios[$key]->setLabel($label, $position);
        }
    }

    /**
     * @param $indices
     */
    public function setTabIndices($indices)
    {
        foreach ($indices as $key => $index) {
            $this->radios[$key]->setTabIndex($index);
        }
    }

    /**
     * Set a radio button of the group to checked.
     * Sets the radio button, where the value attribute equals the parameter $value to checked.
     * Note: uses strict comparison
     * @param string $value value to set checked
     */
    public function setChecked($value) {
        foreach ($this->radios as $radio) {
            if ($radio->val === $value) {
                $radio->setChecked();
            }
            else {
                $radio->setChecked(false);
            }
        }
    }

    /**
     * Set all radios to disabled.
     * If set to true the HTMLFormAttribute disabled="disabled" is rendered
     * and the element is disabled by the browser.
     * @param bool $bool
     */
    public function setDisabled($bool = true)
    {
        foreach ($this->radios as $radio) {
            $radio->setDisabled($bool);
        }
    }

    /**
     * Render the radio button group as html
     * @param int $layout
     * @return string html
     */
    public function render($layout = RadioGroup::RENDER_HORIZONTALLY) {
        $this->setCssClass($this->cssClass);
        if ($layout === self::RENDER_VERTICALLY) {
            $this->setCssClass($this->cssClassVertical);
        }
        $html = '<div'.($this->id ? ' id="'.$this->getId().'"' : '').$this->renderCssClass().'>';
        foreach ($this->radios as $radio) {
            $html .= $radio->render();
        }
        $html .= '</div>';

        return $html;
    }
}