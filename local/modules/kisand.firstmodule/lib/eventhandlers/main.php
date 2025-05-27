<?

namespace Kisand\FirstModule\EventHandlers;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Main
{
    public static $data;
    public static function OnBeforeUserUpdateHandler(&$arFields)
    {
        $arUser = \CUser::GetList(
            ($by = "id"),
            ($order = "desc"),
            ['ID' => $arFields['ID']],
            ['FIELDS' => ['ID'], 'SELECT' => ['UF_USER_CLASS']]
        )->fetch();

        Main::$data['OLD_CLASS'][$arFields['ID']] = $arUser['UF_USER_CLASS'];
    }

    public static function OnAfterUserUpdateHandler(&$arFields)
    {
        if ($arFields['UF_USER_CLASS'] != Main::$data['OLD_CLASS'][$arFields['ID']]) {
            if (Main::$data['OLD_CLASS'][$arFields['ID']]) {
                $arElement = \CUserFieldEnum::GetList(
                    [],
                    ['ID' => Main::$data['OLD_CLASS'][$arFields['ID']], 'USER_FIELD_ID' => UF_CLASS]
                )->fetch();
                $old = $arElement['VALUE'];
            } else {
                $old = Loc::getMessage('NO_CLASS');
            }
            if ($arFields['UF_USER_CLASS']) {
                $arElement = \CUserFieldEnum::GetList(
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
            \CEvent::Send('EX2_AUTHOR_STATUS', 's1', $arEventFields);
        }
    }

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
    public static function OnBeforeEventSendHandler(&$arFields, &$arTemplate)
    {
        if ($arTemplate['EVENT_NAME'] === 'USER_INFO') {

            $user = \CUser::GetList(
                ($by = "id"),
                ($order = "desc"),
                ['ID' => $arFields['USER_ID']],
                ['FIELDS' => ['ID'], 'SELECT' => ['UF_USER_CLASS']]
            )->fetch();
            if ($user['UF_USER_CLASS']) {
                $arElement = \CUserFieldEnum::GetList(
                    [],
                    ['ID' => $user['UF_USER_CLASS'], 'USER_FIELD_ID' => UF_CLASS]
                )->fetch();
                $arFields['CLASS'] = $arElement['VALUE'];
            } else {
                $arFields['CLASS'] = Loc::getMessage('NO_CLASS');
            }
        }
    }
}
