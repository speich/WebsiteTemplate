<?php

namespace WebsiteTemplate\Html;

use function count;


/**
 * This class creates an HtmlSelectElement.
 */
class SelectField extends Form
{

    /** use the option text to set the option title attribute */
    public const OPTION_TITLE_FROM_TEXT = 1;

    /** use the option value to set the option title attribute */
    public const OPTION_TITLE_FROM_VALUE = 2;

    /** Use the value attribute to set the HTMLOptionElement to selected. */
    public const SELECTED_BY_VALUE = 1;

    /** Use the child text to set HTMLOptionElement to selected. */
    public const SELECTED_BY_TEXT = 2;

    /** Render all elements */
    public const RENDER_ALL = 1;

    /** Render only the option elements without the Select element. */
    public const RENDER_OPTION_ONLY = 2;

    /** @var OptionElement[] array holding option elements */
    public array $arrOption = [];

    /** @var bool multiple attribute */
    private bool $multiple = false;

    /**
     * Note: this should be set to at least 2, when you want to use css height and not have multiple = true
     * @var int number of visible rows
     */
    private int $size = 1;

    /** @var array contains the selected text and values */
    private array $selectedOptions = [];

    /** @var false|string text of the first option */
    private string|bool $defaultText = 'Bitte auswählen';

    /** @var string value of the first option element  */
    private string $defaultValue = '';

    /** @var false|int automatically set the option title attribute from option text or value attribute */
    private false|int $autoOptionTitle = false;

    /** @var bool automatically index option values if passed $arrOption is a 1-dim array */
    public bool $autoOptionValues = true;

    /**
     * Construct a SelectFld object.
     *
     * The constructor accepts either a one or a two-dimensional array.
     * In the case of a 1-dim array, a new, zero-based index is created to use as the HTMLValueAttribute and the
     * array values are used as the text of the HTMLOptionElements.
     * Otherwise, the first dimension is used as the value, and the second as the text.
     *
     * @param iterable $arrOption text and value data
     * @param string|null $id
     */
    public function __construct(iterable $arrOption, ?string $id = null)
    {
        if ($id !== null) {
            $this->setId($id);
            $this->name = $id;
        }
        $this->initOptions($arrOption);
    }

