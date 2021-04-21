<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if(count($arResult["PERSON_TYPE"]) > 1) {?>
	
	<h2><?=GetMessage("SOA_TEMPL_PERSON_TYPE")?></h2>
	<div class="person_type">
		<div class="person_type_in">
			<table>
				<tbody>
					<?foreach($arResult["PERSON_TYPE"] as $v) {?>
						<tr>
							<td style="vertical-align:top;">
								<input type="radio" id="PERSON_TYPE_<?=$v["ID"]?>" name="PERSON_TYPE" value="<?= $v["ID"] ?>"<?if ($v["CHECKED"]=="Y") echo " checked=\"checked\"";?> onclick="submitForm()">
							</td>
							<td>
								<label for="PERSON_TYPE_<?=$v["ID"]?>"><?=$v["NAME"] ?></label>
							</td>
						</tr>
					<?}?>
				</tbody>
			</table>
		</div>
	</div>
	<input type="hidden" name="PERSON_TYPE_OLD" value="<?=$arResult["USER_VALS"]["PERSON_TYPE_ID"]?>">

<?} else {
	
	if(IntVal($arResult["USER_VALS"]["PERSON_TYPE_ID"]) > 0) {?>
		<input type="hidden" name="PERSON_TYPE" value="<?=IntVal($arResult["USER_VALS"]["PERSON_TYPE_ID"])?>">
		<input type="hidden" name="PERSON_TYPE_OLD" value="<?=IntVal($arResult["USER_VALS"]["PERSON_TYPE_ID"])?>">
	<?} else {
		foreach($arResult["PERSON_TYPE"] as $v) {?>
			<input type="hidden" id="PERSON_TYPE" name="PERSON_TYPE" value="<?=$v["ID"]?>">11
			<input type="hidden" name="PERSON_TYPE_OLD" value="<?=$v["ID"]?>">
		<?}
	}

}?>