<?php

namespace Mekuteriya;

use DateTimeZone;

class MekuteriyaTime extends \DateTime {
    const TIMEZONE = 'Africa/Addis_Ababa';

    public function __construct(String $datetime = 'now') {
        $dateTimeZone = new DateTimeZone(self::TIMEZONE);
        parent::__construct($datetime, $dateTimeZone);
    }
}

/** EOF */