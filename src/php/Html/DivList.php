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

    /** @var string|null label */
    public ?string $label = null;

    public ?string $blockNameck = 'div-list';

    /**
     * Construct a list of HtmlDiv elements.
     * @param string $id Id of the container element
     * @param array $arrItem Array of elements
     */
    public function __construct(string $id, array $arrItem)
    {
        $this->id = $id;
        $this->arrItem = $arrItem;
    }

    /**
     * Print the HTML list of div elements.
     * @return string Html
     */
    public function render(): string
    {
        $this->addCssClass($this->blockClass());
        $strHtml = '<div id="'.($this->id ? ' id="'.$this->id.'"' : '').'"'.$this->renderCssClass().'>';
        if ($this->label !== null) {
            $strHtml .= '<div class="'.$this->blockClass('label').'">'.$this->label.'</div>';
        }
        foreach ($this->arrItem as $item) {
            if (is_array($item)) {
                $strHtml .= '<div class="'.$this->blockClass('item').'">';
                $strHtml .= '<a class="'.$this->blockClass('link').'" href="'.$item[0].'">'.$item[1].'</a></div>';
            } else {
                $strHtml .= '<div class="'.$this->blockClass('item').'">'.$item.'</div>';
            }
        }
        $strHtml .= '</div>';

        return $strHtml;
    }
}