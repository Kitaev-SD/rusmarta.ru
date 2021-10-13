<?

namespace Acrit\Core\Export\Plugins;

use \Acrit\Core\Helper;

Helper::loadMessages();
?>
<tr id="tr_TRANSFORM_FIELDS" >
   <td class="adm-detail-content-cell-l">
      <label><span class="adm-required-field"><?= static::getMessage('SETTINGS_TITLE'); ?>:</span></label>
   </td>
   <td class="adm-detail-content-cell-r">
      <div>

         <input type="text" name="PROFILE[PARAMS][TITLE]" value="<?= htmlspecialcharsbx($this->arParams['TITLE']); ?>"
                data-role="acrit_exp_facebook_title" size="50" maxlength="20" />
      </div>

   </td>
</tr>

