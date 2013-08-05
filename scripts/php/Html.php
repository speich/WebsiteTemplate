<?php
/**
 * This file contains several classes to create often used HTML snippets such as form elements.
 * 
 * @author Simon Speich
 * @copyright You can use and alter this freely as long as you list the author.
 * @version 1.0, 04.07.2007
 */
namespace WebsiteTemplate;

/**
 * Use the value attribute to set HTMLOptionElement to selected.
 * @see function SetSelected()
 */
const HTML_OPTION_VALUE = 'value';

/**
 * Use the child text to set HTMLOptionElement to selected.
 * @see function SetSelected()
 */
const HTML_OPTION_TEXT = 'txt';

/**
 * Base class to create HTML snippets.
 * 
 * This abstract base class defines a number of attributes and methods
 * to deal with HTMLAttributes that are shared by all HTMLElements, 
 * such as the style, id, name and class attribute.
 */
abstract class Html {
	protected $id = false;
	protected $cssClass = false;
	protected $cssStyle = false;

	/**
	 * Set the id attribute of a HTMLElement.
	 * @param string $id
	 */
	public function setId($id) { $this->id = $id; }
	
	/**
	 * Return the id attribute of a HTMLElement.
	 * @return string|false
	 */
	public function getId() {
		if ($this->id) { return $this->id; }
		else { return false; }
	}
	
	/**
	 * Set the class attribute of a HTMLElement.
	 * @param string $Class
	 */
	public function setCssClass($Class) { $this->cssClass = $Class; }
	
	/**
	 * Return the class attribute of a HTMLElement.
	 * @return string|false
	 */
	public function getCssClass() {
		if ($this->cssClass) { return $this->cssClass; }
		else { return false; }
	}
	
	/**
	 * Set the style attribute of a HTMLElement.
	 * @param string $style
	 */
	public function setCssStyle($style) { $this->cssStyle = $style; }
	
	/**
	 * Return the style atribute of HTMLElement.
	 * @return string|false
	 */
	public function getCssStyle() {
		if ($this->cssStyle) { return $this->cssStyle; }
		else { return false; }
	}
}

/**
 * Class to create a list of HTMLDivElements.
 * 
 * Mimics a HTMLSelectElement, but constructed with HTMLDivElements.
 * You can pass to the constructor either a 1-dim array or a 2-dim array.
 * In the second case the first item would be used to set anchor attribute.
 */
class HtmlDivList extends Html {
	private $label = false;
	private $labelName;
	
	/**
	 * Construct a HtmlDivList object.
	 * @param string $id Id of container element
	 * @param array $arrItem Array of elements
	 */
	public function __construct($id, $arrItem) {
		$this->setId($id);
		$this->arrItem = $arrItem;
	}
	
	/**
	 * Label the list of div elements.
	 * @param string $label
	 */
	public function setLabel($label) {
		$this->labelName = $label;
		$this->label = true;
	}
	
	/**
	 * Print the HTML list of div elements.
	 */
	public function render() {
		echo '<div id="'.$this->getId().'"';
		if ($this->cssClass) { echo ' class="'.$this->cssClass.'"'; }
		if ($this->cssStyle) { echo ' style="'.$this->cssStyle.'"'; }
		echo ">\n";
		if ($this->label) { echo '<div>'.$this->labelName."</div>\n"; }
		foreach ($this->arrItem as $item) {
			if (is_array($item)) {
				echo '<div><a href="'.$item[0].'">'.$item[1]."</a></div>\n";
			}
			else {
				echo '<div>'.$item.'</div>'."\n";
			}
		}
		echo "</div>\n";
	}
}

/**
 * Class to create HTMLFormElements.
 * 
 * This base class defines a number of attributes and methods to deal with
 * HTMLAttributes that are shared by all HTMLFormElements, such as the label, disabled, selected attribute.
 */
class Form extends Html {
	// form elements always need an Id 
	protected $label = false;
	protected $labelName;
	protected $disabled = false;
	protected $selected = false;
	
	/**
	 * Set a form element to disabled.
	 * If set to true the HTMLFormAttribute disabled="disabled" is rendered
	 * and the element is disabled by the browser.
	 * @param bool $bool
	 */
	public function setDisabled($bool) { $this->disabled = $bool; }
	
	/**
	 * Set the form element label.
	 * If set then the label attribute is rendered.
	 * @param string $label
	 */
	public function setLabel($label) {
		$this->labelName = $label;
		$this->label = true;
	}
	
	/**
	 * Remove form element label.
	 */
	public function removeLabel() {
		$this->labelName = null;
		$this->label = false;
	}
	
	/**
	 * Set the form element or item to be selected.
	 * If set then the HTMLFormElement is rendered selected.
	 * @param bool $selected
	 */
	public function setSelected($selected = true) { $this->selected = $selected; }
	
	/**
	 * Return the label of the HTMLFormElement.
	 * @return string|false
	 */
	public function getLabel() {
		if ($this->label) { return $this->labelName; }
		else { return false; }
	}
	
}

/**
 * Class to create a HTMLRadioElement.
 * Often radio buttons occur in group with the same name. Use the setGroup method.
 * @see setGroup() method
 */
class HtmlRadioButton extends Form {
	private $val;
	private $grouped = false;	// radio buttons can be in groupes with the same name
	private $groupName;			// name of radio group
	
	/**
	 * Construct a HtmlRadioButton object.
	 * The constructor sets the id and value attribute of the HTMLRadioElement.
	 * @param string $id HTMLAttribute id
	 * @param string $val HTMLAttribute value
	 */
	public function __construct($id, $val) {
		$this->setId($id);
		$this->val = $val;
	}
	
