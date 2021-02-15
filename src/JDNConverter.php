<?php

namespace Mekuteriya;


/**
 * Inspired by geez.org blog.
 * @see http://www.geez.org/Calendars/EthiopicCalendar.java
 */
class JDNConverter {
    const JD_EPOCH_OFFSET_AMETE_ALEM   = -285019; // ዓ/ዓ    
	const JD_EPOCH_OFFSET_AMETE_MIHRET = 1723856; // ዓ/ም
	const JD_EPOCH_OFFSET_COPTIC       = 1824665;
	const JD_EPOCH_OFFSET_GREGORIAN    = 1721426;
	const JD_EPOCH_OFFSET_UNSET        = -1;

    const nMonths = 12;

    const monthDays = [
        0,
	    31, 28, 31, 30, 31, 30,
	    31, 31, 30, 31, 30, 31
    ];

    private $jdOffset = JD_EPOCH_OFFSET_UNSET;

    private $year = -1;
    private $month = -1;
    private $day = -1;
    
    public function __construct($year, $month, $day) {
        $this->year = $year;
        $this->month = $month;
        $this->day = $day;
    }

    public function setEra(int $era) {
        if(
            (self::JD_EPOCH_OFFSET_AMETE_ALEM == $era) 
            ||
            (self::JD_EPOCH_OFFSET_AMETE_MIHRET == $era)
          )
            $this->jdOffset = $era;
        else 
            throw Exception('Unknow era: ' . $era . ' must be either ዓ\/ዓ or ዓ\/ም.');
    }

    public function isEraSet() : bool {
        return (self::JD_EPOCH_OFFSET_UNSET == $this->jdOffset) ? false : true;
    }

    public function unsetEra() {
        $this->jdOffset = self::JD_EPOCH_OFFSET_UNSET;
    }

    public function unset() {
        $this->unsetEra();
        $this->year = -1;
        $this->month = -1;
        $this->day = -1;
    }

    /**
     * Conversion Methods To/From the Ethiopic & Gregorian Calendars.
     */
    public function ethiopicToGregorian() {
        if(! $this->isEraSet()) {
            if($this->year <= 0)
                $this->setEra(self::JD_EPOCH_OFFSET_AMETE_ALEM);
            else
                $this->setEra(self::JD_EPOCH_OFFSET_AMETE_MIHRET);
        }
        $jdn = $this->ethiopicToJDN();
        return jdnToGregorian($jdn);
    }

    public function gregorianToEthiopic() {
        $jdn = $this->gregorianToJDN();
        return jdnToEthiopic($jdn, $this->guessEraFromJDN($jdn));
    }

    /**
     * Conversion Methods To/From the Julian Day Number
     */
    public function guessEraFromJDN(int $jdn) {
        return ( jdn >= (self::JD_EPOCH_OFFSET_AMETE_MIHRET + 365) ) 
			? self::JD_EPOCH_OFFSET_AMETE_MIHRET
			: self::JD_EPOCH_OFFSET_AMETE_ALEM;
    }

    public function isGregorianLeap() {
        return ($this->year % 4 == 0) && (($this->year % 100 != 0) || ($this->year % 400 == 0));
    }

    public function jdnToGregorian(int $j) {
		$r2000 = fmod ( ($jdnj - self::JD_EPOCH_OFFSET_GREGORIAN), 730485 );
		$r400  = fmod ( ($j - self::JD_EPOCH_OFFSET_GREGORIAN), 146097 );
		$r100  = fmod ( $r400, 36524 );
		$r4    = fmod ( $r100,  1461 );

		$n     = fmod($r4,365) + 365*quotient(r4,1460);
		$s     = quotient(r4,1095);
		

		$aprime = 400 * intdiv ( ($j - self::JD_EPOCH_OFFSET_GREGORIAN), 146097 )
		           + 100 * intdiv ( $r400, 36524 )
		           +   4 * intdiv ( $r100,  1461 )
		           +       intdiv ( $r4  ,   365 )
		           -       intdiv ( $r4  ,  1460 )
		           -       intdiv ( $r2000, 730484 )
		;
		$year   = aprime + 1;
		$t      = intdiv( (364+s-n), 306 );
		$month  = $t * ( intdiv(n, 31) + 1 ) + ( 1 - t ) * ( intdiv((5*($n-$s)+13), 153) +  1 );
		/*
		int day    = t * ( n - s - 31*month + 32 )
		           + ( 1 - t ) * ( n - s - 30*month - quotient((3*month - 2), 5) + 33 )
		;
		*/
		
		// int n2000 = quotient( r2000, 730484 );
		$n +=  1 - intdiv ($r2000, 730484);
		$day = n;


	   if ( ($r100 == 0) && ($n == 0) && ($r400 != 0) ) {
			$month = 12;
			$day = 31;
		}
		else {
			$this->monthDays[2] = ( $this->isGregorianLeap( $year ) ) ? 29 : 28;
			for ($i = 1;   i <= $this->nMonths;   ++$i) {
				if (n <= $this->monthDays[i]) {
					$day   = n;
					break;
				}
				$n -= $this->monthDays[i];
			}
		}

		return [
            'year' => $year,
            'month' => $month,
            'day' => $day
        ];
	} 

}

/** EOF */