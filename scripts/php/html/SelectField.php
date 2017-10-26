<?php

namespace WebsiteTemplate\html;

/**
 * This class creates a HtmlSelectElement.
 */
class SelectField extends Form
{

    /** use the option text to set the option title attribute */
    const OPTION_TITLE_FROM_TEXT = 1;

    /** use the option value to set the option title attribute */
    const OPTION_TITLE_FROM_VALUE = 2;

    /** Use the value attribute to set HTMLOptionElement to selected. */
    const SELECTED_BY_VALUE = 1;

    /** Use the child text to set HTMLOptionElement to selected. */
    const SELECTED_BY_TEXT = 2;

    /** Render all elements */
    const RENDER_ALL = 1;

    /** Render only the option elements without the Select element. */
    const RENDER_OPTION_ONLY = 2;

    /** @var OptionElement[] array holding option elements */
    public $arrOption = array();

    /** @var bool multiple attribute */
    private $multiple = false;

    /** @var int number of visible rows */
    // note: this should be set to at least 2, when you want to use css height and not have multiple = true
    private $size = 1;

    /** @var array contains the selected text and values*/
    private $selectedOptions = array();

    /** @var string first item in select field */
    private $defaultText = 'Bitte auswählen';

    private $autoOptionTitle = false;

    /** @var bool auto index option values if arrOption is 1-dim array */
    public $autoOptionValues = true;

    /**
     * Construct a SelectFld object.
     *
     * The constructor accepts either a one or a two dimensional array. In case of a
     * 1-dim array, a new, zero-based index is created to use as the HTMLValueAttribute and the
     * array values are used as the text of the HTMLOptionElements. Otherwise the first dimension
     * is used as the value, and the second as the text.
     *
     * @param array $arrOption text and value data
     * @param string|null $id
     */
    public function __construct($arrOption, $id = null)
    {
        if(!is_null($id)) {
            $this->setId($id);
            $this->name = $id;
        };
        $this->initOptions($arrOption);
    }

