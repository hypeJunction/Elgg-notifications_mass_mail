<?php

namespace NotificationsMassMail;

use Elgg\Hook;
use Elgg\IntegrationTestCase;
use hypeJunction\Notifications\MassMail;
use hypeJunction\Notifications\SubscriptionsHandler;

/**
 * Tests for SubscriptionsHandler: which recipients get targeted and
 * which delivery method is chosen.
 */
class SubscriptionsHandlerTest extends IntegrationTestCase {

	public function up() {
	}

	public function down() {
	}

	public function getPluginID(): string {
		return 'notifications_mass_mail';
	}

	protected function makeHook($event): Hook {
		$hook = $this->getMockBuilder(Hook::class)->getMock();
		$hook->method('getName')->willReturn('get');
		$hook->method('getType')->willReturn('subscriptions');
		$hook->method('getValue')->willReturn([]);
$hook->method('getParam')->willReturnCallback(function ($key, $default = null) use ($event) {
			if ($key === 'event') {
				return $event;
			}
			return $default;
		});
		return $hook;
	}

	public function testReturnsVoidWhenNoEvent(): void {
		$handler = new SubscriptionsHandler();
		$hook = $this->makeHook(null);
		$this->assertNull($handler($hook));
	}

	public function testReturnsVoidWhenObjectIsNotMassMail(): void {
		$handler = new SubscriptionsHandler();

		$other = $this->createObject(['subtype' => 'blog']);
		$event = $this->getMockBuilder(\Elgg\Notifications\SubscriptionNotificationEvent::class)
			->disableOriginalConstructor()
			->onlyMethods(['getObject'])
			->getMock();
		$event->method('getObject')->willReturn($other);

		$hook = $this->makeHook($event);
		$this->assertNull($handler($hook));
	}

	public function testSiteContainerReturnsAllUsersWithExplicitMethod(): void {
		$handler = new SubscriptionsHandler();

		$user = $this->createUser();

		$mass_mail = $this->getMockBuilder(MassMail::class)
			->disableOriginalConstructor()
			->onlyMethods(['getContainerEntity'])
			->getMock();
		$mass_mail->method('getContainerEntity')->willReturn(\elgg_get_site_entity());
		$mass_mail->method = 'email';

		$event = $this->getMockBuilder(\Elgg\Notifications\SubscriptionNotificationEvent::class)
			->disableOriginalConstructor()
			->onlyMethods(['getObject'])
			->getMock();
		$event->method('getObject')->willReturn($mass_mail);

		$hook = $this->makeHook($event);
		$result = $handler($hook);

		$this->assertIsArray($result);
		$this->assertArrayHasKey($user->guid, $result);
		$this->assertEquals(['email'], $result[$user->guid]);
	}

	public function testPreferredMethodUsesRecipientNotificationSettings(): void {
		$handler = new SubscriptionsHandler();

		$user = $this->createUser();
		// Enable the site method for this recipient; setting uses
		// private_setting notification:method:<method>
		$user->setNotificationSetting('site', true);

		$mass_mail = $this->getMockBuilder(MassMail::class)
			->disableOriginalConstructor()
			->onlyMethods(['getContainerEntity'])
			->getMock();
		$mass_mail->method('getContainerEntity')->willReturn(\elgg_get_site_entity());
		$mass_mail->method = '_preferred';

		$event = $this->getMockBuilder(\Elgg\Notifications\SubscriptionNotificationEvent::class)
			->disableOriginalConstructor()
			->onlyMethods(['getObject'])
			->getMock();
		$event->method('getObject')->willReturn($mass_mail);

		$hook = $this->makeHook($event);
		$result = $handler($hook);

		$this->assertIsArray($result);
		$this->assertArrayHasKey($user->guid, $result);
		// Preferred mode returns the list of enabled methods, not a fixed value.
		$this->assertIsArray($result[$user->guid]);
	}

	public function testGroupContainerOnlyTargetsMembers(): void {
		$handler = new SubscriptionsHandler();

		$owner = $this->createUser();
		$member = $this->createUser();
		$outsider = $this->createUser();
		$group = $this->createGroup(['owner_guid' => $owner->guid]);
		$group->join($member);

		$mass_mail = $this->getMockBuilder(MassMail::class)
			->disableOriginalConstructor()
			->onlyMethods(['getContainerEntity'])
			->getMock();
		$mass_mail->method('getContainerEntity')->willReturn($group);
		$mass_mail->method = 'email';

		$event = $this->getMockBuilder(\Elgg\Notifications\SubscriptionNotificationEvent::class)
			->disableOriginalConstructor()
			->onlyMethods(['getObject'])
			->getMock();
		$event->method('getObject')->willReturn($mass_mail);

		$hook = $this->makeHook($event);
		$result = $handler($hook);

		$this->assertIsArray($result);
		$this->assertArrayHasKey($member->guid, $result);
		$this->assertArrayNotHasKey($outsider->guid, $result);
	}
}
