<?php
/*
* Website route
*/
Router::defaultMap('Home', 'Index');

Router::get('slug', 'Home', 'Slug');
?>