<?php
/*
* default route
*/
Router::defaultMap('Home', 'Index');
Router::get('/{page}','Home', 'Index');
?>