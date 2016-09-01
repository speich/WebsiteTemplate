<?php
namespace WebsiteTemplate;



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
	public function setId($id) {
		$this->id = $id;
	}

	/**
	 * Return the id attribute of a HTMLElement.
	 * @return string|bool id or false
	 */
	public function getId() {
		if ($this->id) {
			return $this->id;
		}
		else {
			return false;
		}
	}

	/**
	 * Set the class attribute of a HTMLElement.
	 * @param string $class
	 */
	public function setCssClass($class) {
		$this->cssClass = $class;
	}

	/**
	 * Return the class attribute of a HTMLElement.
	 * @return string|bool class or false
	 */
	public function getCssClass() {
		if ($this->cssClass) {
			return $this->cssClass;
		}
		else {
			return false;
		}
	}

	/**
	 * Set the style attribute of a HTMLElement.
	 * @param string $style
	 */
	public function setCssStyle($style) {
		$this->cssStyle = $style;
	}

	/**
	 * Return the style atribute of HTMLElement.
	 * @return string|bool style or false
	 */
	public function getCssStyle() {
		if ($this->cssStyle) {
			return $this->cssStyle;
		}
		else {
			return false;
		}
	}
}