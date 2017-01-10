<?php
namespace WebsiteTemplate;

require_once 'Form.php';


/**
 * Use the value attribute to set HTMLOptionElement to selected.
 * @see function SetSelected()
 */
define("HTML_OPTION_VALUE", 'Value');

/**
 * Use the child text to set HTMLOptionElement to selected.
 * @see function SetSelected()
 */
define("HTML_OPTION_TEXT", 'Txt');

/**
 * Render only option data of HTMLSelectElement.
 * @see function SelectFld::Render()
 */
define("HTML_OPTION_ONLY", true);


/**
 * This class creates a HtmlSelectElement.
 */
class SelectField extends Form {

	/** @var array array holding option element value and text */
	public $arrOption = array();

	/** @var bool multiple attribute */
	private $multiple = false;

	/** @var int number of visible rows */
	// note: this should be set to at least 2, when you want to use css height and not have multiple = true
	private $size = 1;

	/** @var bool use option text to set selected instead of value attribute */
	private $useTxt = false;

	/** @var array  value/txt to compare when setting selected, can be an array of values */
	private $selectedVal = array(array());

	/** @var string first item in select field */
	private $defaultText = 'Bitte auswählen';

	/** @var string name attribute, default == id attribute */
	private $name = "";

	/**
	 * Construct a SelectFld object.
	 *
	 * The constructor accepts either a one or a two dimensional array. In case of a
	 * 1-dim array, a new, zero-based index is created to use as the HTMLValueAttribute and the
	 * array values are used as the text of the HTMLOptionElements. Otherwise the first dimension
	 * is used as the value, and the second as the text.
	 *
	 * @param string $id
	 * @param array $arrOption text and value data
	 */
	public function __construct($id, $arrOption) {
		$this->setId($id);
		$this->arrOption = $arrOption;
		$this->name = $id;
	}

	/**
	 * Set the HTMLMultipleAttribute to true.
	 * Set to false by default.
	 * @param boolean $multiple
	 */
	public function setMultiple($multiple = true) {
		$this->multiple = $multiple;
	}

	/**
	 * Set the number of visible rows.
	 * Note: this is independent of multiple attribute
	 * @param $size
	 */
	public function setSize($size) {
		$this->size = $size;
	}

	/**
	 * Set a HTMLOptionElement to selected.
	 * If no type is given, the value attribute is used to set item selected. If type = HTML_OPTION_TEXT then
	 * the option text is used to set selected.
	 * @param mixed $val
	 * @param string|NULL $type use text/value
	 * @return bool
	 */
	public function setSelected($val = false, $type = NULL) {
		if ($val === false) {
			$this->selected = false;
		}
		else {
			$this->selected = true;
		}
		if ($type == HTML_OPTION_TEXT) {
			$this->useTxt = true;
		}
		if (is_array($val)) {
			$this->selectedVal = $val;
		}
		else {
			$this->selectedVal = array($val);
		}
		return true;
	}

	/**
	 * Return selected values or texts.
	 * @param null $type
	 * @return array
	 */
	public function getSelected($type = null) {
		$arr = array();
		foreach ($this->arrOption as $option) {
			if ($this->select($option)) {
				$arr[] = $type == HTML_OPTION_TEXT ? $option[1] : $option[0];
	}
		}
		return $arr;
	}

	/**
	 * Set the name attribute independent of the id attribute.
	 * If you use [] in you id or name attribute, then PHP makes it available as an array after posting.
	 * But [] is not valid as an id in JS and HTML.
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * Overwrite the default text of the first item in the list.
	 * Default text value is 'Bitte auswählen'.
	 * Set to false if no default item (first item in the list) should be displayed.
	 *
	 * @param string|bool $txt
	 */
	public function setDefaultVal($txt) {
		$this->defaultText = $txt;
	}

	/**
	 * Print the HTMLSelectElement.
	 * @param string $type renderAsHtml option elements only
	 * @return string Html
	 */
	public function render($type = NULL) {
		$strHtml = '';
		if ($type != HTML_OPTION_ONLY) {
			$strHtml .= $this->renderLabel();
			$strHtml .= $this->renderSelect();
		}
		$strHtml .= $this->renderOptions();
		if ($type != HTML_OPTION_ONLY) {
			$strHtml .= '</select>';
		}

		return $strHtml;
	}

	/**
	 *  Render HTML select element.
	 * @return string
	 */
	private function renderSelect() {
		$str = '<select id="'.$this->getId().'" name="'.$this->name.'"';
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
	private function renderOption($value = '', $text) {
		$str = '';
		$sel = '';
		if ($this->selected) {
			foreach ($this->selectedVal as $selected) {
				$comp = $this->useTxt ? $text : $value;
				// use strict to prevent value="0" to be set as selected if passing false as $value
				if ($comp === $selected) {
					$sel .= ' selected="selected"';
				}
			}
		}
		$value = ' value="'.$value.'"';
		$str .= '<option'.$value.$sel.'>'.$text.'</option>';

		return $str;
	}

	/**
	 * Render HTML option elements.
	 * @return string
	 */
	private function renderOptions() {
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
			}
			else {
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
	private function renderLabel(){
		$str = '';
		if ($this->label) {
			$str.= '<label for="'.$this->getId().'"';
			if ($this->cssClass) {
				$str.= ' class="label'.$this->cssClass.'"';
			}
			$str.= '>'.$this->getLabel().'</label>';
		}

		return $str;
	}

	/**
	 * Return default text of first unselected option.
	 * @return string|bool
	 */
	public function getDefaultVal() {
		return $this->defaultText;
	}
}