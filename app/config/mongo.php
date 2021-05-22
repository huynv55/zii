<?php
return [
    'host'      => getenv("MONGO_HOST"),
    'port'      => getenv("MONGO_PORT"),
    'user'      => getenv("MONGO_USER"),
    'password'  => getenv("MONGO_PASSWORD"),
    'authDB'    => ''
];
?>