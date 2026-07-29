<?php

namespace WebsiteTemplate\Html;

/**
 * Defines the source property used to automatically generate the title attribute for an OptionElement.
 */
enum OptionTitleSource
{
    /**
     * Use the value attribute of the option as the title attribute.
     */
    case Value;

    /**
     * Use the visible text of the option as the title attribute.
     */
    case Text;

}
