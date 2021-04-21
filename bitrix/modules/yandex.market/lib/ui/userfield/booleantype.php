<?php

namespace Yandex\Market\Ui\UserField;

class BooleanType extends \CUserTypeBoolean
{
	const VALUE_Y = '1';
	const VALUE_N = '0';

	public function GetEditFormHTML($arUserField, $arHtmlControl)
	{
		$value = (string)$arHtmlControl['VALUE'] !== ''
			? (int)$arHtmlControl['VALUE']
			: (int)$arUserField['SETTINGS']['DEFAULT_VALUE'];
		$isChecked = ($value > 0);

		return
			'<input type="hidden" value="0" name="'.$arHtmlControl["NAME"].'">'
			. '<input class="adm-designed-checkbox" type="checkbox" value="1" name="'.$arHtmlControl["NAME"].'"'.($isChecked? ' checked': '').' id="'.$arHtmlControl["NAME"].'"'.($arUserField["EDIT_IN_LIST"]!=="Y"? ' disabled="disabled"': '').'>'
			. '<label class="adm-designed-checkbox-label" for="'.$arHtmlControl["NAME"].'"></label>';
	}
}