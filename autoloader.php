<?php

function __autoload($className) {
    $file = ROOT_PATH . '/classes/' . $className . '.php';
    echo $file . PHP_EOL;
    if (file_exists($file)) { 
        require $file;
        return true;
    }
    
    return false; 
}
