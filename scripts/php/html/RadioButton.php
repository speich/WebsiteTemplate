<?php
namespace WebsiteTemplate;

require_once 'Form.php';


/**
 * Class to create a HTMLRadioElement.
 * Often radio buttons occur in group with the same name. Use the setGroup method.
 * @see setGroup() method
 */
class RadioButton extends Form {
	/** @var string value attribute */
	public $val;

	/** @var bool group buttons with same name? */
	private $grouped = false;

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
		$this->name = $name;
		$this->grouped = true;
	}

	/**
	 * Return the value of the HTMLAttribute name.
	 * @see setGroup() method
	 * @return string|bool value or false
	 */
	public function getGroup() {
		return $this->grouped ? $this->name : false;
	}

	/**
	 * Print out the HTML radio button.
	 * @return string Html
	 */
	public function render() {
		$strHtml = '';
		$strLabel = '';

		if ($this->label) {
			$strLabel.= '<label for="'.$this->getId().'"';
			if ($this->cssClass) {
				$strLabel.= ' class="label'.$this->cssClass.'"';
			}
			$strLabel.= '>'.$this->getLabel().'</label>';
		}

		if ($this->labelPosition === Form::LABEL_BEFORE) {
			$strHtml.= $strLabel;
		}
		$strHtml.= '<input id="'.$this->getId().'"';
		if ($this->grouped === true) {
			$strHtml.= ' name="'.$this->getGroup().'"';
		}
		$strHtml.= ' type="radio" value="'.$this->val.'"';
		if ($this->selected === true) {
			$strHtml.= ' checked="checked"';
		}
		if ($this->disabled === true) {
			$strHtml.= ' disabled="disabled"';
		}
		if ($this->tabIndex) {
			$strHtml.= ' tabindex="'.$this->tabIndex.'"';
		}
		if ($this->cssClass) {
			$strHtml.= ' class="'.$this->cssClass.'"';
		}
		if ($this->cssStyle) {
			$strHtml.= ' style="'.$this->cssStyle.'"';
		}
		if ($this->required === true) {
			$strHtml.= ' required="required"';
		}
		$strHtml.= ">";
		if ($this->labelPosition === Form::LABEL_AFTER) {
			$strHtml.= $strLabel;
		}
		return $strHtml;
	}
}