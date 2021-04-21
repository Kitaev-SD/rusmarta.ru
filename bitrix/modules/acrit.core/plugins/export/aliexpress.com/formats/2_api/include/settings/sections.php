<?
namespace Acrit\Core\Export\Plugins;

$arSelected = $this->arProfile['PARAMS']['SECTION'];
if (!is_array($arSelected) || empty($arSelected)) {
	$arSelected = [];
}

$arSList = AliexpressComApi::getAliCategories(0);
$section_id = 0;
foreach ($arSelected as $section_id):
    $arSIDs = [];
    foreach ($arSList as $item) {
        $arSIDs[] = $item['id'];
    }
	if (in_array($section_id, $arSIDs)) {
?>
    <select name="PROFILE[PARAMS][SECTION][]">
        <option value="">Выбрать вариант</option>
		<?foreach ($arSList as $item):?>
        <option value="<?=$item['id'];?>"<?=($item['id'] == $section_id)?' selected':''?>><?=$item['name'];?></option>
		<?endforeach;?>
    </select><br />
<?
	    $arSList = AliexpressComApi::getAliCategories($section_id);
    }
endforeach;
?>
<?if (!empty($arSList)):?>
<select name="PROFILE[PARAMS][SECTION][]">
    <option value="">Выбрать вариант</option>
	<?foreach ($arSList as $item):?>
    <option value="<?=$item['id'];?>"<?=($item['id'] == $section_id)?' selected':''?>><?=$item['name'];?></option>
	<?endforeach;?>
</select>
<?endif;?>
