<?php

namespace WebsiteTemplate\html;

/**
 * Class RadioGroup
 * Creates HTMLRadioElements
 * @package WebsiteTemplate\html
 */
class RadioGroup extends Form
{
    public const RENDER_HORIZONTALLY = 1;

    public const RENDER_VERTICALLY = 2;

    public string $cssClassMain = 'radiogroup';

    public string $cssClassVertical = 'layout-vertical';

    /** @var RadioButton[] */
    public array $radios = [];

    /**
     * RadioGroup constructor.
     * Uses the $name attribute to create the group of radios with same name.
     * Keys of $values array will be used to index the id attribute together with the name attribute
     * @param string $name
     * @param array $values
     */
    public function __construct(string $name, array $values)
    {
        foreach ($values as $key => $value) {
            $radio = new RadioButton($name.(++$key), $value);
            $radio->setName($name);
            $this->radios[] = $radio;
        }
    }

    /**
     * @param array $labels
     * @param ?int $position
     */
    public function setLabels(array $labels, ?int $position = null): void
    {
        $position = $position ?? Form::LABEL_BEFORE;
        foreach ($labels as $key => $label) {
            $this->radios[$key]->setLabel($label, $position);
        }
    }

    /**
     * @param array $indices
     */
    public function setTabIndices(array $indices): void
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
    public function setChecked(string $value): void
    {
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
    public function setDisabled(?bool $bool = null): void
    {
        foreach ($this->radios as $radio) {
            $radio->setDisabled($bool);
        }
    }

    /**
     * Render the radio button group as html.
     * Sets a css class which renders the group horizontally.
     * @param ?int $layout self::RENDER_VERTICALLY or self::RENDER_HORIZONTALLY
     * @return string html
     */
    public function render(int $layout = null): string
    {
        $this->addCssClass($this->cssClassMain);
        if ($layout === self::RENDER_VERTICALLY) {
            $this->addCssClass($this->cssClassVertical);
        }
        $html = '<div'.($this->id ? ' id="'.$this->getId().'"' : '').$this->renderCssClass().'>';
        foreach ($this->radios as $radio) {
            $html .= $radio->render();
        }
        $html .= '</div>';

        return $html;
    }
}