<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

foreach ($arResult['ITEMS'] as $key => $arItem)
{
	$arItem['PRICES']['PRICE']['PRINT_VALUE'] = number_format((float)$arItem['PRICES']['PRICE']['PRINT_VALUE'], 0, '.', ' ');
	$arItem['PRICES']['PRICE']['PRINT_VALUE'] .= ' '.$arItem['PROPERTIES']['PRICECURRENCY']['VALUE_ENUM'];

	$arResult['ITEMS'][$key] = $arItem;
}
//ex2-580
$productsId = array_column($arResult['ITEMS'], 'ID');

$rsElement = CIBlockElement::GetList(
	$arOrder  = array("SORT" => "ASC"),
	$arFilter = array(
		"IBLOCK_ID" => ID_IBLOCK_REVIEWS,
		"PROPERTY_PRODUCT" => $productsId,
		"ACTIVE" => "Y"
	),
	false,
	false,
	$arSelectFields = array("ID", "NAME", "IBLOCK_ID", "PROPERTY_AUTHOR", "PROPERTY_PRODUCT")
);
while($arElement = $rsElement->fetch()) {
	$reviews[] = $arElement; 
}
$authorsId = array_unique(array_column($reviews, 'PROPERTY_AUTHOR_VALUE'));
$rsUsers = CUser::GetList(
	($by = "id"),
	($order = "desc"),
	['ID' => implode('|' , $authorsId), 'UF_AUTHOR_STATUS' => ID_CLASS_PUBLISHING, 'GROUPS_ID' => ID_GROUP_AUTHORS],
	['FIELDS' => ['ID']]
);
while($arUsers = $rsUsers->fetch()){
	$validAuthors[] = $arUsers['ID'];
}
if (is_array($validAuthors)) {
	foreach ($reviews as $key => $value) {
		if (in_array($value['PROPERTY_AUTHOR_VALUE'], $validAuthors)) {
			$arResult['filtredRev'][$value['PROPERTY_PRODUCT_VALUE']][] = $value['NAME']; 
		}
	}	
}
$count = 0;
if (is_array($arResult['filtredRev'])) {
	foreach ($arResult['filtredRev'] as $value) {
		$count += count($value);
	}
}
$arResult['count'] = $count;
$this->__component->SetResultCacheKeys(['count']);
