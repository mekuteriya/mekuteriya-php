<?php

use PHPUnit\Framework\TestCase;
use Mekuteriya\MekuteriyaTime;

class TimeConverterTest extends TestCase {
    const TIMEZONE = 'Africa/Addis_Ababa';

    public function testMekuteriyaTimeCanBeCreatedFromEmptyConstructor() {
        $this->assertInstanceOf(
            MekuteriyaTime::class,
            new MekuteriyaTime()
        );
    }

    public function testMekuteriyaTimeCanBeCreatedFromString() {
        $this->assertInstanceOf(
            MekuteriyaTime::class,
            new Mekuteriyatime('1:11:50')
        );
    }

    public function testMekuteriyaTimeZone() {
        $time = new MekuteriyaTime();
        $this->assertEquals(self::TIMEZONE, $time->getTimeZone()->getName());
    }
}