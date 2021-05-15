<?php
/*
* default route
*/
Router::defaultMap('Home', 'Index');
Router::any('/{page}','Home', 'Index');
?>