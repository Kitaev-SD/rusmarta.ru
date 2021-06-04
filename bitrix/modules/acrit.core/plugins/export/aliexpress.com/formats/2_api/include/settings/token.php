<?
namespace Acrit\Core\Export\Plugins;

use \Acrit\Core\Helper;
?>
<div id="acrit-module-update-notifier">
    <div class="acrit-exp-note-compact">
        <div class="adm-info-message-wrap">
            <div class="adm-info-message"><?=static::getMessage('TOKEN_HELP');?></div>
        </div>
    </div>
</div>
<p><a href="<?=$this->getTokenLink();?>" target="_blank"><?=static::getMessage('GET_TOKEN');?></a></p>
<input type="text" name="PROFILE[PARAMS][TOKEN]" size="40" spellcheck="false"
	data-role="acrit_exp_aliapi_cred_token" value="<?=htmlspecialcharsbx($this->arParams['TOKEN']);?>" />
<input type="button" data-role="acrit_exp_aliapi_cred_check" value="<?=static::getMessage('TOKEN_CHECK');?>"
	style="height:25px;">
<p id="acrit_core_aliexpress_cred_check_msg"></p>
