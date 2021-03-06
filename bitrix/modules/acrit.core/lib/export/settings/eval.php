<?
/**
 * Class for settings of fields and values
 */

namespace Acrit\Core\Export\Settings;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Log;

Loc::loadMessages(__FILE__);

class SettingsEval extends SettingsBase {
	
	public static function getCode(){
		return 'EVAL';
	}
	
	public static function getName(){
		return static::getMessage('NAME');
	}
	
	public static function getHint(){
		return static::getMessage('DESC');
	}
	
	public static function getSort(){
		return 1500;
	}
	
	public static function getGroup(){
		return array(
			'CODE' => 'ADDITIONAL',
		);
	}
	
	public static function isForFields(){
		return true;
	}
	
	public static function isForValues(){
		return true;
	}
	
	public static function isShown($obField, $arParams){
		if(end(\Acrit\Core\Export\Exporter::getInstance($obField->getModuleId())->getExportModules(true)) == $obField->getModuleId()){
			return true;
		}
		return false;
	}
	
	public static function showSettings($strFieldCode, $obField, $arParams){
		?>
		<input type="checkbox" name="<?=static::getCode();?>" value="Y" id="<?=static::getInputID();?>"
			<?if($arParams[static::getCode()]=='Y'):?> checked="checked"<?endif?> />
		<?=Helper::ShowHint(static::getHint());?>
		<span id="<?=static::getInputID();?>_span" style="margin-top:4px;">
			<?
			// Support for old case
			if(is_numeric($arParams[static::getCode()])){
				$arParams[static::getCode().'_value'] = $arParams[static::getCode()];
				$arParams[static::getCode().'_dots'] = 'Y';
			}
			?>
			<style>
			#<?=static::getInputID();?>_textarea {
				font:normal 12px/15px "Courier New", "Courier", monospace;
				max-height:1000px;
				min-height:100px;
				outline:0;
				resize:vertical;
				width:100%;
				-webkit-box-sizing:border-box;
				-moz-box-sizing:border-box;
				box-sizing:border-box;
			}
			</style>
			<textarea id="<?=static::getInputID();?>_textarea" name="<?=static::getCode();?>_php"><?
				print $arParams[static::getCode().'_php'];
			?></textarea>
		</span>
		<script>
		$('#<?=static::getInputID();?>').bind('change', function(){
			var subSettings = $('#<?=static::getInputID();?>_span');
			if($(this).is(':checked')){
				subSettings.css('display', 'block');
			}
			else{
				subSettings.css('display', 'none');
			}
		}).trigger('change');
		</script>
		<?
	}
	
	protected static function getPhp($strValue){
		$strResult = '
ob_start();
#VALUE#;
return ob_get_clean();
		';
		$strResult = trim(str_replace('#VALUE#', $strValue, $strResult));
		return $strResult;
	}
	
	public static function process(&$mValue, $arParams, $obField=null){
		if(end(\Acrit\Core\Export\Exporter::getInstance($obField->getModuleId())->getExportModules(true)) == $obField->getModuleId()){
			if($arParams[static::getCode()] == 'Y') {
				static::processMultipleValue($mValue, $arParams, $obField, function(&$strValue, $arParams, $obField){
					$strPhp = $arParams[static::getCode().'_php'];
					if(Helper::strlen($strPhp)){
						$strFieldCode = $obField->getCode();
						$strModuleId = $obField->getModuleId();
						$intProfileId = $obField->getProfileID();
						$intIBlockId = $obField->getIBlockID();
						$intElementId = \Acrit\Core\Export\Exporter::getInstance($strModuleId)->getElementId();
						try{
							$strValue = eval(static::getPhp($strPhp));
						}
						catch(\Error $obError){
							$strLogMessage = static::getMessage('EVAL_ERROR_LOG', array(
								'#MESSAGE#' => $obError->getMessage(),
								'#FIELD#' => (is_object($obField) ? $obField->getCode() : (Helper::strlen($arParams['field_code']) ? $arParams['field_code'] : '?')),
								'#PHP#' => $strPhp,
							));
							Log::getInstance($obField->getModuleId())->add($strLogMessage, 
								(is_object($obField) ? $obField->getProfileID() : false), false);
							$strValue = '';
						}
						catch(\Exception $obError){
							$strLogMessage = static::getMessage('EVAL_ERROR_LOG', array(
								'#MESSAGE#' => $obError->getMessage(),
								'#FIELD#' => (is_object($obField) ? $obField->getCode() : (Helper::strlen($arParams['field_code']) ? $arParams['field_code'] : '?')),
								'#PHP#' => $strPhp,
							));
							Log::getInstance($obField->getModuleId())->add($strLogMessage, 
								(is_object($obField) ? $obField->getProfileID() : false), false);
							$strValue = '';
						}
					}
				});
			}
		}
	}
	
}
