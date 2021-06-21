<?
namespace Acrit\Core\Export;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Plugins\WildberriesHelpers\HistoryTable as History;

# More data
$arHistoryItems = [];
if($arTask['ID']){
	$arQuery = [
		'filter' => [
			'PROFILE_ID' => $this->intProfileId,
			'TASK_ID' => $arTask['ID'],
		],
	];
	$resHistoryItems = History::getList($arQuery);
	while($arHistoryItem = $resHistoryItems->fetch()){
		$arHistoryItems[] = $arHistoryItem;
	}
}
if(!empty($arHistoryItems)){
	?>
		<a data-role="log-tasks-status-toggle" class="acrit-inline-link"><?=static::getMessage('STATUS_TOGGLE');?></a>
		<div data-role="log-tasks-status-details-table">
			<table>
				<thead>
					<tr>
						<th style="16px;"></th>
						<th>RequestId</th>
						<th>ElementId</th>
						<th>vendorCode</th>
						<th>imtId</th>
						<th>nmId</th>
					</tr>
				</thead>
				<tbody>
					<?foreach($arHistoryItems as $arItem):?>
						<tr>
							<td>
								<?$bSuccess = $arItem['SUCCESS'] == 'Y';?>
								<img src="/bitrix/themes/.default/images/lamp/<?=($bSuccess ? 'green' : 'red')?>.gif" alt="" 
									style="margin-bottom:2px;vertical-align:middle;"/>
							</td>
							<td>
								<a data-role="log-tasks-status-preview" class="acrit-inline-link" style="white-space:nowrap;"
									data-id="<?=$arItem['ID'];?>"><?=$arItem['REQUEST_ID'];?></a>
							</td>
							<td align="right">
								<?=$arItem['ELEMENT_ID'];?>
							</td>
							<td>
								<?=$arItem['VENDOR_CODE'];?>
							</td>
							<td>
								<?=$arItem['IMT_ID'];?>
							</td>
							<td>
							<?=$arItem['NM_ID'];?>
							</td>
						</tr>
					<?endforeach?>
				</tbody>
			</table>
		</div>
	<?
}

?>