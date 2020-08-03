<?php

namespace WebsiteTemplate\html;

/**
 * Class OptionElement
 * @package WebsiteTemplate\html
 */
class OptionElement
{
    public $selected = false;

    /** @var string value attribute */
    public $value;

    /** @var string title attribute */
    public $title;

    /** @var string text of option */
    public $text = '';

    /**
     * Render HTML option element.
     * @return string HTML
     */
    public function render(): string
    {
        $value = $this->value === null ? '' : ' value="'.$this->value.'"';
        $sel = $this->selected ? ' selected="selected"' : '';
        $title = $this->title === null ? '' : ' title="'.$this->title.'"';

        return '<option'.$value.$sel.$title.'>'.$this->text.'</option>';
    }
}