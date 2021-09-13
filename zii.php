<?php
require realpath(__DIR__.'/vendor/autoload.php');
define("ACL_USER_CRYPTO", false);
define("ROOT_PATH", realpath(__DIR__.'/').DIRECTORY_SEPARATOR);
define("APP_PATH", realpath(__DIR__.'/app/').DIRECTORY_SEPARATOR);
require realpath(__DIR__.'/helpers/helper.php');

(new DotEnv(ROOT_PATH . '.env'))->load();

$argvs = $_SERVER['argv'];
$action = !empty($argvs[1]) ? $argvs[1] : 'init';

require realpath(__DIR__.'/consoles/'.$action.'.php');
?>