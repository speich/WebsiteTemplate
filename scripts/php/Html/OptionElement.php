<?php

namespace WebsiteTemplate\Html;

/**
 * Class OptionElement
 * @package WebsiteTemplate\html
 */
class OptionElement
{
    public bool $selected = false;

    /** @var ?string value attribute */
    public ?string $value = null;

    /** @var ?string title attribute */
    public ?string $title = null;

    /** @var string text of option */
    public string $text = '';

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