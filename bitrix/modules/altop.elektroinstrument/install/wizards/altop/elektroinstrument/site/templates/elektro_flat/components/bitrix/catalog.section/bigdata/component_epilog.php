<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

//JS_CORE//
CJSCore::Init(array('popup', 'ajax', 'fx'));

//BIG_DATA_JSON_ANSWERS//
$request = \Bitrix\Main\Context::getCurrent()->getRequest();
if($request->isAjaxRequest() && $request->get('action') === 'deferredLoad') {
	$content = ob_get_contents();
	ob_end_clean();

	list(, $itemsContainer) = explode('<!-- items-container -->', $content);
	
	$component::sendJsonAnswer(array(
		'items' => $itemsContainer
	));
}