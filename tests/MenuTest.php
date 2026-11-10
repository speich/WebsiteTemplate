<?php

use PHPUnit\Framework\TestCase;
use WebsiteTemplate\Menu;

/**
 * Tests rendering menu item states.
 */
final class MenuTest extends TestCase
{
    /**
     * Check that disabled links and items render the aria-disabled attribute.
     * @return void
     */
    public function testDisabledItemsRenderAriaDisabled(): void
    {
        $menu = new Menu([
            [1, 0, 'Disabled link', '/disabled'],
            [2, 0, 'Disabled item'],
        ]);
        $menu->autoActive = false;
        $menu->byId(1)->disabled = true;
        $menu->byId(2)->disabled = true;

        $html = $menu->render();

        $this->assertStringContainsString('<a href="/disabled" aria-disabled="true">', $html);
        $this->assertStringContainsString('<div aria-disabled="true">', $html);
    }

    /**
     * Check that enabled menu items omit the aria-disabled attribute.
     * @return void
     */
    public function testEnabledItemsOmitAriaDisabled(): void
    {
        $menu = new Menu([[1, 0, 'Enabled item', '/enabled']]);
        $menu->autoActive = false;

        $this->assertStringNotContainsString('aria-disabled', $menu->render());
    }
}