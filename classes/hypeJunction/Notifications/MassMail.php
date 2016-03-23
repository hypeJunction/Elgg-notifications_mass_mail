<?php

namespace hypeJunction\Notifications;

/**
 *
 */
class MassMail extends \ElggObject {

	const TYPE = 'object';
	const SUBTYPE = 'notification_mass_mail';

	/**
	 * {@inheritdoc}
	 */
	protected function initializeAttributes() {
		parent::initializeAttributes();
		$this->attributes['subtype'] = self::SUBTYPE;
	}

}