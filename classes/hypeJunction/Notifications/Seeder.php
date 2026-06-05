<?php

namespace hypeJunction\Notifications;

use Elgg\Database\Seeds\Seed;

class Seeder extends Seed {

	public static function getType(): string {
		return 'notification_mass_mail';
	}

	public function getCountOptions(): array {
		return [
			'type' => 'object',
			'subtype' => 'notification_mass_mail',
		];
	}

	public function seed(): void {
		$this->advance($this->getCount());

		while ($this->seedsCount() < $this->getCount()) {
			$entity = new MassMail();
			$entity->owner_guid = $this->getRandomUser()->guid;
			$entity->container_guid = $entity->owner_guid;
			$entity->title = $this->faker->sentence(4);
			$entity->description = $this->faker->paragraph();
			$entity->access_id = ACCESS_PRIVATE;

			if (!$entity->save()) {
				continue;
			}

			$this->advance();
		}
	}

	public function unseed(): void {
		$entities = elgg_get_entities([
			'type' => 'object',
			'subtype' => 'notification_mass_mail',
			'metadata_name_value_pairs' => [
				[
					'name' => '__faker',
					'value' => true,
				],
			],
			'limit' => false,
			'batch' => true,
		]);

		foreach ($entities as $entity) {
			$entity->delete();
			$this->advance();
		}
	}

	public static function addSeed(\Elgg\Event $event) {
		$seeds = $event->getValue();
		$seeds[] = self::class;
		return $seeds;
	}
}
