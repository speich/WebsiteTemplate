<?php

namespace WebsiteTemplate\Html;

/**
 * Defines the HTML output mode when rendering a SelectField.
 */
enum SelectRenderMode
{
    /**
     * Render the complete <select> element including all its child <option> elements.
     */
    case All;

    /**
     * Render only the inner <option> elements without the wrapping <select> tags.
     * Useful for dynamic AJAX updates or appending to an existing DOM element.
     */
    case OptionsOnly;
}
