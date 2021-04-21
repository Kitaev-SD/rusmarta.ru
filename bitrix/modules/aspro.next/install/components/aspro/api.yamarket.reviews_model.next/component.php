<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */
?>
<?global $arTheme, $APPLICATION;?>
<?
$bError = false;

/*Prepare params*/
$arParams["CACHE_TYPE"] = "Y";
$arParams["CACHE_TIME"] = ($arParams["CACHE_TIME"] ? $arParams["CACHE_TIME"] : $arTheme["YANDEX_MARKET_CACHE_TIME"]["VALUE"]);

$arParams["ACCESS_TOKEN"] = ($arParams["ACCESS_TOKEN"] ? $arParams["ACCESS_TOKEN"] : $arTheme["YANDEX_MARKET_TOKEN_REVIEWS"]["VALUE"]);
$arParams["REVIEWS_GRADE"] = ($arParams["REVIEWS_GRADE"] ? $arParams["REVIEWS_GRADE"] : $arTheme["YANDEX_MARKET_GRADE_REVIEWS"]["VALUE"]);
$arParams["REVIEWS_TYPE_SORT"] = ($arParams["REVIEWS_TYPE_SORT"] ? $arParams["REVIEWS_TYPE_SORT"] : $arTheme["YANDEX_MARKET_SORT_REVIEWS"]["VALUE"]);
$arParams["REVIEWS_DIRECTION_SORT"] = ($arParams["REVIEWS_DIRECTION_SORT"] ? $arParams["REVIEWS_DIRECTION_SORT"] : $arTheme["YANDEX_MARKET_SORT_DIRECTION_REVIEWS"]["VALUE"]);
$arParams["REVIEWS_COUNT"] = ($arParams["REVIEWS_COUNT"] ? $arParams["REVIEWS_COUNT"] : $arTheme["YANDEX_MARKET_COUNT_REVIEWS"]["VALUE"]);

$arParams["YANDEX_MODEL_ID"] = (int)$arParams["YANDEX_MODEL_ID"];
$arParams["REVIEWS_COUNT"] = (int)$arParams["REVIEWS_COUNT"];
$arParams["PAGE"] = (int)$arParams["PAGE"];
/**/

if($arParams["REVIEWS_COUNT"] > 30 || $arParams["REVIEWS_COUNT"] <= 0)
	$arParams["REVIEWS_COUNT"] = 10;

if(!$arParams["ACCESS_TOKEN"])
{
	$bError = true;
	ShowError(GetMessage("NO_ACCESS_TOKEN"));
}
if(!$arParams["YANDEX_MODEL_ID"])
{
	$bError = true;
	ShowError(GetMessage("NO_YANDEX_MODEL_ID"));
}

