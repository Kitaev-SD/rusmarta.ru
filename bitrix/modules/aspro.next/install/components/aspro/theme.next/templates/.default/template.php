<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(false);?>
<?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/jquery.mCustomScrollbar.min.css');?>
<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.mCustomScrollbar.min.js');?>
<?$customColorExist = isset($arResult['BASE_COLOR']['LIST']['CUSTOM']) && isset($arResult['BASE_COLOR_CUSTOM']);?>
<?if($_COOKIE['styleSwitcher'] === 'open'):?>
	<div class="jqmOverlay waiting"></div>
<?endif;?>
<div class="style-switcher new1<?=($_COOKIE['styleSwitcher'] == 'open' ? ' active' : '')?>">
	<div class="switch_presets animation-bg<?=($_COOKIE['styleSwitcherType'] === 'presets' ? ' active' : '')?>">
		<svg class="presets_svg" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 22 22">
			<defs>
				<style>
				.presets_svg .cls-10 {
				fill: #333;
				fill-rule: evenodd;
				}
				</style>
			</defs>
			<path id="Rounded_Rectangle_390_copy_2" data-name="Rounded Rectangle 390 copy 2" class="cls-10" d="M740,112a3.991,3.991,0,0,1-3.859-3H733a1,1,0,0,1,0-2h3.142A3.993,3.993,0,1,1,740,112Zm0-6a2,2,0,1,0,2,2A2,2,0,0,0,740,106Zm-13,5.859V115a1,1,0,0,1-2,0v-3.141A4,4,0,1,1,727,111.859ZM726,106a2,2,0,0,0,0,4h0A2,2,0,0,0,726,106Zm0,12a3.99,3.99,0,0,1,3.858,3H733a1,1,0,0,1,0,2h-3.141A3.994,3.994,0,1,1,726,118Zm0,6a2,2,0,0,0,2-2h0A2,2,0,1,0,726,124Zm13-5.859V115a1,1,0,0,1,1-1h0a1,1,0,0,1,1,1v3.141A4,4,0,1,1,739,118.141ZM740,124a2,2,0,1,0-2-2A2,2,0,0,0,740,124Z" transform="translate(-722 -104)"></path>
		</svg>
		<div class="tooltip">
			<div class="wrap">
				<div class="title"><?=GetMessage('SWITCH_PRESETS_TOOLTIP_TITLE')?></div>
				<div class="text"><?=GetMessage('SWITCH_PRESETS_TOOLTIP_DESCRIPTION')?></div>
			</div>
		</div>
	</div>
	<div class="switch animation-bg<?=($_COOKIE['styleSwitcherType'] === 'parametrs' ? ' active' : '')?>">
		<svg id="Options.svg" xmlns="http://www.w3.org/2000/svg" width="22" height="24" viewBox="0 0 22 24">
		  <defs>
		    <style>
		      .cls-2 {
		        fill: #333;
		        fill-rule: evenodd;
		      }
		    </style>
		  </defs>
		  <path id="Ellipse_12_copy_2" data-name="Ellipse 12 copy 2" class="cls-2" d="M743,214H732.858a3.981,3.981,0,0,1-7.717,0H723a1,1,0,1,1,0-2h2.141a3.981,3.981,0,0,1,7.717,0H743A1,1,0,1,1,743,214Zm-14-3a2,2,0,1,0,2,2A2,2,0,0,0,729,211Zm14-5h-2.142a3.981,3.981,0,0,1-7.717,0H723a1,1,0,0,1,0-2h10.141a3.981,3.981,0,0,1,7.717,0H743A1,1,0,0,1,743,206Zm-6-3a2,2,0,1,0,2,2A2,2,0,0,0,737,203Zm-14,17h10.141a3.982,3.982,0,0,1,7.717,0H743a1,1,0,0,1,0,2h-2.142a3.982,3.982,0,0,1-7.717,0H723A1,1,0,0,1,723,220Zm14,3a2,2,0,1,0-2-2A2,2,0,0,0,737,223Z" transform="translate(-722 -201)"/>
		</svg>
	</div>
	<div class="header <?=($arResult['CAN_SAVE'] ? 'can_save' : '')?>">
		<div class="title title-parametrs"<?=($_COOKIE['styleSwitcherType'] === 'presets' ? '' : ' style="display:block;"')?>>
			<svg class="parametrs_svg" xmlns="http://www.w3.org/2000/svg" width="22" height="24" viewBox="0 0 22 24">
			  <defs>
			    <style>
			      .parametrs_svg .cls-10 {
			        fill: #333;
			        fill-rule: evenodd;
			      }
			    </style>
			  </defs>
			  <path id="Ellipse_12_copy_2" data-name="Ellipse 12 copy 2" class="cls-10" d="M743,177H732.858a3.981,3.981,0,0,1-7.717,0H723a1,1,0,1,1,0-2h2.141a3.981,3.981,0,0,1,7.717,0H743A1,1,0,1,1,743,177Zm-14-3a2,2,0,1,0,2,2A2,2,0,0,0,729,174Zm14-5h-2.142a3.982,3.982,0,0,1-7.717,0H723a1,1,0,1,1,0-2h10.141a3.981,3.981,0,0,1,7.717,0H743A1,1,0,1,1,743,169Zm-6-3a2,2,0,1,0,2,2A2,2,0,0,0,737,166Zm-14,17h10.141a3.982,3.982,0,0,1,7.717,0H743a1,1,0,0,1,0,2h-2.142a3.981,3.981,0,0,1-7.717,0H723A1,1,0,0,1,723,183Zm14,3a2,2,0,1,0-2-2A2,2,0,0,0,737,186Z" transform="translate(-722 -164)"></path>
			</svg><?=GetMessage('SWITCH_PARAMETRS_HEADER_TITLE');?>
		</div>
		<div class="title title-presets"<?=($_COOKIE['styleSwitcherType'] === 'presets' ? ' style="display:block;"' : '')?>>
			<svg class="presets_svg" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 22 22">
			  <defs>
			    <style>
			      .presets_svg .cls-10 {
			        fill: #333;
			        fill-rule: evenodd;
			      }
			    </style>
			  </defs>
			  <path id="Rounded_Rectangle_390_copy_2" data-name="Rounded Rectangle 390 copy 2" class="cls-10" d="M740,112a3.991,3.991,0,0,1-3.859-3H733a1,1,0,0,1,0-2h3.142A3.993,3.993,0,1,1,740,112Zm0-6a2,2,0,1,0,2,2A2,2,0,0,0,740,106Zm-13,5.859V115a1,1,0,0,1-2,0v-3.141A4,4,0,1,1,727,111.859ZM726,106a2,2,0,0,0,0,4h0A2,2,0,0,0,726,106Zm0,12a3.99,3.99,0,0,1,3.858,3H733a1,1,0,0,1,0,2h-3.141A3.994,3.994,0,1,1,726,118Zm0,6a2,2,0,0,0,2-2h0A2,2,0,1,0,726,124Zm13-5.859V115a1,1,0,0,1,1-1h0a1,1,0,0,1,1,1v3.141A4,4,0,1,1,739,118.141ZM740,124a2,2,0,1,0-2-2A2,2,0,0,0,740,124Z" transform="translate(-722 -104)"></path>
			</svg><?=GetMessage('SWITCH_PRESETS_HEADER_TITLE');?>
		</div>
		<div class="buttons">
			<div class="header-inner animation-bg reset" title="<?=GetMessage('THEME_RESET_TITLE')?>">
				<svg class="reset_svg" xmlns="http://www.w3.org/2000/svg" width="14" height="12" viewBox="0 0 14 12">
				  <defs>
				    <style>
				      .reset_svg .cls-10 {
				        fill: #333;
				        fill-rule: evenodd;
				      }
				    </style>
				  </defs>
				  <path id="Rectangle_50_copy_6" data-name="Rectangle 50 copy 6" class="cls-10" d="M419,42h-3a1,1,0,0,1,0-2h0.431a3.95,3.95,0,0,0-6.367-.7l-2.132-.486A5.935,5.935,0,0,1,418,38.766V38a1,1,0,1,1,2,0v3A1,1,0,0,1,419,42Zm-8,1a1,1,0,0,1-1,1h-0.447a3.971,3.971,0,0,0,6.207.885l2.19,0.5a5.953,5.953,0,0,1-9.95-.137V46a1,1,0,1,1-2,0V43a1,1,0,0,1,1-1h3A1,1,0,0,1,411,43Z" transform="translate(-406 -36)"></path>
				</svg><?=GetMessage('THEME_RESET')?>
			</div>
			<?if($arResult['CAN_SAVE']):?>
				<div class="save_btn animation-bg" title="<?=GetMessage("SAVE_CONFIG_TITLE")?>">
					<svg class="save_svg" xmlns="http://www.w3.org/2000/svg" width="16" height="14" viewBox="0 0 16 14">
					  <defs>
					    <style>
					      .save_svg .cls-10 {
					        fill: #333;
					        fill-rule: evenodd;
					      }
					    </style>
					  </defs>
					  <path id="Rounded_Rectangle_361_copy_3" data-name="Rounded Rectangle 361 copy 3" class="cls-10" d="M584,49H572a2,2,0,0,1-2-2V39a2,2,0,0,1,2-2h3v2h-3v8h12V39h-3V37h3a2,2,0,0,1,2,2v8A2,2,0,0,1,584,49Zm-5.214-4.391a0.991,0.991,0,0,1-1.5.094s-0.006,0-.008-0.005l-2.007-2a0.985,0.985,0,0,1,0-1.405,1.015,1.015,0,0,1,1.423,0l0.3,0.3V36a1,1,0,1,1,2,0v5.582l0.292-.294a1,1,0,0,1,1.406,0,0.984,0.984,0,0,1,0,1.4Z" transform="translate(-570 -35)"/>
					</svg><?=GetMessage("SAVE_CONFIG")?>
				</div>
			<?endif;?>
		</div>
		<div class="clearfix"></div>
	</div>
	<form method="POST" name="style-switcher">
		<div class="parametrs">
			<div class="left-block">
				<?$arParametrs = CNext::$arParametrsList;
				$i = 0;?>
				<?foreach($arParametrs as $blockCode => $arBlock)
				{
					if(isset($arBlock['THEME'] ) && $arBlock['THEME'] == 'Y'):?>
						<?
						$active = '';
						if($_COOKIE['styleSwitcherTabIndex'])
						{
							if($i == $_COOKIE['styleSwitcherTabIndex'])
								$active = 'active toggle_initied';
						}
						elseif(!$i)
							$active = 'active toggle_initied';?>
						<div class="section-block <?=$active;?>"><?=$arBlock['TITLE']?></div>
						<?$i++;?>
					<?else:?>
						<?unset($arParametrs[$blockCode]);?>
					<?endif;?>
				<?}?>
			</div>
			<div class="right-block">
				<div class="content-body">
					<?if($arParametrs)
					{
						$i = 0;
						foreach($arParametrs as $blockCode => $arBlock):?>
							<?
							$active = '';
							if($_COOKIE['styleSwitcherTabIndex'])
							{
								if($i == $_COOKIE['styleSwitcherTabIndex'])
									$active = 'active';
							}
							elseif(!$i)
								$active = 'active';?>
							<div class="block-item <?=$active;?>">
								<?foreach($arResult as $optionCode => $arOption)
								{
									if($arOption['TYPE_BLOCK'] == $blockCode && (isset($arOption['THEME']) && $arOption['THEME'] == 'Y') && $optionCode !== 'BASE_COLOR_CUSTOM' && $optionCode !== 'CUSTOM_BGCOLOR_THEME' && !isset($arOption['GROUPS_EXT'])):?>
										<?if($optionCode == 'BGCOLOR_THEME' && $arResult['SHOW_BG_BLOCK']['VALUE'] != 'Y')
										{
											continue;
										}?>
										<div class="item">
											<?if($arOption['TYPE'] == 'checkbox' && (isset($arOption['ONE_ROW']) && $arOption['ONE_ROW'] == 'Y')):?>
												<div class="options pull-left" data-code="<?=$optionCode?>">
													<?=ShowOptions($optionCode, $arOption);?>
												</div>
												<?=ShowOptionsTitle($optionCode, $arOption);?>
											<?else:?>
												<?=ShowOptionsTitle($optionCode, $arOption);?>
												<div class="options <?=((isset($arOption['REFRESH']) && $arOption['REFRESH'] == 'Y') ? 'refresh-block' : '');?>" data-code="<?=$optionCode?>">
													<?if(isset($arOption['TYPE_EXT']) && $arOption['TYPE_EXT'] == 'colorpicker'):?>
														<input type="hidden" id="<?=$optionCode?>" name="<?=$optionCode?>" value="<?=$arOption['VALUE']?>" />
														<?foreach($arOption['LIST'] as $colorCode => $arColor):?>
															<?if($colorCode !== 'CUSTOM'):?>
																<div class="base_color <?=($arColor['CURRENT'] == 'Y' ? 'current' : '')?>" data-value="<?=$colorCode?>" data-color="<?=$arColor['COLOR']?>">
																	<span class="animation-all click_block"  data-option-id="<?=$optionCode?>" data-option-value="<?=$colorCode?>" title="<?=$arColor['TITLE']?>"><span style="background-color: <?=$arColor['COLOR']?>;"></span></span>
																</div>
															<?endif;?>
														<?endforeach;?>
														<?if($customColorExist && (isset($arResult['BASE_COLOR_CUSTOM']['PARENT_PROP']) && $arResult['BASE_COLOR_CUSTOM']['PARENT_PROP'] == $optionCode)):?>
															<?$customColor = str_replace('#', '', (strlen($arResult['BASE_COLOR_CUSTOM']['VALUE']) ? $arResult['BASE_COLOR_CUSTOM']['VALUE'] : $arResult['BASE_COLOR']['LIST'][$arResult['BASE_COLOR']['DEFAULT']]['COLOR']));?>
															<?$arColor = $arOption['LIST']['CUSTOM'];?>
															<div class="base_color base_color_custom <?=($arColor['CURRENT'] == 'Y' ? 'current' : '')?>" data-name="BASE_COLOR_CUSTOM" data-value="CUSTOM" data-color="#<?=$customColor?>">
																<span class="animation-all click_block" data-option-id="<?=$optionCode?>" data-option-value="CUSTOM" title="<?=$arColor['TITLE']?>" ><span data-color="<?=$customColor?>" style="background-color: #<?=$customColor?>;"></span></span>
																<input type="hidden" id="custom_picker" name="BASE_COLOR_CUSTOM" value="<?=$customColor?>" />
															</div>
														<?endif;?>
														<?if($customColorExist && (isset($arResult['CUSTOM_BGCOLOR_THEME']['PARENT_PROP']) && $arResult['CUSTOM_BGCOLOR_THEME']['PARENT_PROP'] == $optionCode)):?>
															<?$customColor = str_replace('#', '', (strlen($arResult['CUSTOM_BGCOLOR_THEME']['VALUE']) ? $arResult['CUSTOM_BGCOLOR_THEME']['VALUE'] : $arResult['CUSTOM_BGCOLOR_THEME']['LIST'][$arResult['CUSTOM_BGCOLOR_THEME']['DEFAULT']]['COLOR']));?>
															<?$arColor = $arOption['LIST']['CUSTOM'];?>
															<div class="base_color base_color_custom <?=($arColor['CURRENT'] == 'Y' ? 'current' : '')?>" data-name="CUSTOM_BGCOLOR_THEME" data-value="CUSTOM" data-color="#<?=$customColor?>">
																<span class="animation-all click_block" data-option-id="<?=$optionCode?>" data-option-value="CUSTOM" title="<?=$arColor['TITLE']?>" ><span style="background-color: #<?=$customColor?>;"></span></span>
																<input type="hidden" id="custom_picker2" name="CUSTOM_BGCOLOR_THEME" value="<?=$customColor?>" />
															</div>
														<?endif;?>
													<?else:?>
														<?=ShowOptions($optionCode, $arOption);?>
													<?endif;?>
												</div>
												<?if(isset($arOption['SUB_PARAMS']) && $arOption['LIST'] && (isset($arOption['REFRESH']) && $arOption['REFRESH'] == 'Y')):?>
													<div>
														<?foreach($arOption['LIST'] as $key => $arListOption):?>
															<?if($arOption['SUB_PARAMS'][$key]):?>
																<?foreach($arOption['SUB_PARAMS'][$key] as $key2 => $arSubOptions)
																{
																	if($arSubOptions['THEME'] == 'N' || $arSubOptions['VISIBLE'] == 'N')
																		unset($arOption['SUB_PARAMS'][$key][$key2]);
																}?>
																<?if($arOption['SUB_PARAMS'][$key]):?>
																	<div class="sup-params options refresh-block s_<?=$key;?> <?=($key == $arOption['VALUE'] ? 'active' : '');?>">
																		<div class="block-title"><span class="dotted-block"><?=GetMessage('SUB_PARAMS')?></span></div>
																		<div class="values">
																			<?$j = 1;?>
																			<?foreach($arOption['SUB_PARAMS'][$key] as $key2 => $arSubOptions):?>
																				<?$isRow = (($arSubOptions['TYPE'] == 'checkbox' && (isset($arSubOptions['ONE_ROW']) && $arSubOptions['ONE_ROW'] == 'Y')) ? true : false);?>
																				<div class="option-wrapper <?=(($arSubOptions['VALUE'] == 'N' && $isRow) ? "disabled" : "");?>">
																					<?if($isRow):?>
																						<table class="">
																							<tr>
																								<td><div class="blocks"><?=$j++;?></div></td>
																								<td><div class="blocks block-title"><?=$arSubOptions['TITLE'];?></div></td>
																								<td>
																									<div class="blocks value">
																										<?=ShowOptions($key.'_'.$key2, $arSubOptions, $arOption);?>
																									</div>
																								</td>
																							</tr>
																						</table>
																					<?else:?>
																						<div class="block-title"><?=$arSubOptions['TITLE'];?></div>
																						<div class="value">
																							<?=ShowOptions($key.'_'.$key2, $arSubOptions);?>
																						</div>
																					<?endif;?>
																				</div>
																			<?endforeach;?>
																			<div class="apply-block"><button class="btn btn-default white apply"><?=GetMessage("APPLY");?></button></div>
																		</div>
																	</div>
																<?endif;?>
															<?endif;?>
														<?endforeach;?>
													</div>
												<?endif;?>
											<?endif;?>
											<?if(isset($arOption['DEPENDENT_PARAMS']) && $arOption['DEPENDENT_PARAMS']) // show dependent options
											{
												foreach($arOption['DEPENDENT_PARAMS'] as $key => $arSubOptions)
												{
													if((!isset($arSubOptions['CONDITIONAL_VALUE']) || ($arSubOptions['CONDITIONAL_VALUE'] && $arResult[$optionCode]['VALUE'] == $arSubOptions['CONDITIONAL_VALUE'])) && $arSubOptions['THEME'] == 'Y')
													{?>
														<?if($arSubOptions['TYPE'] == 'checkbox' && (isset($arSubOptions['ONE_ROW']) && $arSubOptions['ONE_ROW'] == 'Y')):?>
															<div class="borders item">
																<div class="options dependent pull-left" data-code="<?=$key?>">
																	<?=ShowOptions($key, $arSubOptions);?>
																</div>
																<?=ShowOptionsTitle($key, $arSubOptions);?>
															</div>
														<?else:?>
															<?=ShowOptionsTitle($key, $arSubOptions);?>
															<div class="options dependent" data-code="<?=$key;?>">
																<?echo ShowOptions($key, $arSubOptions);?>
															</div>
														<?endif;?>
													<?}
												}
											}?>
										</div>
									<?elseif((isset($arOption['OPTIONS']) && $arOption['OPTIONS']) && (isset($arOption['GROUPS_EXT']) && $arOption['GROUPS_EXT'] == 'Y') && $arOption['TYPE_BLOCK'] == $blockCode && (isset($arOption['THEME']) && $arOption['THEME'] == 'Y')): // show groups options?>
										<div class="item groups">
											<?=ShowOptionsTitle($blockCode, $arOption);?>
											<div class="rows options">
												<?foreach($arOption['OPTIONS'] as $key => $arValue):?>
													<?echo ShowOptions($key, $arValue);?>
												<?endforeach;?>
											</div>
										</div>
									<?endif;?>
								<?}?>
								<?$i++;?>
							</div>
						<?endforeach;?>
					<?}?>
				</div>
			</div>
		</div>
		<div class="presets<?=($_COOKIE['styleSwitcherType'] === 'presets' ? ' active' : '')?>">
			<div class="options">
				<div class="rows items">
					<?$arPresets = CNext::$arPresetsList;?>
					<?foreach($arPresets as $arPreset):?>
						<div class="item col-md-6 col-sm-6 col-xs-12">
							<?
							// define is it current preset?
							$bCurrent = true;
							if($arPreset['OPTIONS']){
								foreach($arPreset['OPTIONS'] as $optionCode => $value){
									if(isset($arResult[$optionCode]) && $arResult[$optionCode]['THEME'] === 'Y'){
										if(is_array($value)){
											if(isset($value['VALUE'])){
												if($arResult[$optionCode]['VALUE'] != $value['VALUE']){
													$bCurrent = false;
													break;
												}
											}

											if(is_array($value['SUB_PARAMS']) && $arResult[$optionCode]['SUB_PARAMS'] && isset($arResult[$optionCode]['SUB_PARAMS'][$value['VALUE']])){
												foreach($value['SUB_PARAMS'] as $subOptionCode => $subValue){
													if(isset($arResult[$optionCode]['SUB_PARAMS'][$value['VALUE']][$subOptionCode])){
														if(is_array($subValue)){
															if(isset($subValue['VALUE'])){
																if($arResult[$optionCode]['SUB_PARAMS'][$value['VALUE']][$subOptionCode]['VALUE'] != $subValue['VALUE']){
																	$bCurrent = false;
																	break 2;
																}

																if(isset($subValue['TEMPLATE']) && isset($arResult['TEMPLATE_PARAMS'][$value['VALUE']][$value['VALUE'].'_'.$subOptionCode.'_TEMPLATE'])){
																	if($arResult['TEMPLATE_PARAMS'][$value['VALUE']][$value['VALUE'].'_'.$subOptionCode.'_TEMPLATE']['VALUE'] != $subValue['TEMPLATE']){
																		$bCurrent = false;
																		break 2;
																	}
																}
															}
														}
														else{
															if($arResult[$optionCode]['SUB_PARAMS'][$value['VALUE']][$subOptionCode]['VALUE'] != $subValue){
																$bCurrent = false;
																break 2;
															}
														}
													}
												}
											}
										}
										else{
											if($arResult[$optionCode]['VALUE'] != $value){
												$bCurrent = false;
												break;
											}
										}
									}
								}
							}
							else{
								$bCurrent = false;
							}
							?>
							<div class="preset-block link-item animation-boxs<?=($bCurrent ? ' current' : '')?><?=($arPreset['IMG'] ? '' : ' no_img')?>" data-id="<?=$arPreset['ID']?>">
								<?if($arPreset['IMG']):?>
									<div class="image"><img src="<?=$arPreset['IMG']?>" title="<?=$arPreset['TITLE']?>" /></div>
								<?endif;?>
								<div class="info">
									<div class="title"><?=$arPreset['TITLE']?></div>
									<div class="description"><?=$arPreset['DESCRIPTION']?></div>
								</div>
								<div class="clearfix"></div>
							</div>
						</div>
					<?endforeach;?>
				</div>
			</div>
		</div>
	</form>
	<div class="clearfix"></div>
</div>