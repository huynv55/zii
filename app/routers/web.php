<?php
/*
* Website route
*/
Router::defaultMap('Home', 'Index');
Router::get('/error', "Error", 'Index');
Router::get('/error/404', "Error", 'NotFound');

Router::get('slug', 'Home', 'Slug');
?>