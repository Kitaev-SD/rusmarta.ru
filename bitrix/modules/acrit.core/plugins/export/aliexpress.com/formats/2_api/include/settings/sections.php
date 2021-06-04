<?
namespace Acrit\Core\Export\Plugins;

$arSelected = $this->arProfile['PARAMS']['SECTION'];
if (!is_array($arSelected) || empty($arSelected)) {
	$arSelected = [];
}
?>
<script>
    var acrit_core_aliexpress_sections = <?=json_encode($arSelected);?>
</script>
<div class="acrit-core-aliexpress-sections">
    <select name="PROFILE[PARAMS][SECTION][]" class="acrit-core-aliexpress-section">
        <option value=""><?=static::getMessage('SECTION_EMPTY');?></option>
    </select>
</div>
