<?php
define('APP_PATH', '/App');
require dirname(__FILE__).'/GeniusPHP/GeniusPHP.php';
require dirname(__FILE__).APP_PATH.'/Config/config.php';
Genius::run($CONFIG);