<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
global $arSectionFilter;

$arSectionFilter = array('IBLOCK_ID' => $arParams['IBLOCK_ID']);
CNext::makeSectionFilterInRegion($arSectionFilter);
?>
<?@include_once('page_blocks/'.$arParams["SECTIONS_TYPE_VIEW"].'.php');?>
