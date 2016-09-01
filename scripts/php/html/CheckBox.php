<?php
namespace WebsiteTemplate;

require_once 'Form.php';


/**
 * Create a HTMLSelectElement.
 */
class CheckBox extends Form {

	/**
	 * Construct a CheckBox object.
	 * Sets the id attribute and the value attribute.
	 * @param string $id
	 * @param string $val
	 */
	public function __construct($id, $val) {
		$this->setId($id);
		$this->val = $val;
		$this->name = $id;
		$this->labelPosition = HTML_LABEL_AFTER;
	}

	/**
	 * Print the HTMLCheckboxElement.
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
			$strLabel.= '>'.$this->getLabel()."</label>\n";
		}

		if ($this->labelPosition === HTML_LABEL_BEFORE) {
			$strHtml.= $strLabel;
		}

		$strHtml.= '<input id="'.$this->getId().'" name="'.$this->name.'"';
		$strHtml.= ' type="checkbox" value="'.$this->val.'"';
		if ($this->selected === true) {
			$strHtml.= ' checked="checked"';
		}
		if ($this->disabled == true) {
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
		if ($this->labelPosition === HTML_LABEL_AFTER) {
			$strHtml.= $strLabel;
		}

		return $strHtml;
	}
}