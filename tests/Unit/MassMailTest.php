<?php

namespace hypeJunction\Notifications\Tests\Unit;

use PHPUnit\Framework\TestCase;

class MassMailTest extends TestCase {

    public function testSubtypeConstantIsDefined(): void {
        $this->assertEquals('notification_mass_mail', \hypeJunction\Notifications\MassMail::SUBTYPE);
    }

    public function testTypeConstantIsDefined(): void {
        $this->assertEquals('object', \hypeJunction\Notifications\MassMail::TYPE);
    }
}
