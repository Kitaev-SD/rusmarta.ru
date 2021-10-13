<?
/**
 * Acrit Core: Yandex marketplace tables
 */

namespace Acrit\Core\Export\Plugins\YandexMarketplaceHelpers;

use
	\Acrit\Core\Helper,
	\Bitrix\Main\Web\Json,
	\Acrit\Core\Export\Plugins\YandexMarketplaceHelpers\StockHistoryTable as StockHistory;

Helper::loadMessages(__FILE__);

$strLogCustomTitle = static::getMessage('LOG_STOCKS_HEADING');

$arNavParams = array(
	'page' => is_numeric($arGet['page']) ? $arGet['page'] : 1,
	'size' => is_numeric($arGet['size']) ? $arGet['size'] : 10,
);

$arStocksHistory = array();
$arStocksFilter = array(
	'PROFILE_ID' => $this->intProfileId,
);
$arQuery = [
	'filter' => $arStocksFilter,
	'order' => array(
		'ID' => 'DESC',
	),
	'limit' => $arNavParams['size'],
	'offset' => ($arNavParams['page'] - 1) * $arNavParams['size']
];
$resStocks = StockHistory::getList($arQuery);
while($arStock = $resStocks->fetch()){
	$arStocksHistory[] = $arStock;
}

# Nav object
$obNav = new \Bitrix\Main\UI\AdminPageNavigation('acrit-exp-nav-stocks');
$arQuery = [
	'filter' => $arStocksFilter,
	'select' => ['CNT'],
	'runtime' => array(
		new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(*)'),
	)
];
$obNav->setRecordCount(StockHistory::getList($arQuery)->fetch()['CNT']);
$obNav->setCurrentPage($arNavParams['page']);
$obNav->setPageSize($arNavParams['size']);
?>
<div data-role="yandex_market_stocks_log">
	<div>
		<input type="button" data-role="acrit_exp_yandex_marketplace_stocks_reload"
			value="<?=static::getMessage('LOG_STOCKS_REFRESH');?>" />
	</div>
	<br/>
	<?if(!empty($arStocksHistory)):?>
		<table class="adm-list-table acrit-exp-table-stocks" style="table-layout:fixed;">
			<thead>
				<tr class="adm-list-table-header">
					<td class="adm-list-table-cell" style="width:50px;">
						<div class="adm-list-table-cell-inner" style="white-space:nowrap;">
							<?=static::getMessage('LOG_STOCKS_ID');?>
						</div>
					</td>
					<td class="adm-list-table-cell" style="width:150px;">
						<div class="adm-list-table-cell-inner" style="white-space:nowrap;">
							<?=static::getMessage('LOG_STOCKS_TIMESTAMP_X');?>
						</div>
					</td>
					<td class="adm-list-table-cell" style="width:150px;">
						<div class="adm-list-table-cell-inner" style="white-space:nowrap;">
							<?=static::getMessage('LOG_STOCKS_WAREHOUSE_ID');?>
						</div>
					</td>
					<td class="adm-list-table-cell">
						<div class="adm-list-table-cell-inner" style="white-space:nowrap;">
							<?=static::getMessage('LOG_STOCKS_SKUS_OUTPUT');?>
						</div>
					</td>
				</tr>
			</thead>
			<tbody>
				<?foreach($arStocksHistory as $arStock):?>
					<tr class="adm-list-table-row" data-stopped="<?=$arStock['STOPPED'];?>" data-task-id="<?=$arStock['TASK_ID'];?>">
						<td class="adm-list-table-cell align-right" style="white-space:nowrap;">
							<?=$arStock['ID'];?>
						</td>
						<td class="adm-list-table-cell align-right" style="white-space:nowrap;">
							<?=is_object($arStock['TIMESTAMP_X']) ? $arStock['TIMESTAMP_X']->toString() : '';?>
						</td>
						<td class="adm-list-table-cell">
							<?=$arStock['WAREHOUSE_ID'];?>
						</td>
						<td class="adm-list-table-cell">
							<?if(Helper::strlen($arStock['SKUS_OUTPUT'])):?>
								<?
								$arStock['SKUS_OUTPUT'] = Json::encode(Json::decode($arStock['SKUS_OUTPUT']), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
								?>
								<a href="javascript:void(0)" class="acrit-inline-link" data-role="log-stocks-item-json"><?=static::getMessage('LOG_STOCKS_SKUS_OUTPUT_DETAILS');?></a>
								<div style="display:none">
									<pre><code class="json"><?print_r($arStock['SKUS_OUTPUT']);?></code></pre>
								</div>
							<?endif?>
						</td>
					</tr>
				<?endforeach?>
			</tbody>
		</table>
		<?/**/?>
		<script>
		AcritExpStocksTable = {
			GetAdminList: function(url){
				if(params = url.match(/page-(\d+)-size-(\d+)/)){
					$('input[data-role="acrit_exp_yandex_marketplace_stocks_reload"]').trigger('click', {
						page: params[1],
						size: params[2]
					});
				}
			}
		}
		$('.acrit-exp-table-stocks pre code').each(function(i, block) {
			highlighElement(block);
		});
		$('a[data-role="log-stocks-item-json"]').bind('click', function(e){
			e.preventDefault();
			let div = $(this).next();
			if(!div.is(':animated')){
				$(this).next().slideToggle();
			}
		});
		</script>
		<style>
		#tr_LOG_CUSTOM .adm-nav-pages-number-block {
			display:none!important;
		}
		</style>
		<?
		$_REQUEST['admin_history'] = '';
		$GLOBALS['APPLICATION']->IncludeComponent(
			"bitrix:main.pagenavigation",
			"admin",
			array(
				"SEF_MODE" => "N",
				"NAV_OBJECT" => $obNav,
				"TITLE" => "",
				"PAGE_WINDOW" => 10,
				"SHOW_ALWAYS" => "Y",
				"TABLE_ID" => "AcritExpStocksTable",
			),
			false,
			array(
				"HIDE_ICONS" => "Y",
			)
		);
		unset($_REQUEST['admin_history']);
		?>
		<div style="clear:both"></div>
	<?else:?>
		<p><?=static::getMessage('LOG_TASKS_EMPTY');?></p>
	<?endif?>
</div>