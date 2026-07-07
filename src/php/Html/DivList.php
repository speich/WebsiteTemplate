<?php

namespace WebsiteTemplate\Html;

/**
 * Class to create a list of HTMLDivElements.
 *
 * Mimics an HTMLSelectElement, but constructed with HTMLDivElements.
 * You can pass to the constructor either a 1-dim array or a 2-dim array.
 * In the second case the first item would be used to set anchor attribute.
 */
class DivList extends Html
{

    /** @var array list of items */
    public array $arrItem;

    /** @var bool renderAsHtml label attribute */
    private bool $label = false;

    /** @var string label for list */
    private string $labelName;

    public ?string $bemBlock = 'div-list';

    /**
     * Construct a list of HtmlDiv elements.
     * @param string $id Id of container element
     * @param array $arrItem Array of elements
     */
    public function __construct(string $id, array $arrItem)
    {
        $this->setId($id);
        $this->arrItem = $arrItem;
    }

    /**
     * Label the list of div elements.
     * @param string $label
     */
    public function setLabel(string $label): void
    {
        $this->labelName = $label;
        $this->label = true;
    }

    /**
     * Print the HTML list of div elements.
     * @return string Html
     */
    public function render(): string
    {
        $this->addCssClass($this->bemClass());
        $strHtml = '<div id="'.$this->getId().'"'.$this->renderCssClass().'>';
        if ($this->label) {
            $strHtml .= '<div class="'.$this->bemClass('label').'">'.$this->labelName.'</div>';
        }
        foreach ($this->arrItem as $item) {
            if (is_array($item)) {
                $strHtml .= '<div class="'.$this->bemClass('item').'">';
                $strHtml .= '<a class="'.$this->bemClass('link').'" href="'.$item[0].'">'.$item[1].'</a></div>';
            } else {
                $strHtml .= '<div class="'.$this->bemClass('item').'">'.$item.'</div>';
            }
        }
        $strHtml .= '</div>';

        return $strHtml;
    }
}