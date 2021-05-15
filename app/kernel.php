<?php
/*
* include services
*/
$listFileServices = getListFileInDir(dirname(__FILE__).DIRECTORY_SEPARATOR.'services');
foreach($listFileServices as $service) {
	require $service;
}

/*
* include Routers
*/
$listFileRouters = getListFileInDir(dirname(__FILE__).DIRECTORY_SEPARATOR.'routers');
foreach($listFileRouters as $route) {
	require $route;
}
?>