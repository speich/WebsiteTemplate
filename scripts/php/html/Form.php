<?php
namespace WebsiteTemplate;

require_once 'Html.php';


/** Render the label before the form element */
define('HTML_LABEL_BEFORE', 'before');

/** Render the label after the form element */
define('HTML_LABEL_AFTER', 'after');

/**
 * Class to create HTMLFormElements.
 *
 * This base class defines a number of attributes and methods to deal with
 * HTMLAttributes that are shared by all HTMLFormElements, such as the label, disabled, selected attribute.
 */
class Form extends Html {

	/** @var bool renderAsHtml label attribute */
	protected $label = false;

	/** @var string label */
	protected $labelName = '';

	/** @var string position of label in relation to element */
	protected $labelPosition = HTML_LABEL_BEFORE;

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
	public function setDisabled($bool = true) {
		$this->disabled = $bool;
	}

	/**
	 * Set the form element label.
	 * If set then the label attribute is rendered. The position can be set to before or after with the constants
	 * HTML_LABEL_BEFORE and HTML_LABEL_AFTER.
	 * @param string $label label
	 * @param string $position position of the label
	 */
	public function setLabel($label, $position = null) {
		$this->labelName = $label;
		$this->label = true;
		$this->labelPosition = is_null($position) ? $this->labelPosition : $position;
	}

	/**
	 * Remove form element label.
	 */
	public function removeLabel() {
		$this->labelName = null;
		$this->label = false;
		$this->labelPosition = HTML_LABEL_BEFORE;
	}

	/**
	 * Set the form element or item to be selected.
	 * If set then the HTMLFormElement is rendered selected.
	 * @param bool $selected
	 */
	public function setSelected($selected = true) {
		$this->selected = (bool)$selected;
	}

	/**
	 * Return the label of the HTMLFormElement.
	 * @return string|bool label or false
	 */
	public function getLabel() {
		if ($this->label) {
			return $this->labelName;
		}
		else {
			return false;
		}
	}

	/**
	 * Set HTMLAttribute required to true or false.
	 * @param $bool
	 */
	public function setRequired($bool = true) {
		$this->required = $bool;
	}
}