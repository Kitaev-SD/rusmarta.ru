<?
namespace Acrit\Core\Export;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Plugins\OzonRuHelpers\HistoryTable as History;

# Statuses
$intSuccess = 0;
if(is_array($arStatus['Status'])){
	foreach($arStatus['Status'] as $strStatus => $intCount){
		$strStatusUpper = toUpper($strStatus);
		print sprintf(static::getMessage('STATUS_'.$strStatusUpper), $intCount);
		if($strStatusUpper != toUpper('Pending')){
			$intSuccess = $intCount;
		}
	}
}

# More data
$arHistoryItems = [];
if($arTask['ID']){
	$arQuery = [
		'filter' => [
			'PROFILE_ID' => $this->intProfileId,
		],
		'select' => [
			'ID',
			'OFFER_ID',
			'PRODUCT_ID',
			'JSON',
			'STATUS',
			'STATUS_DATETIME',
		],
	];
	if($this->isStockAndPrice()){
		$arQuery['filter']['TASK_ID'] = $arTask['ID'];
	}
	else{
		$arQuery['filter']['TASK_ID_OZON'] = $arTask['TASK_ID'];
	}
	$resHistoryItems = History::getList($arQuery);
	while($arHistoryItem = $resHistoryItems->fetch()){
		$arHistoryItems[] = $arHistoryItem;
	}
}
if(!empty($arHistoryItems)){
	# Display count
	$intCount = $arStatus['Count'];
	if($this->isStockAndPrice()){
		$intCount = null;
	}
	if($intCount){
		print sprintf(static::getMessage('STATUS_COUNT', ['#COUNT#' => $intCount]));
	}
	?>
		<a data-role="log-tasks-status-toggle" class="acrit-inline-link"><?=static::getMessage('STATUS_TOGGLE');?></a>
		<div data-role="log-tasks-status-details-table">
			<table>
				<thead>
					<tr>
						<th>OfferID</th>
						<th>ProductID</th>
						<?if(!$this->isStockAndPrice()):?>
							<th>Status</th>
						<?endif?>
					</tr>
				</thead>
				<tbody>
					<?foreach($arHistoryItems as $arItem):?>
						<tr>
							<td align="right">
								<a data-role="log-tasks-status-preview" class="acrit-inline-link" 
									data-id="<?=$arItem['ID'];?>"><?=$arItem['OFFER_ID'];?></a>
							</td>
							<td align="right">
								<?=$arItem['PRODUCT_ID'];?>
							</td>
							<?if(!$this->isStockAndPrice()):?>
								<td>
									<?=$arItem['STATUS'];?>
								</td>
							<?endif?>
						</tr>
					<?endforeach?>
				</tbody>
			</table>
		</div>
	<?
}


?>