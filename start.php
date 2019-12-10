<?php

require_once 'CharManager.php';

/**
 * Displays the given string followed by a carriage return
 * @param $str String to display
 * @return void
 */
function println(string $str){
    echo $str . PHP_EOL;
}
/**
 * Clears the console screen
 * @return void
 */
function cls(){
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'){
        system('cls');
    } else {
        system('clear');
    }
}

$charManager = new CharManager();
$charManager->start();