<?
/**
 * Acrit Core: create tables for WB
 * @documentation https://suppliers-portal.wildberries.ru/goods/products-card/
 */

namespace Acrit\Core\Export\Plugins\WildberriesRuHelpers;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Json,
	\Acrit\Core\Export\Plugins\WildberriesHelpers\HistoryStockTable as HistoryStock;

?>
<div id="acrit_wb_json_preview_popup">
	<?
	$arSubTabs = [];
	$arSubTabs[] = [
		'DIV' => 'json_formatted', 
		'TAB' => static::getMessage('TAB_FORMATTED'), 
		'ONSELECT' => 'acritExpWbPopupJsonChangeTab();',
	];
	$arSubTabs[] = [
		'DIV' => 'json_unformatted', 
		'TAB' => static::getMessage('TAB_UNFORMATTED'), 
		'ONSELECT' => 'acritExpWbPopupJsonChangeTab();',
	];
	$arSubTabs[] = [
		'DIV' => 'response', 
		'TAB' => static::getMessage('TAB_RESPONSE'), 
		'ONSELECT' => 'acritExpWbPopupJsonChangeTab();',
	];
	if($arParams['SHOW_STOCKS']){
		$arSubTabs[] = [
			'DIV' => 'stocks', 
			'TAB' => static::getMessage('TAB_STOCKS'), 
			'ONSELECT' => 'acritExpWbPopupJsonChangeTab();',
		];
	}
	$obTabControl = new \CAdminViewTabControl('AcritExpWbJsonPreview', $arSubTabs);
	$obTabControl->begin();
	$obTabControl->beginNextTab();
	$arJson = Json::decode($strJson);
	$strJsonFormatted = Json::encode($arJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
	if(!Helper::isUtf()){
		$strJsonFormatted = Helper::convertEncoding($strJsonFormatted, 'UTF-8', 'CP1251');
	}
	?>
		<pre style="margin-top:0;"><code class="json"><?=$strJsonFormatted;?></code></pre>
		<div data-role="acrit_wb_json_copy_source" style="height:1px; width:1px; overflow:hidden; white-space:pre;"><?
			print $strJsonFormatted;
		?></div>
	<?
	$obTabControl->beginNextTab();
	?>
		<pre style="margin-top:0;"><code class="json"><?=$strJson;?></code></pre>
		<div data-role="acrit_wb_json_copy_source" style="height:1px; width:1px; overflow:hidden;"><?
			print $strJson;
		?></div>
	<?
	$obTabControl->beginNextTab();
	?>
		<div>
			<img src="/bitrix/themes/.default/images/lamp/<?=($arArray['SUCCESS'] == 'Y' ? 'green' : 'red')?>.gif" alt="" />
		</div>
		<?Helper::P($arArray['RESPONSE']);?>
	<?if($arParams['SHOW_STOCKS']):?>
		<?
		$obTabControl->beginNextTab();
		# Prepare variations
		$arVariations = [];
		foreach($arJson['params']['card']['nomenclatures'][0]['variations'] as $arVariation){
			$arVariations[$arVariation['chrtId']] = $arVariation['barcode'];
		}
		# Prepare stocks
		$arStocks = [];
		if(($intTaskId = $arParams['TASK_ID']) && ($nmId = $arJson['params']['card']['nomenclatures'][0]['nmId'])){
			$arQuery = [
				'order' => [
					'CHRT_ID' => 'ASC',
				],
				'filter' => [
					'TASK_ID' => $intTaskId,
					'NM_ID' => $nmId,
				],
				'select' => [
					'CHRT_ID',
					'PRICE',
					'QUANTITY',
					'STORE_ID',
					'SUCCESS',
				],
			];
			$resStocks = HistoryStock::getList($arQuery);
			while($arStock = $resStocks->fetch()){
				$chrtId = $arStock['CHRT_ID'];
				unset($arStock['CHRT_ID']);
				if(!is_array($arStocks[$chrtId])){
					$arStocks[$chrtId] = [];
				}
				$arStocks[$chrtId][] = $arStock;
			}
		}
		?>
		<div data-role="log-tasks-status-details-table" style="display:block;">
			<table>
				<thead>
					<tr>
						<th></th>
						<th><?=static::getMessage('STOCK_CHRT_ID');?></th>
						<th><?=static::getMessage('STOCK_BARCODE');?></th>
						<th><?=static::getMessage('STOCK_QUANTITY');?></th>
						<th><?=static::getMessage('STOCK_PRICE');?></th>
						<th><?=static::getMessage('STOCK_STORE_ID');?></th>
					</tr>
				</thead>
				<tbody>
					<?foreach($arStocks as $chrtId => $arStocks):?>
						<?$intRows = count($arStocks);?>
						<?foreach($arStocks as $key => $arStock):?>
							<tr>
								<?if($key == 0):?>
									<td colspan="<?=$intRows;?>" align="right">
										<?$bSuccess = $arStock['SUCCESS'] == 'Y';?>
										<img src="/bitrix/themes/.default/images/lamp/<?=($bSuccess ? 'green' : 'red')?>.gif" alt="" 
											style="margin-bottom:2px;vertical-align:middle;"/>
									</td>
									<td colspan="<?=$intRows;?>" align="right">
										<?=$chrtId;?>
									</td>
									<td colspan="<?=$intRows;?>">
										<?=$arVariations[$chrtId];?>
									</td>
								<?endif?>
								<td align="right">
									<?=$arStock['QUANTITY']?>
								</td>
								<td align="right">
									<?=$arStock['PRICE']?>
								</td>
								<td align="right">
									<?=$arStock['STORE_ID']?>
								</td>
							</tr>
						<?endforeach?>
					<?endforeach?>
				</tbody>
			</table>
		</div>
	<?endif?>
	<?
	$obTabControl->end();
	?>
	<?if($arParams['ALLOW_COPY']):?>
		<script>
			$('#acrit_wb_json_preview_popup > .adm-detail-subtabs-block').append(
				$('<span class="adm-detail-subtabs"/>')
					.attr('id', 'acrit_wb_json_preview_popup_copy')
					.text('<?=static::getMessage('JSON_COPY');?>')
					.css({background:'transparent', color:'green'})
					.bind('click', function(e){
						let
							element = $('#acrit_wb_json_preview_popup div[data-role="acrit_wb_json_copy_source"]:visible');
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
<script>
function acritExpWbPopupJsonChangeTab(){
	let tab = $('#acrit_wb_json_preview_popup .adm-detail-subtab-active'),
		tabCode = tab.attr('id').replace(/^view_tab_/, ''),
		bntCopy = $('#acrit_wb_json_preview_popup_copy');
	if(tabCode.match(/^json_/)){
		bntCopy.show();
	}
	else{
		bntCopy.hide();
	}
}
acritExpWbPopupJsonChangeTab();
</script>