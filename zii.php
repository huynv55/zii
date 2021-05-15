<?php
require realpath(__DIR__.'/vendor/autoload.php');
define("ACL_USER_CRYPTO", false);
define("ROOT_PATH", realpath(__DIR__.'/').DIRECTORY_SEPARATOR);
define("APP_PATH", realpath(__DIR__.'/app/').DIRECTORY_SEPARATOR);
/**
 * begin load env file
 * link doc : https://github.com/vlucas/phpdotenv
 */
$dotenv = Dotenv\Dotenv::createImmutable(ROOT_PATH);
$dotenv->load();
/**
 * end load env file
 */

$argvs = $_SERVER['argv'];
$action = !empty($argvs[1]) ? $argvs[1] : 'init';

require realpath(__DIR__.'/consoles/'.$action.'.php');

die();
?>