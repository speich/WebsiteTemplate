<?php
/**
 * This file contains several classes to create often used HTML snippets such as form elements.
 * 
 * @author Simon Speich
 * @copyright You can use and alter this freely as long as you list the author.
 * @version 1.0, 04.07.2007
 * @package NAFIDAS
 */

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
 * Base class to create HTML snippets.
 * 
 * This abstract base class defines a number of attributes and methods
 * to deal with HTMLAttributes that are shared by all HTMLElements, 
 * such as the style, id, name and class attribute.
 * 
 * @package NAFIDAS
 */
abstract class Html {

	/** @var bool|string html id attribute */
	protected $id = false;

	/** @var bool|string html class attribute */
	protected $cssClass = false;

	/** @var bool|string html style attribute */
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
	 * @param string $class
	 */
	public function setCssClass($class) { $this->cssClass = $class; }
	
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
 * 
 * @package NAFIDAS
 */
class HtmlDivList extends Html {

	/** @var bool render label attribute */
	private $label = false;

	/** @var string label for list */
	private $labelName;
	
	/**
	 * Construct a HtmlDivList object
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
 * 
 * @package NAFIDAS
 */
class Form extends Html {

	/** @var bool render label attribute */
	protected $label = false;

	/** @var string label */
	protected $labelName;

	/** @var bool|string disabled attribute */
	protected $disabled = false;

	/** @var bool|string selected attribute */
	protected $selected = false;

	/** @var bool|string required attribute */
	protected $required = false;

	/** @var bool|integer tab index attribute */
	protected $tabIndex = false;

	/**
	 * Set the element's tab index
	 * @param integer $index
	 */
	public function setTabIndex($index) {
		$this->tabIndex = $index;
	}

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
	public function setSelected($selected = true) { $this->selected = (bool) $selected; }
	
	/**
	 * Return the label of the HTMLFormElement.
	 * @return string|false
	 */
	public function getLabel() {
		if ($this->label) { return $this->labelName; }
		else { return false; }
	}

	/**
	 * Set HTMLAttribute required to true or false.
	 * @param $bool
	 */
	public function setRequired($bool = true) {
		$this->required = $bool;
	}
	
}

/**
 * Class to create a HTMLRadioElement
 * Often radio buttons occur in group with the same name. Use the SetGroup method.
 * @package NAFIDAS
 * @see setGroup() method
 */
class HtmlRadioButton extends Form {
	/**
	 * Should radio buttons be grouped with same name?
	 * @var bool
	 */
	private $grouped = false;

	/**
	 * Name attribute of radio group
	 * @var string
	 */
	private $groupName;
	
	/**
	 * Construct a HtmlRadioButton object
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
	 * @see setGroup() method
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
		if ($this->label) {
			echo '<label for="'.$this->getId().'"';
			if ($this->cssClass) {
				echo ' class="label'.$this->cssClass.'"';
			}
			echo '>'.$this->getLabel()."</label>\n";
		}
		echo '<input id="'.$this->getId().'"';
		if ($this->grouped === true) { echo ' name="'.$this->getGroup().'"'; }
		echo ' type="radio" value="'.$this->val.'"';
		if ($this->selected === true) { echo ' checked="checked"'; }
		if ($this->disabled === true) { echo ' disabled="disabled"'; }
		if ($this->tabIndex) { echo ' tabindex="'.$this->tabIndex.'"'; }
		if ($this->cssClass) { echo ' class="'.$this->cssClass.'"'; }
		if ($this->cssStyle) { echo ' style="'.$this->cssStyle.'"'; }
		if ($this->required === true) { echo ' required="required"'; }
		echo ">";
	}
}

/**
 * This class creates a HtmlSelectElement.
 * @package NAFIDAS
 */
class HtmlSelectFld extends Form {

	/** @var array array holding option element value and text */
	private $arrOption;

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
	 */
	public function setMultiple() { $this->multiple = true; }

	/**
	 * Set a HTMLOptionElement to selected.
	 * If no type is given, the value attribute is used to set item selected.
	 * @param mixed $val
	 * @param const|NULL $type	use text/value
	 * @return bool
	 */
	public function setSelected($val = false, $type = NULL) {
		if ($val === false) { $this->selected = false; }
		else { $this->selected = true; }
		if ($type == HTML_OPTION_TEXT) { $this->useTxt = true; }
		if (is_array($val)) {
			$this->selectedVal = $val;
		}
		else {
			$this->selectedVal = array($val);
		}
		return true;
	}

	/**
	 * Returns options data as a two dim array with value and text.
	 * @return array
	 */
	public function getOptions() {
		return $this->arrOption;
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
	 * Print the HTMLSelectElement.
	 * @param const $type render option elements only
	 */
	public function render($type = NULL) {
		$html = '';
		if ($type != HTML_OPTION_ONLY) {
			if ($this->label) {
				echo '<label for="'.$this->getId().'"';
				if ($this->cssClass) {
					echo ' class="label'.$this->cssClass.'"';
				}
				echo '>'.$this->getLabel()."</label>\n";
			}

			$html.= '<select id="'.$this->getId().'" name="'.$this->name.'"';
			if ($this->cssClass) { $html.= ' class="'.$this->cssClass.'"'; }
			if ($this->cssStyle) { $html.= ' style="'.$this->cssStyle.'"'; }
			if ($this->multiple) { $html.= ' multiple="multiple"'; }
			if ($this->disabled) { $html.= ' disabled="disabled"'; }
			if ($this->tabIndex) { $html.= ' tabindex="'.$this->tabIndex.'"'; }
			if ($this->required) { $html.= ' required="required"'; }
			$html .= ">\n";
		}
		if ($this->defaultVal) { $html.= '<option value="">'.$this->defaultVal.'</option>'; }
		$i = 0;
		if (count($this->arrOption) == 0) { $html.= '<option></option>'; }
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
						if ($row[1] == $selected) { $sel = ' selected="selected"'; }
					}
					else if ($row[0] == $selected) {
						$sel = ' selected="selected"';
					}
				}
			}		
			$html.= '<option value="'.$val.'"'.$sel.'>'.$text."</option>\n";
		}
		if ($type != HTML_OPTION_ONLY) {
			$html.= "</select>\n";
		}
		echo $html;
	}
}

/**
 * Create a HTMLSelectElement.
 * @package NAFIDAS
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
		if ($this->label) {
			echo '<label for="'.$this->getId().'"';
			if ($this->cssClass) {
				echo ' class="label'.$this->cssClass.'"';
			}
			echo '>'.$this->getLabel()."</label>\n";
		}
		echo '<input id="'.$this->getId().'" name="'.$this->name.'"';
		echo ' type="checkbox" value="'.$this->val.'"';
		if ($this->selected === true) { echo ' checked="checked"'; }
		if ($this->disabled == true) { echo ' disabled="disabled"'; }
		if ($this->tabIndex) { echo ' tabindex="'.$this->tabIndex.'"'; }
		if ($this->cssClass) { echo ' class="'.$this->cssClass.'"'; }
		if ($this->cssStyle) { echo ' style="'.$this->cssStyle.'"'; }
		if ($this->required === true) { echo ' required="required"'; }
		echo ">";
	}
}