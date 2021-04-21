<?
namespace Aspro\Next\Property;

use Bitrix\Main\Localization\Loc,
	Bitrix\Main\Loader;

Loc::loadMessages(__FILE__);

class Service{
	static function OnIBlockPropertyBuildList(){
		return array(
			'PROPERTY_TYPE' => 'S',
			'USER_TYPE' => 'SAsproService',
			'DESCRIPTION' => Loc::getMessage('SERVICE_PROP_TITLE'),
			'GetPropertyFieldHtml' => array(__CLASS__, 'GetPropertyFieldHtml'),
			'GetPropertyFieldHtmlMulty' => array(__CLASS__, 'GetPropertyFieldHtml'),
			'GetAdminListViewHTML' => array(__CLASS__, 'GetAdminListViewHTML'),
			'GetSettingsHTML' => array(__CLASS__, 'GetSettingsHTML'),
			'PrepareSettings' => array(__CLASS__, 'PrepareSettings'),
		);
	}

	static function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName){
        return str_replace(' ', '&nbsp;', htmlspecialcharsex($value['VALUE']));
	}

	static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName){
		static $initialized;

		$bEditProperty = $strHTMLControlName['MODE'] === 'EDIT_FORM';
		$bDetailPage = $strHTMLControlName['MODE'] === 'FORM_FILL';

		$bReadOnly = $arProperty['USER_TYPE_SETTINGS']['IS_READONLY'] === 'Y';
		$bHidden = $arProperty['USER_TYPE_SETTINGS']['IS_HIDDEN'] === 'Y';
		$note = strlen($arProperty['USER_TYPE_SETTINGS']['NOTE']) ? $arProperty['USER_TYPE_SETTINGS']['NOTE'] : Loc::getMessage('SERVICE_PROP_NOTE_VALUE_DEFAULT');
		$arValues = array();

		if($bEditProperty){
			$arValues[] = array(
				'VALUE' => $value['VALUE'],
				'VALUE_NAME' => $strHTMLControlName['VALUE'],
			);
		}
		else{
			if($arProperty['MULTIPLE'] === 'N'){
				$arValues[] = array(
					'VALUE' => $value ? $value['VALUE'] : $arProperty['DEFAULT_VALUE'],
					'VALUE_NAME' => ($name = $strHTMLControlName['VALUE']),
					'DESCRIPTION' => $value ? $value['DESCRIPTION'] : '',
					'DESCRIPTION_NAME' => str_replace('VALUE', 'DESCRIPTION', $name),
				);
			}
			else{
				if($value){
					foreach($value as $k => $val){
						$arValues[] = array(
							'VALUE' => $val['VALUE'],
							'VALUE_NAME' => ($name = $strHTMLControlName['VALUE'].'['.$k.'][VALUE]'),
							'DESCRIPTION' => $val['DESCRIPTION'],
							'DESCRIPTION_NAME' => str_replace('VALUE', 'DESCRIPTION', $name),
						);
					}
				}

				for($i = 0; $i < $arProperty['MULTIPLE_CNT']; ++$i){
					$arValues['[n'.$i.']'] = array(
						'VALUE' => (!$i && !$value ? $arProperty['DEFAULT_VALUE'] : ''),
						'VALUE_NAME' => ($name = $strHTMLControlName['VALUE'].'[n'.$i.'][VALUE]'),
						'DESCRIPTION' => '',
						'DESCRIPTION_NAME' => str_replace('VALUE', 'DESCRIPTION', $name),
					);
				}
			}
		}

		ob_start();
		?>
		<?if($bDetailPage):?>
			<?if(!isset($initialized)):?>
				<?$initialized = true;?>
				<?self::addCss($arProperty);?>
				<?self::addJs($arProperty);?>
			<?endif;?>
			<div class="aspro_property_service">
				<div class="adm-warning-block-red">
					<div class="adm-warning-icon"></div><div class="aspro_property_service_note"><?=$note?></div>
					<?if($bHidden):?>
						<a href="" onlick=""><?=Loc::getMessage('SERVICE_PROP_SHOW')?></a>
						<div class="aspro_property_service--hidden">
					<?endif;?>
				<?foreach($arValues as $k => $val):?>
					<div class="aspro_property_service_item">
						<?$name = $val['VALUE_NAME'];?>
						<input type="text" id="<?=$name?>" name="<?=$name?>" value="<?=htmlspecialcharsbx($val['VALUE'])?>" data-bx-property-id="<?=$arProperty['CODE']?>" data-bx-comp-prop="true" <?=($bReadOnly ? 'readonly' : '')?> size="<?=$arProperty['COL_COUNT']?>" />
						<?if($arProperty['WITH_DESCRIPTION'] === 'Y'):?>
							<?$name = $val['DESCRIPTION_NAME'];?>
							&nbsp;&nbsp;<label for="<?=$strHTMLControlName['DESCRIPTION']?>"><?=Loc::getMessage('SERVICE_PROP_DESCRIPTION')?></label>&nbsp;<input type="text" id="<?=$name?>" name="<?=$name?>" value="<?=htmlspecialcharsbx($val['DESCRIPTION'])?>" <?=($bReadOnly ? 'readonly' : '')?> size="30" />
						<?endif;?>
						<br />
					</div>
				<?endforeach;?>
					<?if($bHidden):?>
						</div>
					<?endif;?>
				</div>
			</div>
		<?elseif($bEditProperty):?>
			<?foreach($arValues as $k => $val):?>
				<?$name = $val['VALUE_NAME'];?>
				<input type="text" id="<?=$name?>" name="<?=$name?>" value="<?=htmlspecialcharsbx($val['VALUE'])?>" data-bx-property-id="<?=$arProperty['CODE']?>" data-bx-comp-prop="true" size="<?=$arProperty['COL_COUNT']?>" />
			<?endforeach;?>
		<?else:?>
			<?foreach($arValues as $k => $val):?>
				<?$name = $val['VALUE_NAME'];?>
				<input type="text" id="<?=$name?>" name="<?=$name?>" value="<?=$val['VALUE']?>" data-bx-property-id="<?=$arProperty['CODE']?>" data-bx-comp-prop="true" <?=($bReadOnly ? 'readonly' : '')?> size="<?=$arProperty['COL_COUNT']?>" />
				<?if($arProperty['WITH_DESCRIPTION'] === 'Y'):?>
					<?$name = $val['DESCRIPTION_NAME'];?>
					&nbsp;&nbsp;<label for="<?=$strHTMLControlName['DESCRIPTION']?>"><?=Loc::getMessage('SERVICE_PROP_DESCRIPTION')?></label>&nbsp;<input type="text" id="<?=$name?>" name="<?=$name?>" value="<?=htmlspecialcharsbx($val['DESCRIPTION'])?>" <?=($bReadOnly ? 'readonly' : '')?> size="30" />
				<?endif;?>
				<br />
			<?endforeach;?>
		<?endif;?>
		<?
		return ob_get_clean();
	}

	static function PrepareSettings($arFields){
		$arFields['USER_TYPE_SETTINGS']['IS_READONLY'] = (isset($arFields['USER_TYPE_SETTINGS']) && isset($arFields['USER_TYPE_SETTINGS']['IS_READONLY']) && $arFields['USER_TYPE_SETTINGS']['IS_READONLY'] === 'Y') ? 'Y' : 'N';

		$arFields['USER_TYPE_SETTINGS']['IS_HIDDEN'] = (isset($arFields['USER_TYPE_SETTINGS']) && isset($arFields['USER_TYPE_SETTINGS']['IS_HIDDEN']) && $arFields['USER_TYPE_SETTINGS']['IS_HIDDEN'] === 'Y') ? 'Y' : 'N';

		$arFields['USER_TYPE_SETTINGS']['NOTE'] = (isset($arFields['USER_TYPE_SETTINGS']) && isset($arFields['USER_TYPE_SETTINGS']['NOTE']) && strlen($arFields['USER_TYPE_SETTINGS']['NOTE'])) ? $arFields['USER_TYPE_SETTINGS']['NOTE'] : Loc::getMessage('SERVICE_PROP_NOTE_VALUE_DEFAULT');

        return $arFields;
	}

	static function GetSettingsHTML($arProperty, $strHTMLControlName, &$arPropertyFields){
		$arPropertyFields = array(
            'HIDE' => array(
            	'SMART_FILTER',
            	'SEARCHABLE',
            	'COL_COUNT',
            	'ROW_COUNT',
            	'FILTER_HINT',
            ),
            'SET' => array(
            	'SMART_FILTER' => 'N',
            	'SEARCHABLE' => 'N',
            	'ROW_COUNT' => '10',
            ),
        );

		$bReadOnly = $arProperty['USER_TYPE_SETTINGS']['IS_READONLY'] === 'Y';
		$bHidden = $arProperty['USER_TYPE_SETTINGS']['IS_HIDDEN'] === 'Y';
		$note = strlen($arProperty['USER_TYPE_SETTINGS']['NOTE']) ? $arProperty['USER_TYPE_SETTINGS']['NOTE'] : Loc::getMessage('SERVICE_PROP_NOTE_VALUE_DEFAULT');

		$nameReadonly = $strHTMLControlName['NAME'].'[IS_READONLY]';
		$html .= '<tr><td width="40%"><label for="'.$nameReadonly.'">'.Loc::getMessage('SERVICE_PROP_READONLY').'</label></td><td><input type="checkbox" id="'.$nameReadonly.'" name="'.$nameReadonly.'" value="Y" '.($bReadOnly ? 'checked' : '' ).' /></td></tr>';

		$nameHidden = $strHTMLControlName['NAME'].'[IS_HIDDEN]';
		$html .= '<tr><td width="40%"><label for="'.$nameHidden.'">'.Loc::getMessage('SERVICE_PROP_HIDDEN').'</label></td><td><input type="checkbox" id="'.$nameHidden.'" name="'.$nameHidden.'" value="Y" '.($bHidden ? 'checked' : '' ).' /></td></tr>';

		$nameNote = $strHTMLControlName['NAME'].'[NOTE]';
		$html .= '<tr><td width="40%"><label for="'.$nameNote.'">'.Loc::getMessage('SERVICE_PROP_NOTE').'</label></td><td><input type="text" id="'.$nameNote.'" name="'.$nameNote.'" value="'.$note.'" size="50" /></td></tr>';

		return $html;
	}

	private static function addCss($arProperty){
		$GLOBALS['APPLICATION']->SetAdditionalCss('/bitrix/css/aspro.next/style.css');
	}

	private static function addJs($arProperty){
		$GLOBALS['APPLICATION']->AddHeadScript('/bitrix/js/aspro.next/script.js');
	}
}
