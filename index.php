<?php

require dirname(__DIR__).'/mekuteriya-php/vendor/autoload.php';

use Mekuteriya\MekuteriyaTime;
use Mekuteriya\MekuteriyaDate;

$time = new MekuteriyaTime('5:30:10 PM');

echo $time->convert();

echo "<br>";

echo (new MekuteriyaTime('2:10:11 PM'))->convert(
    MekuteriyaTime::ET,
    MekuteriyaTime::ETHIOPIAN
);

echo "<pre>";


print_r( (new MekuteriyaDate('2020-02-03'))->convert(MekuteriyaDate::ET) );
print_r( (new MekuteriyaDate('2012-05-25'))->convert(MekuteriyaDate::GR) );