<?php
namespace WebsiteTemplate;

require_once 'Html.php';


/**
 * Class to create a list of HTMLDivElements.
 *
 * Mimics a HTMLSelectElement, but constructed with HTMLDivElements.
 * You can pass to the constructor either a 1-dim array or a 2-dim array.
 * In the second case the first item would be used to set anchor attribute.
 */
class DivList extends Html {

	/** @var array list of items */
	public $arrItem;

	/** @var bool renderAsHtml label attribute */
	private $label = false;

	/** @var string label for list */
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
	 * @return string Html
	 */
	public function render() {
		$strHtml = '<div id="'.$this->getId().'"';
		if ($this->cssClass) {
			$strHtml.= ' class="'.$this->cssClass.'"';
		}
		if ($this->cssStyle) {
			$strHtml.= ' style="'.$this->cssStyle.'"';
		}
		$strHtml.= ">\n";
		if ($this->label) {
			$strHtml.= '<div>'.$this->labelName."</div>\n";
		}
		foreach ($this->arrItem as $item) {
			if (is_array($item)) {
				$strHtml.= '<div><a href="'.$item[0].'">'.$item[1]."</a></div>\n";
			}
			else {
				$strHtml.= '<div>'.$item.'</div>'."\n";
			}
		}
		$strHtml.= "</div>\n";
		return $strHtml;

	}
}