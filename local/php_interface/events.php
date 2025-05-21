<?
use Bitrix\Main\Localization\Loc;

//ex2-590
AddEventHandler('iblock', 'OnBeforeIBlockElementAdd', ['Ex2_Handlers', 'OnBeforeIBlockElementAddHandler']);
AddEventHandler('iblock', 'OnBeforeIBlockElementUpdate', ['Ex2_Handlers', 'OnBeforeIBlockElementUpdateHandler']);
AddEventHandler('iblock', 'OnAfterIBlockElementUpdate', ['Ex2_Handlers', 'OnAfterIBlockElementUpdateHandler']);
//[ex2-600]
AddEventHandler('main', 'OnBeforeUserUpdate', ['Ex2_Handlers', 'OnBeforeUserUpdateHandler']);
AddEventHandler('main', 'OnAfterUserUpdate', ['Ex2_Handlers', 'OnAfterUserUpdateHandler']);
// ex2-630
AddEventHandler('search', 'BeforeIndex', ['Ex2_Handlers', 'BeforeIndexHandler']);
// [ex2-190]
AddEventHandler('main', 'OnBuildGlobalMenu', ['Ex2_Handlers', 'OnBuildGlobalMenuHandler']);
// [ex2-620] 
AddEventHandler("main", "OnBeforeEventSend", ['Ex2_Handlers', 'OnBeforeEventSendHandler']);


Loc::loadMessages(__FILE__);
//IncludeModuleLangFile(__FILE__);
class Ex2_Handlers
{
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

