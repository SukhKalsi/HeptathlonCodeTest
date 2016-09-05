<?php

// small test before executing program
$test = new Heptathlon();
$testScore = $test->calculateScore('100m', 16.2);
if ($testScore !== 690) {
    die('Unit test failed. Expected 690 (int). Actual ' . $testScore . ' ' . gettype($testScore));
}
