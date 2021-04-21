<?
//Navigation chain template
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arChainBody = '';
foreach($arCHAIN as $i => $item){
	if(strlen($item["LINK"])<strlen(SITE_DIR)){
		continue;
	}
	if($item["LINK"] <> ""){
		$arChainBody .= '<li><a href="'.$item["LINK"].'">'.htmlspecialcharsex($item["TITLE"]).'</a></li>';
	}
	else{
		$arChainBody .= '<li>'.htmlspecialcharsex($item["TITLE"]).'</li>';
	}
}
return $arChainBody;
?>