    /**
     * Create array of option elements
     * If argument $options is a 1-dim array: use created index as value attribute
     * If arguments $options is a 2-dim array: use first index as value attribute, but only
     * if autoOptionValues is true, otherwise no value attibute is set.
     * @param array $options
     */
    private function initOptions($options) {
        $i = 0;
        foreach ($options as $key => $row) {
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
     * @param boolean $multiple
     */
    public function setMultiple($multiple = true)
    {
        $this->multiple = $multiple;
    }

    /**
     * Set the number of visible rows.
     * Note: this is independent of multiple attribute
     * @param $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * Set a HTMLOptionElement to selected.
     * If no type is given, the value attribute is used to set item selected. If type = HTML_OPTION_TEXT then
     * the option text is used to set selected.
     * @param string|bool $val
     * @param int $type SelectField::SELECTED_BY_VALUE | SelectField::SELECTED_BY_TEXT
     */
    public function setSelected($val = false, $type = SelectField::SELECTED_BY_VALUE)
    {
        // kind a useless?
        if ($val === false) {
            $this->selected = false;
        } else {
            $this->selected = true;
        }

        if (!$val) {
            $this->selectedOptions = array();
        }

        foreach ($this->arrOption as $option) {
            if (!$val) {
                // deselect all
                $option->selected = false;
            }
            else {
                $testVal = $type === SelectField::SELECTED_BY_TEXT ? $option->text : $option->value;
                if ($val === $testVal){
                    $option->selected = true;
                    array_push($this->selectedOptions, $option);
                }
            }
        }
    }

    /**
     * Returns the first selected value or text
     * @param null $type SelectField::SELECTED_BY_VALUE | SelectField::SELECTED_BY_TEXT
     * @return bool|string
     */
    public function getSelected($type = null)
    {
        foreach ($this->arrOption as $option) {
            if ($option->selected) {
                return $type === SelectField::SELECTED_BY_TEXT ? $option->text : $option->value;
            }
        }

        return false;
    }

    /**
     * Set the name attribute independent of the id attribute.
     * If you use [] in you id or name attribute, then PHP makes it available as an array after posting.
     * But [] is not valid as an id in JS and HTML.
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Overwrite the default text of the first item in the list.
     * Default text value is 'Bitte auswählen'.
     * Set to false if no default item (first item in the list) should be displayed.
     *
     * @param string|bool $txt
     */
    public function setDefaultVal($txt)
    {
        $this->defaultText = $txt;
    }

    /**
     * Print the HTMLSelectElement.
     * @param int $type render all elements or specific elements only
     * @return string Html
     */
    public function render($type = SelectField::RENDER_ALL)
    {
        $strHtml = '';
        if ($type === SelectField::RENDER_ALL) {
            $strHtml .= $this->labelPosition === Form::LABEL_BEFORE ? $this->renderLabel() : '';
            $strHtml .= $this->renderSelect();
        }
        $strHtml .= $this->renderOptions();
        if ($type === SelectField::RENDER_ALL) {
            $strHtml .= '</select>';
            $strHtml .= $this->labelPosition === Form::LABEL_AFTER ? $this->renderLabel() : '';
        }

        return $strHtml;
    }

    /**
     *  Render HTML select element.
     * @return string
     */
    private function renderSelect()
    {
        $str = '<select'.($this->id ? ' id="'.$this->getId().'"' : '').($this->name ? ' name="'.$this->name.'"' : '');
        if ($this->cssClass) {
            $str .= ' class="'.$this->cssClass.'"';
        }
        if ($this->cssStyle) {
            $str .= ' style="'.$this->cssStyle.'"';
        }
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
        if ($this->required) {
            $str .= ' required="required"';
        }
        $str .= ">\n";

        return $str;
    }

    /**
     * Render HTML option element.
     * @param string $value
     * @param string $text
     * @return string HTML
     */
    private function renderOption($value = '', $text)
    {
        $str = '';
        $sel = '';
        $title = '';
        if ($this->selected) {
            foreach ($this->selectedVal as $selected) {
                $comp = $this->useTxt ? $text : $value;
                if ($comp == $selected) {
                    $sel .= ' selected="selected"';
                }
            }
        }
        $value = ' value="'.$value.'"';
        if (isset($this->autoOptionTitle)) {
            $title = $this->autoOptionTitle === SelectField::OPTION_TITLE_FROM_TEXT ? $text : $value;
            $title = ' title="'.$title.'"';
        }
        $str .= '<option'.$value.$sel.$title.'>'.$text.'</option>';

        return $str;
    }

    /**
     * Render HTML option elements.
     * @return string
     */
    private function renderOptions()
    {
        $str = '';
        if ($this->defaultText) {
            $str .= $this->renderOption('', $this->defaultText);
        }
        $i = 0;
        foreach ($this->arrOption as $row) {
            // 1-dim array: use created index as value attribute
            // 2-dim array: use first index as value attribute
            if (count($row) > 1) {
                $val = $row[0];
                $text = $row[1];
            } else {
                $val = $i++;
                $text = $row;
            }
            $str .= $this->renderOption($val, $text);
        }

        return $str;
    }

    /**
     * Render HTML for label element.
     * @return string
     */
    private function renderLabel()
    {
        $str = '';
        if ($this->label) {
            $str .= '<label for="'.$this->getId().'"';
            if ($this->cssClass) {
                $str .= ' class="label'.$this->cssClass.'"';
            }
            $str .= '>'.$this->getLabel().'</label>';
        }

        return $str;
    }

    /**
     * Return default text of first unselected option.
     * @return string|bool
     */
    public function getDefaultVal()
    {
        return $this->defaultText;
    }

    /**
     * Automatically sets the title attribute on the option element from the option text.
     * @param int $type
     */
    public function setAutoOptionTitle($type = SelectField::OPTION_TITLE_FROM_TEXT)
    {
        $this->autoOptionTitle = $type;
    }

}