<?php 
/**
 * Utility class
 */
class Helper {
    public static function parseCsv($filename) {
        return array_map('str_getcsv', file($filename));
    }

    // public static function printTableRow($message = '') {
        // print('<tr>' . $message . '</tr>');
    // }

    public static function printLn($message = '') {
        // print($message . '<br />');
        print($message . PHP_EOL);
    }
}
