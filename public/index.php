<?php
/**
 * Blog - A PHP Framework For Web Artisans
 */
require realpath(__DIR__.'/../vendor/autoload.php');
define("ACL_USER_CRYPTO", false);
define("ROOT_PATH", realpath(__DIR__.'/../').DIRECTORY_SEPARATOR);
define("APP_PATH", realpath(__DIR__.'/../app/').DIRECTORY_SEPARATOR);
/**
 * begin load env file
 * link doc : https://github.com/vlucas/phpdotenv
 */
$dotenv = Dotenv\Dotenv::createImmutable(ROOT_PATH);
$dotenv->load();
/**
 * end load env file
 */
require realpath(__DIR__.'/../helpers/helper.php');

$Zii = require_once realpath(__DIR__.'/../bootstrap/app.php');
$GLOBALS['ZiiApp'] = $Zii;
$Zii->run();
?>