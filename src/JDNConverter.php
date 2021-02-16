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

    private $jdOffset = self::JD_EPOCH_OFFSET_UNSET;

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
     * Main func 1.
     * Conversion Methods To/From the Ethiopic & Gregorian Calendars.
     */
    public function ethiopicToGregorian() {
        if(! $this->isEraSet()) {
            if($this->year <= 0)
                $this->setEra(self::JD_EPOCH_OFFSET_AMETE_ALEM);
            else
                $this->setEra(self::JD_EPOCH_OFFSET_AMETE_MIHRET);
        }
        //$jdn = $this->ethiopicToJDN();
        $jdn = $this->ethCopticToJDN();
        return jdnToGregorian($jdn);
    }

    public function gregorianToEthiopic() {
        $jdn = $this->gregorianToJDN();
        return $this->jdnToEthiopic($jdn, $this->guessEraFromJDN($jdn));
    }

    /**
     * Conversion Methods To/From the Julian Day Number
     */
    public function guessEraFromJDN(int $jdn) {
        return ( $jdn >= (self::JD_EPOCH_OFFSET_AMETE_MIHRET + 365) ) 
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

    public function gregorianToJDN() {
		$s   = intdiv ( $this->year    ,   4 )
		        - intdiv ( $this->year - 1,   4 )
		        - intdiv ( $this->year    , 100 )
		        + intdiv ( $this->year - 1, 100 )
		        + intdiv ( $this->year    , 400 )
		        - intdiv ( $this->year - 1, 400 )
		;

		$t   = intdiv ( 14 - $this->month, 12 );

		$n   = 31 * $t * ( $this->month - 1 )
		        + ( 1 - $t ) * ( 59 + $s + 30 * ($this->month - 3) + intdiv( (3*$this->month - 7), 5) )
		        + $this->day - 1
		;

		$j   = self::JD_EPOCH_OFFSET_GREGORIAN
		        + 365 * ($this->year - 1)
		        + intdiv ( $this->year - 1,   4 )
		        - intdiv ( $this->year - 1, 100 )
		        + intdiv ( $this->year - 1, 400 )
		        + $n
		;

		return $j;
    }
    
    public function jdnToEthiopic(int $jdn, int $era = -1 ) {
        if($era == -1)
            return ( isEraSet() )
                ? $this->jdnToEthiopic($jdn, $jdOffset )
                : $this->jdnToEthiopic($jdn, $this->guessEraFromJDN($jdn) );
        else {
            $r = fmod( ($jdn - $era), 1461 ) ;
            $n = fmod( $r, 365 ) + 365 * intdiv( $r, 1460 ) ; 
            
            $year = 4 * intdiv( ($jdn - $era), 1461 )
                + intdiv( $r, 365 )
                - intdiv( $r, 1460 )
                ;
            $month = intdiv( $n, 30 ) + 1;
            $day   = fmod( $n, 30 ) + 1 ;
            
            return [
                'year' => $year,
                'month' => $month,
                'day' => $day
            ];
        }
    }

    /**
	 *  Computes the Julian day number of the given Coptic or Ethiopic date.
	 *  This method assumes that the JDN epoch offset has been set. This method
	 *  is called by copticToGregorian and ethiopicToGregorian which will set
	 *  the jdn offset context.
	 */
	private function ethCopticToJDN(int $era ) {
		$jdn = ( $era + 365 )
		    + 365 * ( $this->year - 1 )
		    + quotient( $this->year, 4 )
		    + 30 * $this->month
		    + $this->day - 31
		;
	       
		return $jdn;
    }
    
    public function copticToGregorian() {
		$this->setEra( self::JD_EPOCH_OFFSET_COPTIC );
        //$jdn = $this->ethiopicToJDN();
        $jdn = $this->ethCopticToJDN();
		return $this->jdnToGregorian( jdn );
    }
}

/** EOF */