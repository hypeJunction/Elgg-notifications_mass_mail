<?php

namespace NotificationsMassMail;

use Elgg\IntegrationTestCase;
use hypeJunction\Notifications\MassMail;

/**
 * Tests for MassMail entity class mapping, CRUD, and permissions.
 */
class MassMailEntityTest extends IntegrationTestCase {

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
     * @return void
     */
    public function testSubtypeConstant(): void {
		$this->assertEquals('notification_mass_mail', MassMail::SUBTYPE);
		$this->assertEquals('object', MassMail::TYPE);
	}

	/**
     * @return void
     */
    public function testInitializeAttributesSetsSubtype(): void {
		$entity = new MassMail();
		$this->assertEquals(MassMail::SUBTYPE, $entity->getSubtype());
	}

	/**
     * @return void
     */
    public function testEntityCanBeSavedAndLoaded(): void {
		$user = $this->createUser();
		$site = \elgg_get_site_entity();

		$entity = new MassMail();
		$entity->owner_guid = $user->guid;
		$entity->container_guid = $site->guid;
		$entity->access_id = ACCESS_LOGGED_IN;
		$entity->title = 'Test subject';
		$entity->description = 'Test body';
		$entity->method = '_preferred';

elgg_call(ELGG_IGNORE_ACCESS, function () use ($entity, &$loaded, &$guid) {
			$this->assertNotFalse($entity->save());
			$guid = $entity->guid;
			_elgg_services()->entityCache->delete($guid);
			$loaded = get_entity($guid);
		});

		$this->assertInstanceOf(MassMail::class, $loaded);
		$this->assertEquals('Test subject', $loaded->title);
		$this->assertEquals('Test body', $loaded->description);
		$this->assertEquals('_preferred', $loaded->method);

		elgg_call(ELGG_IGNORE_ACCESS, fn() => $entity->delete());
	}

	/**
     * @return void
     */
    public function testEntityClassMappedForSubtype(): void {
		$user = $this->createUser();
		$entity = new MassMail();
		$entity->owner_guid = $user->guid;
		$entity->container_guid = \elgg_get_site_entity()->guid;
		$entity->access_id = ACCESS_LOGGED_IN;
		$entity->title = 'hello';
		$entity->description = 'world';

elgg_call(ELGG_IGNORE_ACCESS, function () use ($entity, &$loaded) {
			$this->assertNotFalse($entity->save());
			_elgg_services()->entityCache->delete($entity->guid);
			$loaded = get_entity($entity->guid);
		});

		$this->assertInstanceOf(MassMail::class, $loaded);
		$this->assertEquals('notification_mass_mail', $loaded->getSubtype());

		elgg_call(ELGG_IGNORE_ACCESS, fn() => $entity->delete());
	}

	/**
     * @return void
     */
    public function testMethodMetadataPersists(): void {
		$user = $this->createUser();
		$entity = new MassMail();
		$entity->owner_guid = $user->guid;
		$entity->container_guid = \elgg_get_site_entity()->guid;
		$entity->access_id = ACCESS_LOGGED_IN;
		$entity->title = 't';
		$entity->description = 'd';
		$entity->method = 'email';

elgg_call(ELGG_IGNORE_ACCESS, function () use ($entity, &$loaded) {
			$this->assertNotFalse($entity->save());
			_elgg_services()->entityCache->delete($entity->guid);
			$loaded = get_entity($entity->guid);
		});

		$this->assertEquals('email', $loaded->method);

		elgg_call(ELGG_IGNORE_ACCESS, fn() => $entity->delete());
	}
}
