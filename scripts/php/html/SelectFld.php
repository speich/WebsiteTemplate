<?php
namespace WebsiteTemplate;

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
 * @see function HtmlSelectFld::Render()
 */
define("HTML_OPTION_ONLY", true);


/**
 * This class creates a HtmlSelectElement.
 */
class SelectFld extends Form {

	/** @var array array holding option element value and text */
	public $arrOption = array();

	/** @var bool multiple attribute */
	private $multiple = false;

	/** @var bool use option text to set selected instead of value attribute */
	private $useTxt = false;

	/** @var array  value/txt to compare when setting selected, can be an array of values */
	private $selectedVal = array(array());

	/** @var string first item in select field */
	private $defaultVal = 'Bitte auswählen';

	/** @var string name attribute, default == id attribute */
	private $name = "";

	/**
	 * Construct a HtmlSelectFld object.
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
		$this->defaultVal = $txt;
	}

	/**
	 * Print the HTMLSelectElement.
	 * @param string $type renderAsHtml option elements only
	 * @return string Html
	 */
	public function render($type = NULL) {
		$strHtml = '';
		if ($type != HTML_OPTION_ONLY) {
			if ($this->label) {
				$strHtml.= '<label for="'.$this->getId().'"';
				if ($this->cssClass) {
					$strHtml.= ' class="label'.$this->cssClass.'"';
				}
				$strHtml.= '>'.$this->getLabel().'</label>';
			}

			$strHtml.= '<select id="'.$this->getId().'" name="'.$this->name.'"';
			if ($this->cssClass) {
				$strHtml.= ' class="'.$this->cssClass.'"';
			}
			if ($this->cssStyle) {
				$strHtml.= ' style="'.$this->cssStyle.'"';
			}
			if ($this->multiple) {
				$strHtml.= ' multiple="multiple"';
			}
			if ($this->disabled) {
				$strHtml.= ' disabled="disabled"';
			}
			if ($this->tabIndex) {
				$strHtml.= ' tabindex="'.$this->tabIndex.'"';
			}
			if ($this->required) {
				$strHtml.= ' required="required"';
			}
			$strHtml.= ">\n";
		}
		if ($this->defaultVal) {
			$strHtml.= '<option value="">'.$this->defaultVal.'</option>';
		}
		$i = 0;
		foreach ($this->arrOption as $row) {
			// 1-dim array: use created index as value attribute
			// 2-dim array: use first index as value attribute
			if (count($row) > 1) {
				$val = $row[0];
				$text = $row[1];
				$sel = '';
			}
			else {
				$val = $i++;
				$text = $row;
				$sel = '';
			}
			if ($this->selected) {
				foreach ($this->selectedVal as $selected) {
					if ($this->useTxt) {
						// use strict to prevent value="0" to be set as selected if passing false as $Val
						if ($row[1] == $selected) {
							$sel = ' selected="selected"';
						}
					}
					else if ($row[0] == $selected) {
						$sel = ' selected="selected"';
					}
				}
			}
			$strHtml.= '<option value="'.$val.'"'.$sel.'>'.$text."</option>\n";
		}
		if ($type != HTML_OPTION_ONLY) {
			$strHtml.= "</select>\n";
		}
		return $strHtml;
	}

	/**
	 * Return default text of first unselected option.
	 * @return string|bool
	 */
	public function getDefaultVal() {
		return $this->defaultVal;
	}
}