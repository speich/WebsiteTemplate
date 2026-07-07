<?php

namespace WebsiteTemplate;

/**
 * Trait to handle adding and rendering CSS classes.
 */
trait CssClassTrait
{
    /** @var array<string> list of CSS classes */
    private array $cssClasses = [];

    /**
     * Add one or several CSS classes.
     * Adds one or more classes. Existing classes with the same name are ignored.
     * Supports passing multiple arguments and string arguments containing multiple space-separated classes.
     * @param string ...$names
     */
    public function addCssClass(string ...$names): void
    {
        // Combine all arguments into one string, then split by spaces
        $classes = explode(' ', implode(' ', $names));

        // Trim each class and safely filter out empty strings (preserves '0' if it exists)
        $newClasses = array_filter(array_map('trim', $classes), static fn($class) => $class !== '');

        // Merge, deduplicate, and re-index the final array
        $this->cssClasses = array_values(array_unique(array_merge($this->cssClasses, $newClasses)));
    }

    /**
     * Returns the CSS class string.
     * @return string
     */
    public function getCssClass(): string
    {
        return implode(' ', $this->cssClasses);
    }

    /**
     * Returns the HTML class attribute string.
     * @return string HTML class attribute
     */
    public function renderCssClass(): string
    {
        $classes = $this->getCssClass();

        return $classes === '' ? '' : ' class="' . $classes . '"';
    }
}