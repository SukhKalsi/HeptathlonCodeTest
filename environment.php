<?php
if ( isset($_SERVER['APPLICATION_ENV']) && ($_SERVER['APPLICATION_ENV'] === 'development') ) {
    error_reporting(E_ALL);
} else {
    error_reporting(0);
}
