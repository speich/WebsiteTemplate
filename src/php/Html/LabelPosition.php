<?php

namespace WebsiteTemplate\Html;

enum LabelPosition: int
{
    /** Render the label before the form element */
    case Before = 1;

    /** Render the label after the form element */
    case After = 2;

    /** Render the label wrapped around the form field and place the text before the radio button */
    case WrappedBefore = 3;

    /** Render the label wrapped around the form field and place the text after the radio button */
    case WrappedAfter = 4;
}