    /**
     * Create an array of option elements
     * If argument $options is a 1-dim array: created value attribute if autoOptionValues is true, otherwise no value attribute is set.
     * If arguments $options is a 2-dim array: use the first index as the value attribute, the second as text.
     *
     * @param iterable $options
     */
    private function initOptions(iterable $options): void
    {
        $i = 0;
        foreach ($options as $row) {
            $option = new OptionElement();
            if (count($row) > 1) {
                $option->value = $row[0];
                $option->text = $row[1];
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
     * Set the HTMLMultipleAttribute to true.
     * Set to false by default.
     * @param ?bool $multiple
     */
    public function setMultiple(?bool $multiple = null): void
    {
        $this->multiple = $multiple ?? true;
    }

    /**
     * Set the number of visible rows.
     * Note: this is independent of multiple attribute
     * @param int $size
     */
    public function setSize(int $size): void
    {
        $this->size = $size;
    }

    /**
     * Set a HTMLOptionElement to selected.
     * Passing false or null deselects everything.
     * If no type is given, the value attribute is used to set item selected. If type = HTML_OPTION_TEXT then
     * the option text is used to set selected.
     * @param bool|string|null $val
     * @param ?int $type SelectField::SELECTED_BY_VALUE | SelectField::SELECTED_BY_TEXT
     */
    public function setSelected(bool|string $val = null, ?int $type = null): void
    {
        $val = $val ?? false;
        $deselect = $val === false;
        if ($deselect) {
            $this->selectedOptions = [];
        }

        $type = $type ?? self::SELECTED_BY_VALUE;
        foreach ($this->arrOption as $option) {
            if ($deselect) {
                // deselect all
                $option->selected = false;
            } else {
                $testVal = $type === self::SELECTED_BY_TEXT ? $option->text : $option->value;
                if ($val === $testVal) {
                    $option->selected = true;
                    $this->selectedOptions[] = $option;
                }
            }
        }
    }

    /**
     * Returns the first selected value or text
     * @param int|null $type SelectField::SELECTED_BY_VALUE | SelectField::SELECTED_BY_TEXT
     * @return bool|string
     */
    public function getSelected(?int $type = null): bool|string
    {
        foreach ($this->arrOption as $option) {
            if ($option->selected) {
                return $type === self::SELECTED_BY_TEXT ? $option->text : $option->value;
            }
        }

        return false;
    }

    /**
     * Returns the select option elements.
     * @return OptionElement[]
     */
    public function getSelectedOptions(): array
    {
        return $this->selectedOptions;
    }

    /**
     * Overwrite the default text of the first item in the list.
     * Default text value is 'Bitte auswählen'.
     * Set to false if no default item (first item in the list) should be displayed.
     *
     * @param false|string $txt
     */
    public function setDefaultText(false|string $txt): void
    {
        $this->defaultText = $txt;
    }

    /**
     * Print the HTMLSelectElement.
     * @param ?int $type render all elements or specific elements only
     * @return string Html
     */
    public function render(int $type = null): string
    {
        $type = $type ?? self::RENDER_ALL;
        $strHtml = '';
        if ($type === self::RENDER_ALL) {
            $strHtml .= $this->labelPosition === Form::LABEL_BEFORE ? $this->renderLabel() : '';
            $strHtml .= $this->renderSelect();
        }
        $strHtml .= $this->renderOptions();
        if ($type === self::RENDER_ALL) {
            $strHtml .= '</select>';
            $strHtml .= $this->labelPosition === Form::LABEL_AFTER ? $this->renderLabel() : '';
        }

        return $strHtml;
    }

    /**
     * Render HTML select element.
     * @return string
     */
    private function renderSelect(): string
    {
        $str = '<select'.($this->id ? ' id="'.$this->getId().'"' : '').($this->name ? ' name="'.$this->name.'"' : '');
        if ($this->multiple) {
            $str .= ' multiple="multiple"';
        }
        if ($this->size > 1) {
            $str .= ' size="'.$this->size.'"';
        }
        if ($this->disabled) {
            $str .= ' disabled="disabled"';
        }
        if ($this->tabIndex) {
            $str .= ' tabindex="'.$this->tabIndex.'"';
        }
        $str .= $this->renderCssClass();
        if ($this->required) {
            $str .= ' required="required"';
        }
        $str .= '>';

        return $str;
    }

    /**
     * Render HTML option elements.
     * @return string
     */
    private function renderOptions(): string
    {
        $str = '';
        if ($this->defaultText !== false) {
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
     * Render HTML for label element.
     * @return string
     */
    private function renderLabel(): string
    {
        $str = '';
        if ($this->label) {
            $str .= '<label for="'.$this->getId().'" '.$this->renderCssClass().'>'.$this->getLabel().'</label>';
        }

        return $str;
    }

    /**
     * Return default text of first unselected option.
     * @return false|string
     */
    public function getDefaultText(): false|string
    {
        return $this->defaultText;
    }

    /**
     * Enable setting the title attribute on the option element automatically.
     * Title can be set from the option text or option value attribute.
     * @param int $type SelectField::OPTION_TITLE_FROM_TEXT | SelectField::OPTION_TITLE_FROM_VALUE
     */
    public function setOptionTitleAuto(int $type = self::OPTION_TITLE_FROM_TEXT): void
    {
        $this->autoOptionTitle = $type;
    }

    /**
     * Set the title attribute automatically.
     * @param OptionElement $option
     */
    protected function setAutoOptionTitle(OptionElement $option): void
    {
        if ($this->autoOptionTitle !== false) {
            $option->title = $this->autoOptionTitle === self::OPTION_TITLE_FROM_TEXT ? $option->text : $option->value;
        }
    }
}