<?php

require dirname(__DIR__).'/mekuteriya-php/vendor/autoload.php';

use Mekuteriya\MekuteriyaTime;

$time = new MekuteriyaTime();

echo $time->convert();