            $rsProp = CIBlockElement::GetProperty(
                ID_IBLOCK_REVIEWS,
                $arFields["ID"],
                [],
                ['CODE' => 'AUTHOR']
            );
            while ($prop = $rsProp->fetch()) {
                $old_author = $prop['VALUE'];
            }
            if ($old_author) {
                Ex2_Handlers::$data['old_author'][$arFields["ID"]] = $old_author;
            } else {
                Ex2_Handlers::$data['old_author'][$arFields["ID"]] = Loc::getMessage('NO_AUTHOR');
            }
        }
    }
    public static function OnAfterIBlockElementUpdateHandler(&$arFields)
    {
        global $APPLICATION;
        if ($arFields['IBLOCK_ID'] == ID_IBLOCK_REVIEWS) {
            $rsProp = CIBlockElement::GetProperty(
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
            if ($new_author != Ex2_Handlers::$data['old_author'][$arFields["ID"]]) {
                $mess = Loc::getMessage('NEW_AUTHOR', ['#ID#' => $arFields['ID'], '#old#' => Ex2_Handlers::$data['old_author'][$arFields["ID"]], '#new#' => $new_author]);


                CEventLog::Add(
                    [
                        'AUDIT_TYPE_ID' => 'ex2_590',
                        'DESCRIPTION' => $mess
                    ]
                );
            }
        }
    }
    //[ex2-600]
    public static function OnBeforeUserUpdateHandler(&$arFields)
    {
        $arUser = CUser::GetList(
            ($by = "id"),
            ($order = "desc"),
            ['ID' => $arFields['ID']],
            ['FIELDS' => ['ID'], 'SELECT' => ['UF_USER_CLASS']]
        )->fetch();

        Ex2_Handlers::$data['OLD_CLASS'][$arFields['ID']] = $arUser['UF_USER_CLASS'];
   
    }

    public static function OnAfterUserUpdateHandler(&$arFields)
    {
        if ($arFields['UF_USER_CLASS'] != Ex2_Handlers::$data['OLD_CLASS'][$arFields['ID']]) {
            if (Ex2_Handlers::$data['OLD_CLASS'][$arFields['ID']]) {
                $arElement = CUserFieldEnum::GetList(
                    [],
                    ['ID' => Ex2_Handlers::$data['OLD_CLASS'][$arFields['ID']], 'USER_FIELD_ID' => UF_CLASS]
                )->fetch();
                $old = $arElement['VALUE'];
            } else {
                $old = Loc::getMessage('NO_CLASS');
            }
            if ($arFields['UF_USER_CLASS']) {
                $arElement = CUserFieldEnum::GetList(
                    [],
                    ['ID' => $arFields['UF_USER_CLASS'], 'USER_FIELD_ID' => UF_CLASS]
                )->fetch();
                $new = $arElement['VALUE'];
            } else {
                $new = Loc::getMessage('NO_CLASS');
            }
            $arEventFields = array(
                'old_class' => $old,
                'new_class' => $new
            );
            CEvent::Send('EX2_AUTHOR_STATUS', 's1', $arEventFields);
        }
    }

    public static function BeforeIndexHandler($arFields)
    {
        // Проверяем, что индексируется элемент нужного инфоблока
        if ($arFields["MODULE_ID"] == "iblock" && $arFields["PARAM2"] == ID_IBLOCK_REVIEWS) {
            // Получаем свойство AUTHOR элемента
            $property = CIBlockElement::GetProperty(
                ID_IBLOCK_REVIEWS,
                $arFields['ITEM_ID'],
                [],
                ['CODE' => 'AUTHOR']
            )->Fetch();

            // Если свойство найдено и содержит значение (ID пользователя)
            if ($property && !empty($property['VALUE'])) {
                // Получаем данные пользователя
                $user = CUser::GetList(
                    ($by = "id"),
                    ($order = "desc"),
                    ['ID' => $arFields['ID']],
                    ['FIELDS' => ['LOGIN']]
                )->fetch();

                // Добавляем логин к заголовку, если пользователь существует
                if ($user) {
                    $arFields['TITLE'] .= ' ' . $user['LOGIN'];
                }
            }
        }

        // Возвращаем модифицированные поля (обязательно!)
        return $arFields;
    }

    // [ex2-190]
    public static function OnBuildGlobalMenuHandler(&$aGlobalMenu, &$aModuleMenu)
    {

        global $USER;

        if (in_array(5, $USER->GetUserGroupArray())) {
            if (array_key_exists("global_menu_content", $aGlobalMenu)) {
                $aGlobalMenuFiltred["global_menu_content"] = $aGlobalMenu["global_menu_content"];
            }
            foreach ($aModuleMenu as $value) {
                if ($value['parent_menu'] === 'global_menu_content') {
                    $aModuleMenuFiltred[] = $value;
                }
            }
            $aGlobalMenuFiltred['global_menu_quick'] = [
                'menu_id' => 'quick_access',
                'text' => 'Быстрый доступ',
                'title' => 'Быстрый доступ',
                'sort' => 100,
                'items_id' => 'global_menu_quick',
                'items' => [
                    [
                        'text' => 'Ссылка 1',
                        'url' => 'https://test1/'
                    ],
                    [
                        'text' => 'Ссылка 2',
                        'url' => 'https://test2/'
                    ]
                ]
            ];
            $aGlobalMenu = $aGlobalMenuFiltred;
            $aModuleMenu = $aModuleMenuFiltred;
        }
    }
    //[ex2-620] 
    public static function OnBeforeEventSendHandler(&$arFields, &$arTemplate)
    {
        if ($arTemplate['EVENT_NAME'] === 'USER_INFO') {

            $user = CUser::GetList(
                ($by = "id"),
                ($order = "desc"),
                ['ID' => $arFields['USER_ID']],
                ['FIELDS' => ['ID'], 'SELECT' => ['UF_USER_CLASS']]
            )->fetch();
            if ($user['UF_USER_CLASS']) {
                $arElement = CUserFieldEnum::GetList(
                    [],
                    ['ID' => $user['UF_USER_CLASS'], 'USER_FIELD_ID' => UF_CLASS]
                )->fetch();
                //тут не могу проверить сработает или так или так
                $arTemplate["MESSAGE"] = str_replace('#CLASS#', $arElement['VALUE'], $arTemplate["MESSAGE"]); 
                //$arFields['CLASS'] = $arElement['VALUE'];
            } else {
                $arTemplate["MESSAGE"] = str_replace('#CLASS#', Loc::getMessage('NO_CLASS'), $arTemplate["MESSAGE"]);
                //$arFields['CLASS'] = Loc::getMessage('NO_CLASS');
            }
        }
    }
}
