<?
if(!defined ('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$eventManager = \Bitrix\Main\EventManager::getInstance();

require_once __DIR__ . "/functions.php";
require_once __DIR__ . "/constants.php";


$eventManager->AddEventHandler('iblock', 'OnBeforeIBlockElementAdd', [
    'Kisand\FirstModule\EventHandlers\Iblock',
    'OnBeforeIBlockElementAddHandler'
]);
$eventManager->AddEventHandler('iblock', 'OnBeforeIBlockElementUpdate', [
    'Kisand\FirstModule\EventHandlers\Iblock',
    'OnBeforeIBlockElementUpdateHandler'
]);
$eventManager->AddEventHandler('iblock', 'OnAfterIBlockElementUpdate', [
    'Kisand\FirstModule\EventHandlers\Iblock',
    'OnAfterIBlockElementUpdateHandler'
]);
$eventManager->AddEventHandler('main', 'OnBeforeUserUpdate', [
    'Kisand\FirstModule\EventHandlers\Main',
    'OnBeforeUserUpdateHandler'
]);
$eventManager->AddEventHandler('main', 'OnAfterUserUpdate', [
    'Kisand\FirstModule\EventHandlers\Main',
    'OnAfterUserUpdateHandler'
]);
$eventManager->AddEventHandler('main', 'OnBuildGlobalMenu', [
    'Kisand\FirstModule\EventHandlers\Main',
    'OnBuildGlobalMenuHandler'
]);
$eventManager->AddEventHandler('main', 'OnBeforeEventSend', [
    'Kisand\FirstModule\EventHandlers\Main',
    'OnBeforeEventSendHandler'
]);
$eventManager->AddEventHandler('search', 'BeforeIndex', [
    'Kisand\FirstModule\EventHandlers\Search',
    'BeforeIndexHandler'
]);