if(!$bError)
{
	$arParams["COMPONENT_NAME"] = $componentName;
	$arParams["TEMPLATE"] = $componentTemplate;

	$fixGrade = 3; //bug fix api market for grade review

	$context=\Bitrix\Main\Application::getInstance()->getContext();
	$request=$context->getRequest();

	if($request->isPost() && $arParams["SHOW_REVIEW"] == "Y") //show yandex market reviews
	{
		if($this->StartResultCache())
		{
			$arTmpUrl = array();
			$sParamUrl = '';
			foreach($arParams as $key => $value)
			{
				switch($key):
					case "REVIEWS_GRADE":
						if($value)
							$arTmpUrl[] = "grade=".($value-$fixGrade);
						break;
					case "REVIEWS_COUNT":
						$arTmpUrl[] = "count=".$value;
						break;
					case "REVIEWS_TYPE_SORT":
						$arTmpUrl[] = "sort=".$value;
						break;
					case "REVIEWS_DIRECTION_SORT":
						$arTmpUrl[] = "how=".$value;
						break;
					case "PAGE":
						$arTmpUrl[] = "page=".$value;
						break;
				endswitch;
			}
			if($arTmpUrl)
				$sParamUrl = implode("&", $arTmpUrl);

			$query_page = "https://api.content.market.yandex.ru/v1/model/".$arParams["YANDEX_MODEL_ID"]."/opinion.json?".$sParamUrl;

			$headers = array(
				"Host: api.content.market.yandex.ru",
				"Accept: */*",
				"Authorization: ".$arParams["ACCESS_TOKEN"]
			);

			if(function_exists('curl_init'))
			{
				/*curl request*/
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_URL,$query_page);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				$data = curl_exec($ch);
				$err = curl_errno($ch);
				curl_close($ch);
				/**/
			}
			else
				$err = "function 'curl_init' doesn't exists";

			if($err == '0'):
				$arResponse = json_decode($data,true); //format response

				if(is_array($arResponse["modelOpinions"]) && $arResponse["modelOpinions"]):
					if(LANG_CHARSET != "UTF-8"): // convert form 'UTF-8' to site charset
						foreach($arResponse["modelOpinions"]["opinion"] as $key => $arItem)
						{
							$arResult["ITEMS"][$key] = $APPLICATION->ConvertCharsetArray($arItem, "UTF-8", LANG_CHARSET);
						}
					else:
						$arResult["ITEMS"] = $arResponse["modelOpinions"]["opinion"];
					endif;

					foreach($arResult["ITEMS"] as $key => $arItem)
					{
						$arItem["date"] = $date = substr($arItem["date"], 0, -3);;
						$arItem["grade"] = $arItem["grade"]+$fixGrade;
						$arResult["ITEMS"][$key] = $arItem;
					}

					$arResult["TOTAL_ITEMS"] = $arResponse["modelOpinions"]["total"];
					$arResult["PAGE_ELEMENT_COUNT"] = $arResponse["modelOpinions"]["count"];
					$arResult["CURRENT_PAGE"] = $arParams["PAGE"];
					$arResult["TOTAL_PAGES"] = ceil($arResult["TOTAL_ITEMS"]/$arParams["REVIEWS_COUNT"]);

					/*prepare pagination*/
					$count_item_between_cur_page = 2; // count numbers left and right from cur page
					$count_item_dotted = 2; // count numbers to end or start pages

					$arResult["PAGINATION"]["START_PAGE"] = $arResult["CURRENT_PAGE"] - $count_item_between_cur_page;
					$arResult["PAGINATION"]["START_PAGE"] = $arResult["PAGINATION"]["START_PAGE"] <= 0 ? 1 : $arResult["PAGINATION"]["START_PAGE"];
					$arResult["PAGINATION"]["END_PAGE"] = $arResult["CURRENT_PAGE"] + $count_item_between_cur_page;
					$arResult["PAGINATION"]["END_PAGE"] = $arResult["PAGINATION"]["END_PAGE"] > $arResult["TOTAL_PAGES"] ? $arResult["TOTAL_PAGES"] : $arResult["PAGINATION"]["END_PAGE"];

					if($arResult["CURRENT_PAGE"] == 1)
						$arResult["PAGINATION"]["PREV_DISABLED"] = true;
					elseif($arResult["CURRENT_PAGE"] < $arResult["TOTAL_PAGES"])
						$arResult["PAGINATION"]["PREV_DISABLED"] = false;
					if($arResult["CURRENT_PAGE"] == $arResult["TOTAL_PAGES"])
						$arResult["PAGINATION"]["NEXT_DISABLED"] = true;
					else
						$arResult["PAGINATION"]["NEXT_DISABLED"] = false;

					$arResult["PAGINATION"]["CI_BETWEEN_CUR_PAGE"] = $count_item_between_cur_page;
					$arResult["PAGINATION"]["CI_DOTTED"] = $count_item_dotted;
					/**/

					unset($arResponse);

				elseif($arResponse["errors"]):
					$arResult["ERROR"] = $arResponse["errors"];

					$this->AbortResultCache();
				else:
					$arResult["ERROR"] = "Empty response";

					$this->AbortResultCache();
				endif;
			else:
				if($err)
					$arResult["ERROR"] = $err;
				else
					$arResult["ERROR"] = "Empty response";

				$this->AbortResultCache();
			endif;

			$this->IncludeComponentTemplate('reviews');
		}
	}
	else // show container and get yandex market reviews
	{
		$this->IncludeComponentTemplate();
	}

}?>