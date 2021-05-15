<?php
return [
    'host'      => getenv('MYSQL_HOST'),
    'port'		=> getenv('MYSQL_PORT'),
    'username'  => getenv('MYSQL_USER'),
    'password'  => getenv('MYSQL_PASSWORD'),
    'db'		=> getenv('MYSQL_DBNAME')
];
?>