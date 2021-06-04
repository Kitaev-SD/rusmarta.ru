<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Json;

# Format request (pretty)
$strJsonRequest = Json::encode($arJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
if(!Helper::isUtf()){
	$strJsonRequest = Helper::convertEncoding($strJsonRequest, 'UTF-8', 'CP1251');
}
$strJsonRequest = htmlspecialcharsbx($strJsonRequest);

# Format request (raw)
$strJsonRequestRaw = Json::encode($arJson);
$strJsonRequestRaw = htmlspecialcharsbx($strJsonRequestRaw);

# Get headers
$arResponseHeaders = $this->API->getHeaders();

# Get errror
$strErrorDetails = null;
if(is_array($arQueryResult['error'])){
	$strErrorDetails = htmlspecialcharsbx($arQueryResult['error']['message']); // Bad: wrong addins cause: map[err:Пожалуйста, выберите Рос.размер из нашего справочника! Значение 22 не поддерживается. Штрихкод: 61 ]
	$strErrorDetailsCause = htmlspecialcharsbx($arQueryResult['error']['data']['cause']['err']); // Good: Пожалуйста, выберите Рос.размер из нашего справочника! Значение 22 не поддерживается. Штрихкод: 61 
	if(strlen($strErrorDetailsCause)){
		$strErrorDetails = $strErrorDetailsCause;
	}
}
elseif(in_array('HTTP/1.1 502 Bad Gateway', $arResponseHeaders)){
	$strErrorDetails = 'HTTP/1.1 502 Bad Gateway';
}
$strDisplayMessageTitle = static::getMessage('ERROR_EXPORT_ITEMS_BY_API');

print Helper::showError($strDisplayMessageTitle, $strErrorDetails);
?>
<div>
	<div>
		<a href="#" class="acrit-inline-link" data-role="acrit_wb_preview_error_json">
			<?=static::getMessage('ERROR_PREVIEW_ARRAY');?>
		</a>
	</div>
	<div style="display:none; word-break:break-all;">
		<?Helper::P($arQueryResult);?>
	</div>
</div>
<div style="margin-top:8px;">
	<div>
		<a href="#" class="acrit-inline-link" data-role="acrit_wb_preview_error_json">
			<?=static::getMessage('ERROR_PREVIEW_JSON');?>
		</a>
	</div>
	<div style="display:none; word-break:break-all;">
		<?Helper::P($strJsonRequest);?>
	</div>
</div>
<div style="margin-top:8px;">
	<div>
		<a href="#" class="acrit-inline-link" data-role="acrit_wb_preview_error_json">
			<?=static::getMessage('ERROR_PREVIEW_JSON_RAW');?>
		</a>
	</div>
	<div style="display:none; word-break:break-all;">
		<?Helper::P($strJsonRequestRaw);?>
	</div>
</div>
<div style="margin-top:8px;">
	<div>
		<a href="#" class="acrit-inline-link" data-role="acrit_wb_preview_error_json">
			<?=static::getMessage('ERROR_PREVIEW_HEADERS');?>
		</a>
	</div>
	<div style="display:none; word-break:break-all;">
		<?Helper::P($arResponseHeaders);?>
	</div>
</div>
<script>
if(AcritExpPopupExecute.PARTS.CONTENT_DATA.clientWidth < 800){
	AcritExpPopupExecute.SetSize({width:800});
}
$('a[data-role="acrit_wb_preview_error_json"]').bind('click', function(e){
	e.preventDefault();
	$(this).parent().next().toggle();
});
</script>
