<?php

namespace Mekuteriya;

use Mekuteriya\JDNConverter;

class MekuteriyaDate extends \DateTime {
    const ET = 1;

    const GR = 2;

    public function convert($to = self::ET) {
        if($to == self::ET)
            return $this->convertToETDate();
        else if($to == self::GR)
            return $this->convertToGR();
    }

    private function convertToETDate() {
        $year = parent::format('Y');
        $month = parent::format('m');
        $day = parent::format('d');
        $jdnConverter = new JDNConverter(
            $year,
            $month,
            $day
        );

        return $jdnConverter->gregorianToEthiopic();
    }

    private function convertToGR() {
        $year = parent::format('Y');
        $month = parent::format('m');
        $day = parent::format('d');
        $jdnConverter = new JDNConverter(
            $year,
            $month,
            $day
        );

        return $jdnConverter->ethiopicToGregorian();
    }

}

/** EOF */