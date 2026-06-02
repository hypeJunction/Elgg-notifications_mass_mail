<?php

namespace NotificationsMassMail;

use Elgg\Hook;
use Elgg\IntegrationTestCase;
use hypeJunction\Notifications\PageMenuHandler;

/**
 * Tests for PageMenuHandler: admin menu entry, and group context entry
 * gated by plugin setting.
 */
class PageMenuHandlerTest extends IntegrationTestCase {

	public function up() {
	}

	public function down() {
	}

	/**
     * @return string
     */
    public function getPluginID(): string {
		return 'notifications_mass_mail';
	}

	/**
     * @param array $return
     * @return Hook
     */
    protected function makeHook(array $return): Hook {
		$hook = $this->getMockBuilder(Hook::class)->getMock();
		$hook->method('getName')->willReturn('register');
		$hook->method('getType')->willReturn('menu:page');
		$hook->method('getValue')->willReturn($return);
		$hook->method('getParam')->willReturnCallback(function ($k, $d = null) { return $d; });
		$hook->method('getParams')->willReturn([]);
		return $hook;
	}

	/**
     * @return void
     */
    public function testAdminContextAddsMassMailMenuItem(): void {
		\elgg_push_context('admin');
		try {
			$handler = new PageMenuHandler();
			$hook = $this->makeHook([]);
			$result = $handler($hook);
		} finally {
			\elgg_pop_context();
		}

		$this->assertIsArray($result);
		$this->assertCount(1, $result);
		$this->assertInstanceOf(\ElggMenuItem::class, $result[0]);
		$this->assertEquals('mass_mail', $result[0]->getName());
	}

	/**
     * @return void
     */
    public function testNonAdminNonGroupContextAddsNothing(): void {
		$handler = new PageMenuHandler();
		$hook = $this->makeHook(['existing']);
		$result = $handler($hook);

		$this->assertEquals(['existing'], $result);
	}

	/**
     * @return void
     */
    public function testGroupContextWithoutSettingAddsNothing(): void {
		$plugin = \elgg_get_plugin_from_id('notifications_mass_mail');
		if ($plugin) {
			$plugin->setSetting('groups_mass_mail', '0');
		}

		$owner = $this->createUser();
		$group = $this->createGroup(['owner_guid' => $owner->guid]);
		\elgg_set_page_owner_guid($group->guid);
		\elgg_push_context('groups');

		try {
			$handler = new PageMenuHandler();
			$hook = $this->makeHook([]);
			$result = $handler($hook);
		} finally {
			\elgg_pop_context();
			\elgg_set_page_owner_guid(0);
		}

		$this->assertEquals([], $result);
	}

	/**
     * @return void
     */
    public function testGroupContextWithSettingAddsMenuItem(): void {
		$plugin = \elgg_get_plugin_from_id('notifications_mass_mail');
		if (!$plugin) {
			$this->markTestSkipped('Plugin not installed in test DB');
		}
		$plugin->setSetting('groups_mass_mail', '1');

		$owner = $this->createUser();
		$group = $this->createGroup(['owner_guid' => $owner->guid]);
		\elgg_set_page_owner_guid($group->guid);
		\elgg_push_context('groups');

		try {
			$handler = new PageMenuHandler();
			$hook = $this->makeHook([]);
			$result = $handler($hook);
		} finally {
			\elgg_pop_context();
			\elgg_set_page_owner_guid(0);
			$plugin->setSetting('groups_mass_mail', '0');
		}

		$this->assertIsArray($result);
		$this->assertCount(1, $result);
		$this->assertInstanceOf(\ElggMenuItem::class, $result[0]);
		$this->assertEquals('mass_mail', $result[0]->getName());
		$this->assertStringContainsString((string) $group->guid, $result[0]->getHref());
	}
}
