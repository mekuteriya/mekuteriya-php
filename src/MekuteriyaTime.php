<?php

namespace Mekuteriya;

use DateTimeZone;

class MekuteriyaTime extends \DateTime {

    const ET = 1;

    const ETHIOPIAN= 2;

    const GREGORIAN = 3;

    const AMHARIC = 'am';

    const ENGLISH = 'en';

    const TIMEZONE = 'Africa/Addis_Ababa';

    const EKUL_LEILIT = [
        'en' => 'Ekul Leilit',
        'am' => 'እኩለ ሌሊት'
    ];

    const WEDEKET = [
        'en' => 'Wedek\'et',
        'am' => 'ውደቀት',
    ];

    const NIGAT = [
        'en' => 'NIGAT',
        'am' => 'ንጋት'
    ];

    const TUWAT = [
        'en' => 'T\'uwat',
        'am' => 'ጡዋት'
    ];

    const REFAD = [
        'en' => 'Refad',
        'am' => 'ረፋድ'
    ];

    const EKUL_KEN = [
        'en' => 'Ekul k\'en',
        'am' => 'እኩለ ቀን'
    ];

    const KESEAT_BEHWALA = [
        'en' => 'Kese\'at Behwala',
        'am' => 'ከሰዓት በኋላ'
    ];

    const WEDEMATA = [
        'en' => 'Wedemata',
        'am' => 'ወደማታ'
    ];

    const SIDENEGIZ = [
        'en' => 'Sidenegiz',
        'am' => 'ሲደነግዝ'
    ];

    const MISHET = [
        'en' => 'Mishet',
        'am' => 'ምሽት'
    ];

    public function __construct(String $datetime = 'now') {
        $dateTimeZone = new DateTimeZone(self::TIMEZONE);
        parent::__construct($datetime, $dateTimeZone);
    }

    public function convert($to = self::ET, $format = self::ETHIOPIAN, $language = self::AMHARIC) {
        if($to == self::ET) {
            // Convert
            $et_hour = $this->westernToETHR();
            // Format the time
            if($format == self::ETHIOPIAN)
                return $this->etFormatter($et_hour, $language);
            else if($format == self::GREGORIAN)
                return $this->westernFormatter($et_hour);
        }
    }

    private function westernToETHR() {
        $hour_am_pm = (int)parent::format('h');

        $hour_in_et = $hour_am_pm - 6 <= 0 ? $hour_am_pm + 6 : $hour_am_pm - 6;

        return $hour_in_et;
    }

    private function etToWesternHR() {
        $hour_am_pm = (int)parent::format('h');

        $hour_in_western = $hour_am_pm > 6 ? $hour_am_pm - 6 : $hour_am_pm + 6;

        return $hour_in_western;
    }

    private function etFormatter(int $et_hour, $language) {
        $hour_24hr = (int)parent::format('H');
        $minute = (int)parent::format('i');

        $day_division = null;

        switch($hour_24hr) {
            case 23:
            case 24:
            case 0:
                $day_division = self::EKUL_LEILIT[$language];
                break;
            case 1:
            case 2:
            case 3:
                $day_division = self::WEDEKET[$language];
                break;
            case 4:
            case 5:
                $day_division = self::NIGAT[$language];
                break;
            case 6:
            case 7:
            case 8:
                $day_division = self::TUWAT[$language];
                break;
            case 9:
            case 10:
            case 11:
                $day_division = self::REFAD[$language];
                break;
            case 12:
                $day_division = self::EKUL_KEN[$language];
                break;
            case 13:
            case 14:
            case 15:
                $day_division = self::KESEAT_BEHWALA[$language];
                break;
            case 16:
            case 17:
                $day_division = self::WEDEMATA[$language];
                break;
            case 18:
            case 19:
                $day_division = self.SIDENEGIZ[$language];
                break;
            case 20:
            case 21:
            case 22:
                $day_division = self.MISHET[$language];
                break;
        }

        return (String)$et_hour . ':' . (String)$minute . ':' . (String)parent::format('s') . ' ' . $day_division;
    }
}

/** EOF */