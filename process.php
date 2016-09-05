<?php
require 'environment.php';
define(ROOT_PATH, dirname(__FILE__) );
require 'autoloader.php';
require 'tests.php';

// Run
try {
    $t = new Report('Heptathlon.csv', IAAF::SPORT_TYPE_HEPTATHLON);
    $t->process();
    $t->output();
} catch (Exception $e) {
    echo 'Failed execution.' . PHP_EOL;
    echo $e->getMessage() . PHP_EOL;
}

?>