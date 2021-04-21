<?define("STATISTIC_SKIP_ACTIVITY_CHECK", "true");?>
<?define('STOP_STATISTICS', true);
define('PUBLIC_AJAX_MODE', true);?>
<?require_once($_SERVER['DOCUMENT_ROOT']. "/bitrix/modules/main/include/prolog_before.php");?>

<?
$context=\Bitrix\Main\Application::getInstance()->getContext();
$request=$context->getRequest();

if($request->isPost() && $request->get("ACCESS_TOKEN") && $request->get("YANDEX_MODEL_ID")):?>
	<?$arParams = $request->getPostList()->toArray();?>
	<?/*$APPLICATION->IncludeComponent(
		"aspro:api.yamarket.reviews_model.tires2",
		"",
		$arParams
	);*/?>
	<?$token = $request->get("ACCESS_TOKEN");
	$model = $request->get("YANDEX_MODEL_ID");
	$page = $request->get("PAGE");


	$query_page = 'https://api.content.market.yandex.ru/v1/model/'.$model.'/opinion.json?geo_id=0&page='.$page.$strOptions;

	$headers = array(
		"Host: api.content.market.yandex.ru",
		"Accept: */*",
		"Authorization: ".$token
	);
	// echo time();
	/*curl request*/
	/*$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL,$query_page);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	$data = curl_exec($ch);
	$err = curl_errno($ch);
	curl_close($ch);

	if($err == '0'):
		$arReviews = json_decode($data, true);
	else
		$arReviews = $err;*/
	print_r($arReviews);?>
<?endif;?>