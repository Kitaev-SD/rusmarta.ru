<?
namespace Aspro\Next\Property;

use Bitrix\Main\Localization\Loc,
	Bitrix\Main\Loader,
	Aspro\Next\SearchQuery;

Loc::loadMessages(__FILE__);

class YaDirectQuery{
	static function OnIBlockPropertyBuildList(){
		self::ajaxAction();

		return array(
			'PROPERTY_TYPE' => 'S',
			'USER_TYPE' => 'SAsproYaDirectQuery',
			'DESCRIPTION' => Loc::getMessage('YADIRECTQUERY_PROP_TITLE'),
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
			<div class="aspro_property_yadirectquery">
				<?if(!isset($initialized)):?>
					<?$initialized = true;?>
					<?self::addCss($arProperty);?>
					<?self::addJs($arProperty);?>
				<?endif;?>
				<?=BeginNote('style="width:334px;"').Loc::getMessage('YADIRECTQUERY_PROP_NOTE').EndNote();?>
				<?foreach($arValues as $k => $val):?>
					<div class="aspro_property_yadirectquery_item query_explanation">
						<?$name = $val['VALUE_NAME'];?>
						<input type="text" id="<?=$name?>" name="<?=$name?>" value="<?=htmlspecialcharsbx($val['VALUE'])?>" data-bx-property-id="<?=$arProperty['CODE']?>" data-bx-comp-prop="true" size="51" /><br />
						<a class="query_explanation_btn" title="<?=Loc::getMessage('YADIRECTQUERY_EXPLANATION_BTN_TITLE')?>"><i></i><?=Loc::getMessage('YADIRECTQUERY_EXPLANATION_BTN_NAME')?></a>
						<div class="query_explanation_inner"><div class="query_explanation_content hidden"></div></div><br />
					</div>
				<?endforeach;?>
			</div>
		<?elseif($bEditProperty):?>
			<?foreach($arValues as $k => $val):?>
				<?$name = $val['VALUE_NAME'];?>
				<input type="text" id="<?=$name?>" name="<?=$name?>" value="<?=htmlspecialcharsbx($val['VALUE'])?>" data-bx-property-id="<?=$arProperty['CODE']?>" data-bx-comp-prop="true" size="51" /><br />
			<?endforeach;?>
		<?else:?>
			<?foreach($arValues as $k => $val):?>
				<?$name = $val['VALUE_NAME'];?>
				<input type="text" id="<?=$name?>" name="<?=$name?>" value="<?=$val['VALUE']?>" data-bx-property-id="<?=$arProperty['CODE']?>" data-bx-comp-prop="true" size="51" /><br />
			<?endforeach;?>
		<?endif;?>
		<?
		return ob_get_clean();
	}

	static function PrepareSettings($arFields){
		$arFields['SMART_FILTER'] = $arFields['WITH_DESCRIPTION'] = 'N';
		$arFields['MULTIPLE'] = 'Y';
		$arFields['MULTIPLE_CNT'] = 1;

        return $arFields;
	}

	static function GetSettingsHTML($arProperty, $strHTMLControlName, &$arPropertyFields){
		$arPropertyFields = array(
            'HIDE' => array(
            	'SMART_FILTER',
            	'MULTIPLE_CNT',
            	'COL_COUNT',
            	'MULTIPLE',
            	'WITH_DESCRIPTION',
            	'FILTER_HINT',
            ),
            'SET' => array(
            	'SMART_FILTER' => 'N',
            	'MULTIPLE_CNT' => '1',
            	'MULTIPLE' => 'Y',
            	'WITH_DESCRIPTION' => 'N',
            ),
        );

		return $html;
	}

	private static function addCss($arProperty){
		$GLOBALS['APPLICATION']->SetAdditionalCss('/bitrix/css/aspro.next/style.css');
		$GLOBALS['APPLICATION']->SetAdditionalCss('/bitrix/panel/catalog/catalog_cond.css');
	}

	private static function addJs($arProperty){
		$GLOBALS['APPLICATION']->AddHeadScript('/bitrix/js/aspro.next/script.js', true);
	}

	private static function ajaxAction(){
		if(isset($_POST['action']) && $_POST['action'] === 'getQueryExplanation'){
			$GLOBALS['APPLICATION']->RestartBuffer();
			if(isset($_POST['query']) && strlen($_POST['query'])){
				$query = iconv('UTF-8', SITE_CHARSET, $_POST['query']);
				list($query, $hash, $arData) = SearchQuery::getSentenceMeta($query);
				if($hash !== SearchQuery::META_HASH_NOT_VALID){
					$example = SearchQuery::getSentenceExampleQuery($query);

					$minusWords = $stopWords = $fixedForms = $fixedOrder = $other = false;
					$arExplanations = $arExplanationsFixedOrder = $arNeedWords = $arComplex = $arMinusWords = $arMinusStems = $arFixedForms = $arOther = $arStopWords = array();
					if($arData){
						$minusWords = $arData['MINUS'];
						$stopWords = $arData['STOP'];
						$arComplex = $arData['COMPLEX'];
						$fixedForms = $arData['FORMS'];
						$fixedOrder = $arData['ORDER'];
						$other = $arData['OTHER'];
					}

					if($hash & SearchQuery::META_HASH_HAS_FIXED_COUNT){
						$cntFixedCount = $hash >> 16;
						$arExplanations[] = array(
							'LOGIC' => '&&',
							'TEXT' => Loc::getMessage('YADIRECTQUERY_EXPLANATION_CONTAINS').' <a title="'.Loc::getMessage('YADIRECTQUERY_EXPLANATION_CONTAINS_EQUAL').'">'.Loc::getMessage('YADIRECTQUERY_EXPLANATION_EQUAL').' '.SearchQuery::vail($cntFixedCount, array(Loc::getMessage('YADIRECTQUERY_EXPLANATION_WORD1'), Loc::getMessage('YADIRECTQUERY_EXPLANATION_WORD2'), Loc::getMessage('YADIRECTQUERY_EXPLANATION_WORD0'))).'</a>',
						);
					}
					else{
						$cntAll = ($hash & (255 << 8)) >> 8;
						if($cntAll > 0){
							$arExplanations[] = array(
								'LOGIC' => '&&',
								'TEXT' => Loc::getMessage('YADIRECTQUERY_EXPLANATION_CONTAINS').' <a title="'.Loc::getMessage('YADIRECTQUERY_EXPLANATION_CONTAINS_MINIMAL').'">'.Loc::getMessage('YADIRECTQUERY_EXPLANATION_MINIMAL').' '.SearchQuery::vail($cntAll, array(Loc::getMessage('YADIRECTQUERY_EXPLANATION_WORD1'), Loc::getMessage('YADIRECTQUERY_EXPLANATION_WORD2'), Loc::getMessage('YADIRECTQUERY_EXPLANATION_WORD0'))).'</a>',
							);
						}
					}

					if($hash & SearchQuery::META_HASH_HAS_FIXED_FORMS && strlen($fixedForms)){
						$arFixedForms = array_filter(explode(';', $fixedForms));
					}
					if(strlen($other)){
						$arOther = array_filter(explode(';', $other));
					}
					if($arFixedForms || $arOther){
						foreach($arFixedForms as $word){
							if(strlen($word)){
								$word = ToLower($word);
								if(!in_array($word, $arNeedWords)){
									$arNeedWords[] = $word;
									$arExplanations[] = array(
										'LOGIC' => '&&',
										'TEXT' => Loc::getMessage('YADIRECTQUERY_EXPLANATION_CONTAINS').' <a title=\''.Loc::getMessage('YADIRECTQUERY_EXPLANATION_FIX_WORD').' "'.$word.'"\'>'.$word.'</a>',
									);
								}
							}
						}
						foreach($arOther as $word){
							if(strlen($word)){
								$word = ToLower($word);
								if(!in_array($word, $arNeedWords)){
									$arNeedWords[] = $word;
									$arExplanations[] = array(
										'LOGIC' => '&&',
										'TEXT' => Loc::getMessage('YADIRECTQUERY_EXPLANATION_CONTAINS').' <a title=\''.Loc::getMessage('YADIRECTQUERY_EXPLANATION_ALL_WORD').' "'.$word.'"\'>'.$word.'*</a>',
									);
								}
							}
						}
					}

					if($hash & SearchQuery::META_HASH_HAS_STOP_WORDS && strlen($stopWords)){
						$arStopWords = array_filter(explode(';', $stopWords));
					}
					foreach($arStopWords as $word){
						if(strlen($word)){
							$word = ToLower($word);
							if(!in_array($word, $arNeedWords)){
								$arNeedWords[] = $word;
								$arExplanations[] = array(
									'LOGIC' => '&&',
									'TEXT' => Loc::getMessage('YADIRECTQUERY_EXPLANATION_CONTAINS').' <a title=\''.Loc::getMessage('YADIRECTQUERY_EXPLANATION_FIX_WORD').' "'.$word.'"\'>'.$word.'</a>',
								);
							}
						}
					}

					if($hash & SearchQuery::META_HASH_HAS_FIXED_ORDER && strlen($fixedOrder)){
						$arFixedOrder = array_filter(explode(';', $fixedOrder));
						$cntFixedOrder = count($arFixedOrder);
						$explanation = '';
						foreach($arFixedOrder as $i => $fixedOrder){
							$fixedOrder = str_replace('[\s]|$', '[\s|$]', $fixedOrder);
							if($fixedOrder = explode('[\s]', $fixedOrder)){
								$explanation .= '<div class="condition-wrapper">'.($i !=  ($cntFixedOrder - 1) ? '<div class="condition-logic condition-logic-and">'.Loc::getMessage('YADIRECTQUERY_EXPLANATION_LOGIC_AND').'</div>' : '').'<div class="condition-simple-control">';

								foreach($fixedOrder as $word){
									$word = str_replace('[\s|$]', '[\s]|$', $word);
									if(strpos($word, '(') !== false && strpos($word, ')') !== false){
										if(preg_match_all('/([a-zA-Z'.TREG_CYR.'0-9-]+)([\[][^\]]*[\]])/'.BX_UTF_PCRE_MODIFIER, $word, $arMatches)){
											$explanation .= '(';
												foreach($arMatches[1] as $j => $word){
													$bFixedForm = $arMatches[2][$j] !== '[a-zA-Z'.TREG_CYR.'0-9-]*';
													$word = ToLower(str_replace(array('[\s]', '[a-zA-Z'.TREG_CYR.'0-9-]*', '(', ')'), '', $word));
													$explanation .= ($j ? ' '.Loc::getMessage('YADIRECTQUERY_EXPLANATION_LOGIC_OR').' ' : '').'<a title=\''.($bFixedForm ? Loc::getMessage('YADIRECTQUERY_EXPLANATION_FIX_WORD') : Loc::getMessage('YADIRECTQUERY_EXPLANATION_ALL_WORD')).' "'.$word.'"\'>'.$word.($bFixedForm ? '' : '*').'</a>';
												}
											$explanation .= ') ';
										}
									}
									else{
										$bFixedForm = strpos($word, '[a-zA-Z'.TREG_CYR.'0-9-]*') === false;
										$word = ToLower(str_replace(array('[\s]', '[a-zA-Z'.TREG_CYR.'0-9-]*', '(', ')'), '', $word));
										$explanation .= '<a title=\''.($bFixedForm ? Loc::getMessage('YADIRECTQUERY_EXPLANATION_FIX_WORD') : Loc::getMessage('YADIRECTQUERY_EXPLANATION_ALL_WORD')).' "'.$word.'"\'>'.$word.($bFixedForm ? '' : '*').'</a> ';
										if(!in_array($word, $arNeedWords)){
											$arNeedWords[] = $word;
											$arExplanations[] = array(
												'LOGIC' => '&&',
												'TEXT' => Loc::getMessage('YADIRECTQUERY_EXPLANATION_CONTAINS').' <a title=\''.($bFixedForm ? Loc::getMessage('YADIRECTQUERY_EXPLANATION_FIX_WORD') : Loc::getMessage('YADIRECTQUERY_EXPLANATION_ALL_WORD')).' "'.$word.'"\'>'.$word.($bFixedForm ? '' : '*').'</a>',
											);
										}
									}
								}

								$explanation .= '</div></div>';
							}
						}

						if($explanation){
							$arExplanationsFixedOrder = array(
								'LOGIC' => '&&',
								'TEXT' => Loc::getMessage('YADIRECTQUERY_EXPLANATION_CONTAINS_ORDER').' <a title="'.Loc::getMessage('YADIRECTQUERY_EXPLANATION_NO_MOVE').'">'.Loc::getMessage('YADIRECTQUERY_EXPLANATION_FIX_ORDER').'</a> '.Loc::getMessage('YADIRECTQUERY_EXPLANATION_WORD0').' <div class="condition-container">'.$explanation.'</div>',
							);
						}
					}

					if($hash & SearchQuery::META_HASH_HAS_COMPLEX && $arComplex){
						foreach($arComplex as $complex){
							if($complex = explode('|', str_replace('|$', '', $complex))){
								$explanation = '';
								$cntComplex = count($complex);
								foreach($complex as $i => $word){
									$bFixedForm = preg_match('/[a-zA-Z'.TREG_CYR.'0-9-]+[\[][\\\]s/'.BX_UTF_PCRE_MODIFIER, $word);
									$word = ToLower(str_replace(array('[\s]', '[a-zA-Z'.TREG_CYR.'0-9-]*', '(', ')'), '', $word));
									$explanation .= '<div class="condition-wrapper">'.($i !=  ($cntComplex - 1) ? '<div class="condition-logic condition-logic-or">'.Loc::getMessage('YADIRECTQUERY_EXPLANATION_LOGIC_OR').'</div>' : '').'<div class="condition-simple-control"><a title=\''.($bFixedForm ? Loc::getMessage('YADIRECTQUERY_EXPLANATION_FIX_WORD') : Loc::getMessage('YADIRECTQUERY_EXPLANATION_ALL_WORD')).' "'.$word.'"\'>'.$word.($bFixedForm ? '' : '*').'</a></div></div>';
								}
								$arExplanations[] = array(
									'LOGIC' => '&&',
									'TEXT' => Loc::getMessage('YADIRECTQUERY_EXPLANATION_CONTAINS').' <div class="condition-container">'.$explanation.'</div>',
								);
							}
						}
					}

					if($arExplanationsFixedOrder){
						$arExplanations[] = $arExplanationsFixedOrder;
					}

					if($hash & SearchQuery::META_HASH_HAS_MINUS_WORDS && ($minusWords['WORDS'] || $minusWords['STEM'])){
						$arMinusWords = array_filter(explode(';', $minusWords['WORDS']));
						$arMinusStems = array_filter(explode(';', $minusWords['STEM']));
					}
					if($arMinusWords || $arMinusStems){
						foreach($arMinusWords as $word){
							if(strlen($word)){
								$arExplanations[] = array(
									'LOGIC' => '&&!',
									'TEXT' => Loc::getMessage('YADIRECTQUERY_EXPLANATION_CONTAINS').' <a title=\''.Loc::getMessage('YADIRECTQUERY_EXPLANATION_FIX_WORD').' "'.$word.'"\'>'.$word.'</a>',
								);
							}
						}
						foreach($arMinusStems as $word){
							if(strlen($word)){
								$arExplanations[] = array(
									'LOGIC' => '&&!',
									'TEXT' => Loc::getMessage('YADIRECTQUERY_EXPLANATION_CONTAINS').' <a title=\''.Loc::getMessage('YADIRECTQUERY_EXPLANATION_ALL_WORD').' "'.$word.'"\'>'.$word.'*</a>',
								);
							}
						}
					}
					?>
					<div class="condition-wrapper">
						<div class="condition-border">
							<?if($arExplanations):?>
								<?$cntExplanations = count($arExplanations);?>
								<span class="control-string"><?=Loc::getMessage('YADIRECTQUERY_EXPLANATION_SHOW')?>,<br /> <?=Loc::getMessage('YADIRECTQUERY_EXPLANATION_FOR')?> <span class="condition-simple"><?=($cntExplanations > 1 ? Loc::getMessage('YADIRECTQUERY_EXPLANATION_ALL_COND') : Loc::getMessage('YADIRECTQUERY_EXPLANATION_ONE_COND'))?>:</span></span>
								<div class="condition-wrapper">
									<div class="condition-container">
										<?foreach($arExplanations as $i => $arExplanation):?>
											<div class="condition-wrapper">
												<?if(isset($arExplanations[$i + 1])):?>
													<div class="condition-logic condition-logic-<?=($arExplanations[$i + 1]['LOGIC'] === '||' ? 'or' : 'and')?>"><?=($arExplanations[$i + 1]['LOGIC'] === '||' ? Loc::getMessage('YADIRECTQUERY_EXPLANATION_LOGIC_OR') : ($arExplanations[$i + 1]['LOGIC'] === '&&!' ? Loc::getMessage('YADIRECTQUERY_EXPLANATION_LOGIC_AND').' <span class="control-string-no">'.Loc::getMessage('YADIRECTQUERY_EXPLANATION_LOGIC_NOT').'</span>' : Loc::getMessage('YADIRECTQUERY_EXPLANATION_LOGIC_AND')))?></div>
												<?endif;?>
												<div class="condition-simple-control"><?=$arExplanation['TEXT']?></div>
											</div>
										<?endforeach;?>
									</div>
								</div>
								<br />
								<?=Loc::getMessage('YADIRECTQUERY_EXPLANATION_EXAMPLE_NOTE')?><br />
								<span class="condition-simple"><?=$example?></span>
							<?else:?>
								<span class="control-string"><?=Loc::getMessage('YADIRECTQUERY_EXPLANATION_SHOW_')?> <span class="condition-simple"><?=Loc::getMessage('QUERY_EXPLANATION_ANY')?></span> <?=Loc::getMessage('YADIRECTQUERY_EXPLANATION_QUERY')?></span>
							<?endif;?>
							<br />
							<br />
						</div>
					</div>
					<pre style="display:none"><?=$hash.' <br />';print_r($arData);?></pre>
					<?
				}
				else{
					echo '<div class="errornote">'.Loc::getMessage('YADIRECTQUERY_EXPLANATION_EXAMPLE_ERROR_NOTE').'</div>';
				}
			}
			else{
				echo '<div class="tmpnote">'.Loc::getMessage('YADIRECTQUERY_EXPLANATION_EXAMPLE_TMP_NOTE').'</div>';
			}
			die();
		}
	}
}
