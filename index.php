<?php

require dirname(__DIR__).'/mekuteriya-php/vendor/autoload.php';

use Mekuteriya\MekuteriyaTime;

$time = new MekuteriyaTime('1:10:11');

echo $time->convert();

echo "<br>";

echo (new MekuteriyaTime('2:10:11 PM'))->convert(
    MekuteriyaTime::ET,
    MekuteriyaTime::ETHIOPIAN
);