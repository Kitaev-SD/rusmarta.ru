<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arSections = array();

foreach($arResult["SECTIONS"] as $key => $arSection):
	if($arSection["IBLOCK_SECTION_ID"] > 0):
		$arSections[$arSection["IBLOCK_SECTION_ID"]]["CHILDREN"][$arSection["ID"]] = $arSection;
	else:
		$arSection["CHILDREN"] = array();
		$arSections[$arSection["ID"]] = $arSection;
	endif;
endforeach;
 
$arResult["SECTIONS"] = $arSections;

foreach($arResult["SECTIONS"] as $key => $arSection):	
	if(isset($arSection["CHILDREN"]) && count($arSection["CHILDREN"]) > 0):
		foreach($arSection["CHILDREN"] as $keyChild => $arChild):
			if(is_array($arChild["PICTURE"])):
				if($arChild["PICTURE"]["WIDTH"] > $arParams["DISPLAY_IMG_WIDTH"] || $arChild["PICTURE"]["HEIGHT"] > $arParams["DISPLAY_IMG_HEIGHT"]):
					$arFileTmp = CFile::ResizeImageGet(
						$arChild["PICTURE"],
						array("width" => $arParams["DISPLAY_IMG_WIDTH"], "height" => $arParams["DISPLAY_IMG_HEIGHT"]),
						BX_RESIZE_IMAGE_PROPORTIONAL,
						true
					);
					$arResult["SECTIONS"][$key]["CHILDREN"][$keyChild]["PICTURE"] = array(
						"SRC" => $arFileTmp["src"],
						"WIDTH" => $arFileTmp["width"],
						"HEIGHT" => $arFileTmp["height"],
					);
				else:
					$arResult["SECTIONS"][$key]["CHILDREN"][$keyChild]["PICTURE"] = array(
						"SRC" => $arChild["PICTURE"]["SRC"],
						"WIDTH" => $arChild["PICTURE"]["WIDTH"],
						"HEIGHT" => $arChild["PICTURE"]["HEIGHT"],
					);
				endif;
			endif;
		endforeach;
	endif;
endforeach;?>