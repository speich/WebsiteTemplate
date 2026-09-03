<?php

namespace WebsiteTemplate\Html;

use function array_key_exists;
use function count;

/**
 * This class creates an HtmlSelectElement.
 */
class SelectField extends Form
{
    /** @var OptionElement[] array holding option elements */
    public array $arrOption = [];

    /** @var bool automatically index the option values if the passed $arrOption is a 1-dim array */
    public bool $autoOptionValues = true;

    /** @var bool multiple attribute */
    public bool $multiple = false;

    /** @var int|null size atttibute */
    public ?int $size = null;

    /** @var array contains the selected text and values */
    private array $selectedOptions = [];

    /** @var string|null text of the first option in the list */
    public ?string $defaultText = null;

    /** @var string value of the first option element */
    public string $defaultValue = '';

    /** @var OptionTitleSource|null automatically set the option title attribute */
    public ?OptionTitleSource $autoOptionTitle = null;

    /** @var SelectRenderMode Default rendering behavior for this select field */
    public SelectRenderMode $renderMode = SelectRenderMode::All;

    /** @var OptionSelectionMethod Default method used for matching and retrieving selected options */
    public OptionSelectionMethod $selectionMethod = OptionSelectionMethod::Value;

    /** @var string key for the value if the option array is associative */
    public string $keyValue = 'value';

    /** @var string key for the text if the option array is associative */
    public string $keyText = 'text';

    public ?string $blockName = 'select';

    /**
     * Construct a SelectFld object.
     *
     * The constructor accepts either a one or a two-dimensional array.
     * In the case of a 1-dim array, a new, zero-based index is created to use as the HTMLValueAttribute and the
     * array values are used as the text of the HTMLOptionElements.
     * Otherwise, the first dimension is used as the value, and the second as the text.
     *
     * @param string $name name attribute
     * @param iterable $arrOption text and value data
     * @param bool $nameOnly if true, only the name attribute is set, otherwise the id attribute is set to the name attribute
     */
    public function __construct(string $name, iterable $arrOption, bool $nameOnly = true)
    {
        $this->name = $name;
        if (!$nameOnly) {
            $this->id = $name;
        }
        // already initializing here instead of only when rendering, allows setting an option selected.
        $this->initOptions($arrOption);
    }

    /**
     * Create an array of option elements
     * If the argument $options is a 1-dim array: created value attribute if autoOptionValues is true, otherwise no value attribute is set.
     * If the argument $options is a 2-dim array: use the first index as the value attribute, the second as text. If the array is associative,
     * the keys $keyValue and $keyText can be set to use the keys as value and text attribute.
     *
     * @param iterable $options
     */
    protected function initOptions(iterable $options): void
    {
        $i = 0;
        foreach ($options as $row) {
            $option = new OptionElement();
            if (is_array($row)) {
                $option->value = $row[0] ?? (array_key_exists($this->keyValue, $row) ? $row[$this->keyValue] : current($row));
                $option->text = $row[1] ?? (array_key_exists($this->keyText, $row) ? $row[$this->keyText] : next($row));
            } else {
                if ($this->autoOptionValues) {
                    $option->value = $i++;
                }
                $option->text = $row;
            }
            $this->arrOption[] = $option;
        }
    }


    /**
     * Set an HTMLOptionElement to selected.
     * Passing false or null deselects everything.
     * If no type is given, the value attribute is used to set the item selected. If type = HTML_OPTION_TEXT then
     * the option text is used to set selected.
     *
     * @param bool|string|null $val
     * @param OptionSelectionMethod|null $method
     */
    public function setSelected(bool|string|null $val = null, ?OptionSelectionMethod $method = null): void
    {
        $val = $val ?? false;
        $deselect = $val === false;
        if ($deselect) {
            $this->selectedOptions = [];
        }

        $method = $method ?? $this->selectionMethod;
        foreach ($this->arrOption as $option) {
            if ($deselect) {
                // deselect all
                $option->selected = false;
            } else {
                $testVal = $method === OptionSelectionMethod::Text ? $option->text : $option->value;
                if ($val === $testVal) {
                    $option->selected = true;
                    $this->selectedOptions[] = $option;
                }
            }
        }
    }

    /**
     * Returns the first selected value or text
     *
     * @param OptionSelectionMethod|null $method
     *
     * @return bool|string
     */
    public function getSelected(?OptionSelectionMethod $method = null): bool|string
    {
        $method = $method ?? $this->selectionMethod;

        foreach ($this->arrOption as $option) {
            if ($option->selected) {
                return $method === OptionSelectionMethod::Text ? $option->text : $option->value;
            }
        }

        return false;
    }

    /**
     * Returns the select option elements.
     *
     * @return OptionElement[]
     */
    public function getSelectedOptions(): array
    {
        return $this->selectedOptions;
    }

    /**
     * Print the HTMLSelectElement.
     *
     * @param SelectRenderMode|null $mode render option elements only
     *
     * @return string Html
     */
    public function render(?SelectRenderMode $mode = null): string
    {
        $mode = $mode ?? $this->renderMode;

        $options = $this->renderOptions();
        if ($mode === SelectRenderMode::All) {
            $element = $this->renderSelect().$options.'</select>';
        } else {
            $element = $options;
        }
        if ($this->label !== null) {
            $strHtml = $this->renderLabel($element);
        } else {
            $strHtml = $element;
        }

        return $strHtml;
    }

    /**
     * Render HTML option elements.
     *
     * @return string
     */
    private function renderOptions(): string
    {
        $str = '';
        if ($this->defaultText !== null) {
            $option = new OptionElement();
            $option->text = $this->defaultText;
            $option->value = $this->defaultValue;
            $str .= $option->render();
        }
        foreach ($this->arrOption as $option) {
            $this->setAutoOptionTitle($option);
            $str .= $option->render();
        }

        return $str;
    }

    /**
     * Automatically sets the title attribute on an option element based on the selected TitleSource.
     *
     * The title attribute acts as a native browser tooltip to improve UX when the user hovers over an option:
     * - TitleSource::Text: Useful for fixed-width select menus. If the CSS truncates long option text,
     *   the user can still read the full string on hover.
     * - TitleSource::Value: Allows the user to reveal the underlying hidden value attribute (such as a database ID).
     *
     * @param OptionElement $option The option element to modify.
     */
    protected function setAutoOptionTitle(OptionElement $option): void
    {
        if ($this->autoOptionTitle !== null) {
            $option->title = $this->autoOptionTitle === OptionTitleSource::Text ? $option->text : $option->value;
        }
    }

    /**
     * Render HTML select element.
     *
     * @return string
     */
    private function renderSelect(): string
    {
        $str = '<select'.($this->id ? ' id="'.$this->id.'"' : '').($this->name ? ' name="'.$this->name.'"' : '');
        if ($this->multiple) {
            $str .= ' multiple="multiple"';
        }
        if ($this->size !== null) {
            $str .= ' size="'.$this->size.'"';
        }
        if ($this->disabled) {
            $str .= ' disabled="disabled"';
        }
        if ($this->tabIndex !== null) {
            $str .= ' tabindex="'.$this->tabIndex.'"';
        }
        $str .= $this->renderCssClass();
        if ($this->required) {
            $str .= ' required="required"';
        }
        $str .= '>';

        return $str;
    }

}