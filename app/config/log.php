<?php
return [
    'debug' => env("APP_DEBUG"),
    'path_debug' => "/log/debug.log",
    'path_error' => "/log/error.log",
    'path_debug_cron' => "/log/debug-cron.log",
    'path_error_cron' => "/log/error-cron.log",
    'error_page' => '/error',
    'error_page_view' => '/errors/500.phtml'
]
?>