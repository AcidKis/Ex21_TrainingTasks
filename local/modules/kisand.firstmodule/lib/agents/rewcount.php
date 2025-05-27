<?


namespace Kisand\FirstModule\Agents;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
class RewCount
{
    public static function CheckRews($old_timestamp = null)
    {

        if ($old_timestamp !== null) {

            $arFilter = [
                "ACTIVE"        => "Y",
                ">TIMESTAMP_X"  => ConvertTimeStamp($old_timestamp, 'FULL'),
                "IBLOCK_ID"     => 5
            ];
            Loader::includeModule("iblock");

            $rsElement = \CIBlockElement::GetList(
                ["SORT" => "ASC"],
                $arFilter,
                false,
                false,
                ["ID", "IBLOCK_ID"]
            );


            $arElements = [];
            while ($arRew = $rsElement->Fetch()) {
                $arElements[] = $arRew;
            }
            $count = count($arElements);


            $mess = Loc::getMessage('AGENT_MESSAGE', [
                '#date#'  => FormatDate('d.m.Y H:i:s', $old_timestamp),
                '#count#' => $count
            ]);


            \CEventLog::Add([
                'AUDIT_TYPE_ID' => 'ex2_610',
                'DESCRIPTION'   => $mess
            ]);
        } 
        
        return __METHOD__ . '(' . time() . ');';
    }
}
