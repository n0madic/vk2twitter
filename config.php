<?php

/**
 * @file
 * A single location to store configuration.
 */

//////////////////////////////// CONFIG ////////////////////////////////

define('ADMIN_PASS', 'f379eaf3c831b04de153469d1bec345e'); // md5() hash 

define('CONSUMER_KEY', 'yT8nB64wK8MKDxPjXwBBZnZlW');
define('CONSUMER_SECRET', 'EasEmBzeqKYcNBroOaZi6QB7btLTFOvlfaRkjwA3gS9NWOPSZU');

define('DB_SERVER', 'localhost');
define('DB_NAME', 'vk2twitter');
define('DB_USER', 'vk2twitter');
define('DB_PASSWORD', 'vk2twitter');

////////////////////////////// END CONFIG //////////////////////////////

mb_internal_encoding("UTF-8");

// Подключаемся к БД
$mysqli = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

/* изменение набора символов на utf8 */
if (!$mysqli->set_charset("utf8")) {
    die('ERROR: Ошибка при загрузке набора символов UTF8: (' . $mysqli->errno . ') ' . $mysqli->error);
}

?>