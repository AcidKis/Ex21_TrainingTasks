<?

namespace Kisand\FirstModule\EventHandlers;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
class Search
{
    public static function BeforeIndexHandler($arFields)
    {

        if ($arFields["MODULE_ID"] == "iblock" && $arFields["PARAM2"] == ID_IBLOCK_REVIEWS) {

            $property = \CIBlockElement::GetProperty(
                ID_IBLOCK_REVIEWS,
                $arFields['ITEM_ID'],
                [],
                ['CODE' => 'AUTHOR']
            )->Fetch();


            if ($property && !empty($property['VALUE'])) {

                $user = \CUser::GetList(
                    ($by = "id"),
                    ($order = "desc"),
                    ['ID' => $arFields['ID']],
                    ['FIELDS' => ['LOGIN']]
                )->fetch();

                if ($user) {
                    $arFields['TITLE'] .= ' ' . $user['LOGIN'];
                }
            }
        }

        return $arFields;
    }
}
