<?php

namespace NotificationsMassMail;

use Elgg\Hook;
use Elgg\IntegrationTestCase;
use hypeJunction\Notifications\ContainerPermissionsHandler;
use hypeJunction\Notifications\MassMail;

/**
 * Tests for ContainerPermissionsHandler hook: who can write mass-mail
 * entities into which container type.
 */
class ContainerPermissionsHandlerTest extends IntegrationTestCase {

	public function up() {
	}

	public function down() {
	}

	public function getPluginID(): string {
		return 'notifications_mass_mail';
	}

	protected function makeHook(string $subtype, $container): Hook {
		$hook = $this->getMockBuilder(Hook::class)->getMock();
		$hook->method('getName')->willReturn('container_permissions_check');
		$hook->method('getType')->willReturn('object');
		$hook->method('getValue')->willReturn(true);
$hook->method('getParam')->willReturnCallback(function ($key, $default = null) use ($subtype, $container) {
			if ($key === 'subtype') {
				return $subtype;
			}
			if ($key === 'container') {
				return $container;
			}
			return $default;
		});
		return $hook;
	}

	public function testReturnsVoidForOtherSubtype(): void {
		$handler = new ContainerPermissionsHandler();
		$hook = $this->makeHook('blog', \elgg_get_site_entity());
		$this->assertNull($handler($hook));
	}

	public function testReturnsVoidWhenContainerNotEntity(): void {
		$handler = new ContainerPermissionsHandler();
		$hook = $this->makeHook(MassMail::SUBTYPE, null);
		$this->assertNull($handler($hook));
	}

	public function testSiteContainerReturnsVoidAllowingDefault(): void {
		$handler = new ContainerPermissionsHandler();
		$hook = $this->makeHook(MassMail::SUBTYPE, \elgg_get_site_entity());
		// site => return; (void, default value preserved)
		$this->assertNull($handler($hook));
	}

	public function testUserContainerDenied(): void {
		$handler = new ContainerPermissionsHandler();
		$user = $this->createUser();
		$hook = $this->makeHook(MassMail::SUBTYPE, $user);
		$this->assertFalse($handler($hook));
	}

	public function testObjectContainerDenied(): void {
		$handler = new ContainerPermissionsHandler();
		$owner = $this->createUser();
		$object = $this->createObject(['subtype' => 'blog', 'owner_guid' => $owner->guid]);
		$hook = $this->makeHook(MassMail::SUBTYPE, $object);
		$this->assertFalse($handler($hook));
	}

	public function testGroupContainerDelegatesToCanEdit(): void {
		$handler = new ContainerPermissionsHandler();

		$group = $this->getMockBuilder(\ElggGroup::class)
			->disableOriginalConstructor()
			->onlyMethods(['canEdit', 'getType'])
			->getMock();
		$group->method('getType')->willReturn('group');
		$group->method('canEdit')->willReturn(true);

		$hook = $this->makeHook(MassMail::SUBTYPE, $group);
		$this->assertTrue($handler($hook));
	}

	public function testGroupContainerDeniedWhenCannotEdit(): void {
		$handler = new ContainerPermissionsHandler();

		$group = $this->getMockBuilder(\ElggGroup::class)
			->disableOriginalConstructor()
			->onlyMethods(['canEdit', 'getType'])
			->getMock();
		$group->method('getType')->willReturn('group');
		$group->method('canEdit')->willReturn(false);

		$hook = $this->makeHook(MassMail::SUBTYPE, $group);
		$this->assertFalse($handler($hook));
	}
}
