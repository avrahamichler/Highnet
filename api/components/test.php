<?php
namespace api\components\test;

require_once '../../vendor/autoload.php';
use Google_Client;
/**
 * Convert strings with underscores into CamelCase
 *
 * @param    string    $string    The string to convert
 * @param    bool    $first_char_caps    camelCase or CamelCase
 * @return    string    The converted string
 *
 */
function underscoreToCamelCase( $string)
{
    $string = strtolower($string);
    $func = create_function('$c', 'return strtoupper($c[1]);');
    return preg_replace_callback('/_([a-z])/', $func, $string);
}

function autoload($str){
    $name = strtoupper(preg_replace('/([a-z])([A-Z])/', '$1_$2', $str));
    return $name;
}


$bytes = openssl_random_pseudo_bytes(16, $cstrong);
$hex   = bin2hex($bytes);


var_dump($hex);

echo PHP_EOL;

?>