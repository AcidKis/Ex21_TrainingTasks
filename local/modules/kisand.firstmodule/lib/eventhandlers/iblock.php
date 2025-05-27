<?

namespace Kisand\FirstModule\EventHandlers;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
class Iblock {
    private static $data;
    //ex2-590
    public static function OnBeforeIBlockElementAddHandler(&$arFields)
    {
        global $APPLICATION;
        if ($arFields['IBLOCK_ID'] == ID_IBLOCK_REVIEWS) {
            if (str_contains($arFields['PREVIEW_TEXT'], '#del#')) {
                $arFields['PREVIEW_TEXT'] = str_replace('#del#', '', $arFields['PREVIEW_TEXT']);
            }
            if (mb_strlen($arFields['PREVIEW_TEXT']) < 5) {
                $APPLICATION->ThrowException(Loc::getMessage('PREVIEW_TEXT_ERROR', ['#len#' => mb_strlen($arFields['PREVIEW_TEXT'])]));
                return false;
            }
        }
    }
    public static function OnBeforeIBlockElementUpdateHandler(&$arFields)
    {
        global $APPLICATION;

        if ($arFields['IBLOCK_ID'] == ID_IBLOCK_REVIEWS) {
            if (str_contains($arFields['PREVIEW_TEXT'], '#del#')) {
                $arFields['PREVIEW_TEXT'] = str_replace('#del#', '', $arFields['PREVIEW_TEXT']);
            }
            if (mb_strlen($arFields['PREVIEW_TEXT']) < 5) {
                $APPLICATION->ThrowException(Loc::getMessage('PREVIEW_TEXT_ERROR', ['#len#' => mb_strlen($arFields['PREVIEW_TEXT'])]));
                return false;
            }

            $rsProp = \CIBlockElement::GetProperty(
                ID_IBLOCK_REVIEWS,
                $arFields["ID"],
                [],
                ['CODE' => 'AUTHOR']
            );
            while ($prop = $rsProp->fetch()) {
                $old_author = $prop['VALUE'];
            }
            if ($old_author) {
                Main::$data['old_author'][$arFields["ID"]] = $old_author;
            } else {
                Main::$data['old_author'][$arFields["ID"]] = Loc::getMessage('NO_AUTHOR');
            }
        }
    }
    public static function OnAfterIBlockElementUpdateHandler(&$arFields)
    {
        global $APPLICATION;
        if ($arFields['IBLOCK_ID'] == ID_IBLOCK_REVIEWS) {
            $rsProp = \CIBlockElement::GetProperty(
                ID_IBLOCK_REVIEWS,
                $arFields["ID"],
                [],
                ['CODE' => 'AUTHOR']
            );
            while ($prop = $rsProp->fetch()) {
                $new_author = $prop['VALUE'];
            }
            if (!$new_author) {
                $new_author = Loc::getMessage('NO_AUTHOR');
            }
            if ($new_author != Main::$data['old_author'][$arFields["ID"]]) {
                $mess = Loc::getMessage('NEW_AUTHOR', ['#ID#' => $arFields['ID'], '#old#' => Main::$data['old_author'][$arFields["ID"]], '#new#' => $new_author]);


                \CEventLog::Add(
                    [
                        'AUDIT_TYPE_ID' => 'ex2_590',
                        'DESCRIPTION' => $mess
                    ]
                );
            }
        }
    }
}