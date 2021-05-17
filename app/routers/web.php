<?php
/*
* Website route
*/
Router::defaultMap('Home', 'Index');
Router::get('/{slug}','Home', 'Index');
Router::get('/{slug}/{page}','Home', 'Index');
?>