<?php

use PHPUnit\Framework\TestCase;

class ExampleTimeTest extends TestCase {
    public function testDatetimeExample() {
        $datetime = new DateTime('11:50:10');
        
        fwrite(
            STDERR,
            print(
                "+++++++++++++++++++++++++++++++++++++++++++" + 
                $datetime->format('H:i:s')
                )
        );
        
    }
}