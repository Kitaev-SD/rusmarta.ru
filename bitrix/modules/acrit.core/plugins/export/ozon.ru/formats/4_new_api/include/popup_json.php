<?
/**
 * Acrit Core: create tables for ozon
 * @documentation https://docs.ozon.ru/api/seller
 */

namespace Acrit\Core\Export\Plugins\OzonRuHelpers;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Json,
	\Acrit\Core\Export\Exporter;
?>
<div id="acrit_exp_ozon_json_preview_popup">
	<?
	$arSubTabs = [];
	$arSubTabs[] = [
		'DIV' => 'json_formatted', 
		'TAB' => static::getMessage('TAB_FORMATTED'), 
	];
	$arSubTabs[] = [
		'DIV' => 'json_unformatted', 
		'TAB' => static::getMessage('TAB_UNFORMATTED'), 
	];
	$obTabControl = new \CAdminViewTabControl('AcritExpOzonJsonPreview', $arSubTabs);
	$obTabControl->begin();
	$obTabControl->beginNextTab();
	$arJson = Json::decode($strJson);
	$strJsonFormatted = Json::encode($arJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
	if(!Helper::isUtf()){
		$strJsonFormatted = Helper::convertEncoding($strJsonFormatted, 'UTF-8', 'CP1251');
	}
	?>
		<pre style="margin-top:0;"><code class="json"><?=$strJsonFormatted;?></code></pre>
		<div data-role="acrit_ozon_json_copy_source" style="height:1px; width:1px; overflow:hidden; white-space:pre;"><?
			print $strJsonFormatted;
		?></div>
	<?
	$obTabControl->beginNextTab();
	?>
		<pre style="margin-top:0;"><code class="json"><?=$strJson;?></code></pre>
		<div data-role="acrit_ozon_json_copy_source" style="height:1px; width:1px; overflow:hidden;"><?
			print $strJson;
		?></div>
	<?
	$obTabControl->end();
	?>
	<?if($arParams['ALLOW_COPY']):?>
		<script>
			$('#acrit_exp_ozon_json_preview_popup > .adm-detail-subtabs-block').append(
				$('<span class="adm-detail-subtabs"/>')
					.text('<?=static::getMessage('JSON_COPY');?>')
					.css({background:'transparent', color:'green'})
					.bind('click', function(e){
						let
							element = $('#acrit_exp_ozon_json_preview_popup div[data-role="acrit_ozon_json_copy_source"]:visible');
						e.preventDefault();
						console.log(element.get(0));
						acritCoreCopyToClipboard(element.get(0), function(){
							alert('<?=static::getMessage('JSON_COPIED');?>');
						});
					})
			);
		</script>
	<?endif?>
</div>