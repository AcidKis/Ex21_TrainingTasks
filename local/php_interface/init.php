<?
if (file_exists($_SERVER["DOCUMENT_ROOT"] . '/local/php_interface/const.php')) {
    require_once($_SERVER["DOCUMENT_ROOT"] . '/local/php_interface/const.php');
}
if (file_exists($_SERVER["DOCUMENT_ROOT"] . '/local/php_interface/events.php')) {
    require_once($_SERVER["DOCUMENT_ROOT"] . '/local/php_interface/events.php');
}
if (file_exists($_SERVER["DOCUMENT_ROOT"] . '/local/php_interface/agents.php')) {
    require_once($_SERVER["DOCUMENT_ROOT"] . '/local/php_interface/agents.php');
}