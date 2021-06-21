<?
namespace Acrit\Core\Export;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Plugins\WildberriesHelpers\TaskTable as Task;

Helper::loadMessages(__FILE__);

$strLogCustomTitle = static::getMessage('LOG_TASKS_HEADING');

$arNavParams = array(
	'page' => is_numeric($arGet['page']) ? $arGet['page'] : 1,
	'size' => is_numeric($arGet['size']) ? $arGet['size'] : 10,
);

$arProfileTasksAll = array();
$arTasksFilter = array(
	'PROFILE_ID' => $this->intProfileId,
	'>PRODUCTS_COUNT' => 0,
);
$arQuery = [
	'filter' => $arTasksFilter,
	'order' => array(
		'ID' => 'DESC',
	),
	'limit' => $arNavParams['size'],
	'offset' => ($arNavParams['page'] - 1) * $arNavParams['size']
];
$resTasks = Task::getList($arQuery);
while($arTask = $resTasks->fetch()){
	$arProfileTasksAll[] = $arTask;
}

# Nav object
$obNav = new \Bitrix\Main\UI\AdminPageNavigation('acrit-exp-nav-tasks');
$arQuery = [
	'filter' => $arTasksFilter,
];
#$resTasks = Task::getList($arQuery);
$obNav->setRecordCount($resTasks->getSelectedRowsCount());
unset($resTasks);
$obNav->setCurrentPage($arNavParams['page']);
$obNav->setPageSize($arNavParams['size']);
?>
<div data-role="wb_tasks_log">
	<div>
		<input type="button" data-role="log-tasks-refresh"
			value="<?=static::getMessage('LOG_TASKS_REFRESH');?>" />
	</div>
	<br/>
	<?if(!empty($arProfileTasksAll)):?>
		<table class="adm-list-table acrit-exp-table-tasks">
			<thead>
				<tr class="adm-list-table-header">
					<td class="adm-list-table-cell" style="width:1px;">
						<div class="adm-list-table-cell-inner" style="white-space:nowrap;">
							<?=static::getMessage('LOG_TASKS_ID');?>
						</div>
					</td>
					<td class="adm-list-table-cell" style="width:1px;">
						<div class="adm-list-table-cell-inner" style="white-space:nowrap;">
							<?=static::getMessage('LOG_TASKS_PRODUCTS_COUNT');?>
						</div>
					</td>
					<td class="adm-list-table-cell">
						<div class="adm-list-table-cell-inner">
							<?=static::getMessage('LOG_TASKS_STATUS');?>
						</div>
					</td>
					<td class="adm-list-table-cell" style="width:135px;">
						<div class="adm-list-table-cell-inner">
							<?=static::getMessage('LOG_TASKS_STATUS_STOCK');?>
						</div>
					</td>
					<td class="adm-list-table-cell" style="width:1px;">
						<div class="adm-list-table-cell-inner" style="white-space:nowrap;">
							<?=static::getMessage('LOG_TASKS_TIMESTAMP_X');?>
						</div>
					</td>
				</tr>
			</thead>
			<tbody>
				<?foreach($arProfileTasksAll as $arTask):?>
					<tr class="adm-list-table-row" data-stopped="<?=$arTask['STOPPED'];?>" data-task-id="<?=$arTask['ID'];?>">
						<td class="adm-list-table-cell align-right" style="white-space:nowrap;">
							<?=$arTask['ID'];?>
						</td>
						<td class="adm-list-table-cell align-right">
							<?=$arTask['PRODUCTS_COUNT'];?>
						</td>
						<td class="adm-list-table-cell align-right">
							<div data-role="log-tasks-item-status" style="text-align:left;">
								<?=$this->displayTaskStatus($arTask);?>
							</div>
						</td>
						<td class="adm-list-table-cell">
							<?if(Helper::strlen($arTask['STOCKS_REQUEST'])):?>
								<a data-role="log-tasks-status-stocks-preview" class="acrit-inline-link">
									<?=static::getMessage('LOG_TASKS_STATUS_STOCK_VIEW');?>
								</a>
							<?endif?>
						</td>
						<td class="adm-list-table-cell align-right" style="white-space:nowrap;">
							<?=is_object($arTask['TIMESTAMP_X']) ? $arTask['TIMESTAMP_X']->toString() : '';?>
						</td>
					</tr>
				<?endforeach?>
			</tbody>
		</table>
		<?/**/?>
		<script>
		AcritExpTasksTable = {
			GetAdminList: function(url){
				if(params = url.match(/page-(\d+)-size-(\d+)/)){
					$('input[data-role="log-tasks-refresh"]').trigger('click', {
						page: params[1],
						size: params[2]
					});
				}
			}
		}
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
				"TABLE_ID" => "AcritExpTasksTable",
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