	/**
	 * Set the name attribute of the radio element.
	 * If radio elements share the same name, clicking one radio deselects
	 * the other radios having the same name (the same group).
	 * @param string $name HTMLAttribute name
	 */
	public function setGroup($name) {
		$this->groupName = $name;
		$this->grouped = true;
	}
	
	/**
	 * Return the value of the HTMLAttribute name.
	 * @see SetGroup method.
	 * @return string|false
	 */
	public function getGroup() {
		if ($this->grouped) { return $this->groupName; }
		else { return false; }
	}
	
	/**
	 * Print out the HTML radio button.
	 */
	function render() {
		if ($this->label) {	echo '<label for="'.$this->getId().'">'.$this->getLabel()."</label>\n"; }
		echo '<input id="'.$this->getId().'"';
		if ($this->grouped) { echo ' name="'.$this->getGroup().'"'; }
		echo ' type="radio" value="'.$this->val.'"';
		if ($this->selected) { echo ' checked="checked"'; }
		if ($this->disabled) { echo ' disabled="disabled"'; }
		if ($this->cssClass) { echo ' class="'.$this->cssClass.'"'; }
		if ($this->cssStyle) { echo ' style="'.$this->cssStyle.'"'; }
		echo " />";
	}
}

/**
 * This class creates a HtmlSelectElement.
 */
class HtmlSelectFld extends Form {
	private $arrOption;
	private $multiple = false;
	
	// set selected field
	private $useTxt = false;
	private $selectedVal = array(array());		// store value/txt to compare, can be an array of values
	private $defaultVal = 'Bitte auswählen';	// first item in select field
	private $name = "";								// set name attribute separately, default = Id in constructor
	// e.g. id="Test" name="Test[]" post PHP array [] not valid JS as an id

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
	 * @param string $type use text/value
	 * @return bool
	 */
	public function setSelected($val = false, $type = NULL) {
		if ($val === false) { $this->selected = false; }
		else { $this->selected = true; }
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
	public function setName($name) { $this->name = $name; }

	/**
	 * Overwrite the default text of the first item in the list.
	 * Default text value is 'Bitte auswählen'.
	 * Set to false if no default item (first item in the list) should be displayed.
	 *
	 * @param string|false $txt
	 */
	public function setDefaultVal($txt) {
		$this->defaultVal = $txt;
	}

	/**
	 * Return default text of first unselected option.
	 * @return string|bool
	 */
	public function getDefaultVal() {
		return $this->defaultVal;
	}

	/**
	 * Print the HTMLSelectElement.
	 */
	public function render() {
		if ($this->label) {	echo '<label for="'.$this->getId().'">'.$this->getLabel()."</label>\n"; }
		$html = '<select id="'.$this->getId().'" name="'.$this->name.'"';
		if ($this->cssClass) { $html .= ' class="'.$this->cssClass.'"'; }
		if ($this->cssStyle) { $html .= ' style="'.$this->cssStyle.'"'; }
		if ($this->multiple) { $html .= ' multiple="multiple"'; }
		if ($this->disabled) { $html .= ' disabled="disabled"'; }
		$html .= ">\n";
		if ($this->defaultVal) { $html .= '<option value="">'.$this->defaultVal.'</option>'; }
		$i = 0;
		if (count($this->arrOption) == 0) { $html .= '<option></option>'; }
		// if data is a 1-dim array: use created index as value attribute
		// else 2-dim array: use first index as value attribute. Array keys can either be integer or assoc.
		foreach ($this->arrOption as $row) {
			if (is_array($row)) {
				$val = $row[key($row)];
				next($row);
				$text = $row[key($row)];
			}
			else {
				$val = $i++;
				$text = $row;
			}
			reset($row);
			$html.= '<option value="'.$val.'"'.($this->select($row) ? ' selected="selected"' : '').'>'.$text."</option>\n";
		}
		$html .= "</select>\n";
		echo $html;
	}

	/**
	 * Set HTML selected attribute.
	 * @param array $row
	 * @return bool
	 */
	protected function select($row) {
		if ($this->selected) {
			foreach ($this->selectedVal as $selected) {
				if (!$this->useTxt && $row[key($row)] == $selected) {
					return true;
				}
				else {
					next($row);
					// use strict to prevent value="0" to be set as selected if passing false as $Val
					if ($row[key($row)] == $selected) {
						return true;
					}
				}
			}
		}
		return false;
	}
}

/**
 * Create a HTMLSelectElement.
 */
class HtmlCheckBox extends Form {
	
	/**
	 * Construct a HtmlCheckBox object.
	 * Sets the id attribute and the value attribute.
	 * @param string $id
	 * @param string $val
	 */
	public function __construct($id, $val) {
		$this->setId($id);
		$this->val = $val;
		$this->name = $id;
	}

	/**
	 * Print the HTMLCheckboxElement.
	 */
	function render() {
		if ($this->label) {	echo '<label for="'.$this->getId().'">'.$this->getLabel()."</label>\n"; }
		echo '<input id="'.$this->getId().'" name="'.$this->name.'"';
		echo ' type="checkbox" value="'.$this->val.'"';
		if ($this->selected) { echo ' checked="checked"'; }
		if ($this->disabled) { echo ' disabled="disabled"'; }
		if ($this->cssClass) { echo ' class="'.$this->cssClass.'"'; }
		if ($this->cssStyle) { echo ' style="'.$this->cssStyle.'"'; }
		echo " />";
	}
}