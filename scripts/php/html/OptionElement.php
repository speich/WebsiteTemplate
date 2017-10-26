<?php

namespace WebsiteTemplate\html;

/**
 * Class OptionElement
 * @package WebsiteTemplate\html
 */
class OptionElement
{
    public $selected = false;

    public $value = null;

    public $title = null;

    public $text = '';

    /**
     * Render HTML option element.
     * @return string HTML
     */
    public function render()
    {
        $value = $this->value === null ? '' : ' value="'.$this->value.'"';
        $sel = $this->selected ? ' selected="selected"' : '';
        $title = $this->title === null ? '' : ' title="'.$this->title.'"';

        return '<option'.$value.$sel.$title.'>'.$this->text.'</option>';
    }
}