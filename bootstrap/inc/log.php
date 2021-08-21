<?php
/**
 * class Log debug
 */
class Log {
    public static function debug($msg) {
        $config = Config::get('log');
        file_put_contents(dirname(__FILE__)."/../../".$config['path_debug'], "", FILE_APPEND);
        $logfile = realpath(dirname(__FILE__)."/../../".$config['path_debug']);
        error_log(date('Y-m-d H:i:s').' : ', 3, $logfile);
        error_log(print_r($msg, true), 3, $logfile);
        error_log("\n", 3, $logfile);
    }

    public static function error($msg) {
        $config = Config::get('log');
        file_put_contents(dirname(__FILE__)."/../../".$config['path_error'], "", FILE_APPEND);
        $logfile = realpath(dirname(__FILE__)."/../../".$config['path_error']);
        error_log(date('Y-m-d H:i:s').' : ', 3, $logfile);
        error_log(print_r($msg, true), 3, $logfile);
        error_log("\n", 3, $logfile);
    }
}

/**
* Uncaught exception handler.
*/
function log_exception( $e )
{
    $config = Config::get('log');
    if( is_cli() ) {
        echo "".PHP_EOL;
        echo "Exception Occured: ".PHP_EOL;
        echo "Type :".get_class($e).PHP_EOL;
        echo "Message :".$e->getMessage().PHP_EOL;
        echo "File :".$e->getFile().PHP_EOL;
        echo "Line :".$e->getLine().PHP_EOL;
        $message = "Type: " . get_class( $e ) . "; Message: {$e->getMessage()}; File: {$e->getFile()}; Line: {$e->getLine()};";
        Log::errorCronJob($message);
        die();
    }

    if ( !empty($config["debug"]) )
    {   
        http_response_code(500);
        print "<div style='text-align: center;'>";
        print "<h2 style='color: rgb(190, 50, 50);'>Exception Occured:</h2>";
        print "<table style='width: 800px; display: inline-block;'>";
        print "<tr style='background-color:rgb(230,230,230);'><th style='width: 80px;'>Type</th><td>" . get_class( $e ) . "</td></tr>";
        print "<tr style='background-color:rgb(240,240,240);'><th>Message</th><td>{$e->getMessage()}</td></tr>";
        print "<tr style='background-color:rgb(230,230,230);'><th>File</th><td>{$e->getFile()}</td></tr>";
        print "<tr style='background-color:rgb(240,240,240);'><th>Line</th><td>{$e->getLine()}</td></tr>";
        print "</table></div>";
    }
    else
    {
        $message = "Type: " . get_class( $e ) . "; Message: {$e->getMessage()}; File: {$e->getFile()}; Line: {$e->getLine()};";
        $url = UrlHelper::fullUrl();
        $tmp = explode('?', $url);
        if ($tmp[0] == $config["error_page"]) {
            $html = View::render($config["error_page_view"], []);
            echo $html;
            die();
        } else {
            Log::error($message);
            header( "Location: {$config["error_page"]}?url=".$url);
        }
    }
    exit();
}

/**
* Error handler, passes flow over the exception logger with new ErrorException.
*/
function log_error( $num, $str, $file, $line, $context = null )
{
    log_exception( new ErrorException( $str, 0, $num, $file, $line ) );
}

/**
* Checks for a fatal error, work around for set_error_handler not working on fatal errors.
*/
function check_for_fatal()
{
    $error = error_get_last();
    if(!empty($error) && !empty($error['type']) && !empty($error['message']) && !empty($error['file']) && !empty($error['line'])) {
        if ( $error["type"] == E_ERROR )
        log_error( $error["type"], $error["message"], $error["file"], $error["line"] );
    }
}
register_shutdown_function( "check_for_fatal" );
set_error_handler( "log_error" );
set_exception_handler( "log_exception" );
ini_set( "display_errors", "off" );
error_reporting( E_ALL );