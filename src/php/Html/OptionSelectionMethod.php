<?php

namespace WebsiteTemplate\Html;

/**
 * Defines which property of an OptionElement is used to match and set it as selected.
 */
enum OptionSelectionMethod
{
    /**
     * Match the given selection against the HTML value attribute.
     */
    case Value;

    /**
     * Match the given selection against the visible text of the option.
     */
    case Text;
}