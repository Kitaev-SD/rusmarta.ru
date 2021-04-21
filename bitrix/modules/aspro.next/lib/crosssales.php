<?
namespace Aspro\Next;
use	CNext as Solution,
	CNextCache as Cache,
	CNextCondition as Condition,
	\Aspro\Next\Property\CustomFilter,
	\Bitrix\Main\Web\Json;

class CrossSales {
	const ALL_USERS_GROUP_ID = 2;
	const IBLOCK_TYPE = 'aspro_next_catalog';
	const IBLOCK_CODE = 'aspro_next_cross_sales';
	const PROPERTY_EXT_PRODUCTS_FILTER_CODE = 'EXT_PRODUCTS_FILTER';

	protected static $arShowPlacesByIblockId = array();

	protected $siteId;
	protected $iblockId;
	protected $arParams;
	protected $arRules;

	protected $productId;
	protected $productIblockId;
	protected $arProductSelect;
	protected $arProduct;

	public function __construct($productId, $arParams = array()){
		$this->setProduct($productId, $arParams);
	}

	public function __set($name, $value){
		switch($name){
			case 'productId':
				$this->setProduct($value, $this->arParams);
				break;
			case 'arParams':
				$this->setProduct($this->productId, $arParams);
				break;
			case 'siteId':
				$this->siteId = $value;
				$this->iblockId = self::_getSiteIblockId($this->siteId);
				$this->arParams = $this->arProduct = $this->arProductSelect = array();
				break;
		}

		return $value;
	}

	public function __get($name){
		if(property_exists($this, $name)){
			return $this->{$name};
		}

		return null;
	}

	protected function _reset(){
		$this->productId = $this->productIblockId = $this->arRules = $this->$siteId = $this->iblockId = false;
		$this->arParams = $this->arProduct = $this->arProductSelect = array();
	}

	public function setProduct($productId, $arParams = array()){
		$this->_reset();

		if(($productId = intval($productId)) > 0){
			$this->productId = $productId;

			$this->siteId = defined('SITE_ID') ? SITE_ID : false;
			$this->iblockId = self::_getSiteIblockId($this->siteId);
			$this->arParams = $arParams && is_array($arParams) ? $arParams : array();

			$productIblockId = $arParams['IBLOCK_ID'] ? $arParams['IBLOCK_ID'] : false;
			if(($productIblockId = intval($productIblockId)) > 0){
				$this->productIblockId = $productIblockId;
			}
			else{
				$this->productIblockId = self::_getProductIblockId($productId);
			}
		}
	}

	public function getRules(){
		if($this->arRules === false){
			$arRules = array();

			if($this->iblockId){
				$propertyProductsFilterIblockId = CustomFilter::getSettingsIblockId('PRODUCTS_FILTER', $this->iblockId);

				if($propertyProductsFilterIblockId == $this->productIblockId){
					$arRulesTmp = array();

					// get current user groups
					$arUserGroups = self::_getUserGroups();

					// collect show places variants
					$arShowPlaces = self::_getShowPlaces($this->iblockId);
					$arShowPlacesFlipped = array_flip($arShowPlaces);

					$arSelect = array(
						'ID',
						'PROPERTY_PRIORITY',
						'PROPERTY_SORT',
						'PROPERTY_SHOW_PLACE',
						'PROPERTY_LAST_LEVEL_RULE',
						'PROPERTY_LAST_RULE',
						'PROPERTY_PRODUCTS_FILTER',
						'PROPERTY_EXT_PRODUCTS_FILTER',
						'PROPERTY_USER_GROUPS',
						'PROPERTY_LINK_REGION',
					);

					$arFilter = array(
						'IBLOCK_ID' => $this->iblockId,
						'ACTIVE' => 'Y',
						array(
							'LOGIC' => 'OR',
							array('PROPERTY_USER_GROUPS' => $arUserGroups),
							array('PROPERTY_USER_GROUPS' => false),
						),
					);

					// use region
					if($GLOBALS['arRegion'] && $GLOBALS['arTheme']['USE_REGIONALITY']['VALUE'] === 'Y'){
						$arFilter[] = array(
							'LOGIC' => 'OR',
							array('PROPERTY_LINK_REGION' => $GLOBALS['arRegion']['ID']),
							array('PROPERTY_LINK_REGION' => false),
						);
					}

					// get all rules for current user groups in current region
					if($arRulesTmp = Cache::CIBLockElement_GetList(
						array(
							'property_PRIORITY' => 'DESC',
							'property_SORT' => 'ASC',
							'CACHE' => array(
								'MULTI' => 'Y',
								'TAG' => Cache::GetIBlockCacheTag($this->iblockId),
							)
						),
						$arFilter,
						false,
						false,
						$arSelect
					)){
						// get active by date without cache
						$arRulesIDs = array_column($arRulesTmp, 'ID');
						$dbRes = \CIBlockElement::GetList(
							array(),
							array(
								'ID' => $arRulesIDs,
								'IBLOCK_ID' => $this->iblockId,
								'ACTIVE_DATE' => 'Y',
							),
							false,
							false,
							array('ID')
						);
						$arRulesIDs = array();
						while($arRule = $dbRes->Fetch()){
							$arRulesIDs[] = $arRule['ID'];
						}

						if($arRulesIDs){
							$obCache = new \CPHPCache();
							$cacheTime = 36000000;
							$cacheTag = Cache::GetIBlockCacheTag($this->iblockId);
							$cachePath = '/CNextCache/iblock/CIBlockElement_GetList/'.$cacheTag.'/';
							$cacheID = 'CIBlockElement_GetList_'.$cacheTag.md5(serialize($arRulesIDs));
							if($obCache->InitCache($cacheTime, $cacheID, $cachePath)){
								$res = $obCache->GetVars();
								$arRulesTmp = $res['arRulesTmp'];
								$this->arProductSelect = $res['arProductSelect'];
							}
							else{
								$this->arProductSelect = array(
									'ID',
									'IBLOCK_ID',
								);


								// collect parsed conditions from PRODUCT_FILTER property
								// collect product filds for select
								$cond = new Condition();
								foreach($arRulesTmp as $i => &$arRule){
									if(in_array($arRule['ID'], $arRulesIDs)){
										$bBadProductsFilter = true;
										if(is_string($arRule['PROPERTY_PRODUCTS_FILTER_VALUE'])){
											if($arRule['PROPERTY_PRODUCTS_FILTER_VALUE']){
												$arTmpProductsFilter = Json::decode($arRule['PROPERTY_PRODUCTS_FILTER_VALUE']);
												if(is_array($arTmpProductsFilter)){
													try{
														$arRule['PROPERTY_PRODUCTS_FILTER_VALUE'] =  $this->parseCondition($arTmpProductsFilter, $this->arParams);
														$bBadProductsFilter = false;
													}
													catch(\Exception $e){
														$arRule['PROPERTY_PRODUCTS_FILTER_VALUE'] = false;
													}
												}
											}
											else{
												$bBadProductsFilter = false;
											}
										}

										if(!$bBadProductsFilter){
											if(!$arRule['PROPERTY_PRODUCTS_FILTER_VALUE']){
												$arRule['PROPERTY_PRODUCTS_FILTER_VALUE'] = array('IBLOCK_ID' => $propertyProductsFilterIblockId);
											}
											if(isset($arRule['PROPERTY_EXT_PRODUCTS_FILTER_VALUE']) && is_string($arRule['PROPERTY_EXT_PRODUCTS_FILTER_VALUE'])){
												$arTmpExtProductsFilter = Json::decode($arRule['PROPERTY_EXT_PRODUCTS_FILTER_VALUE']);
												if($arTmpExtProductsFilter && $arTmpExtProductsFilter['CHILDREN']){
													try{
														$arRule['PROPERTY_EXT_PRODUCTS_FILTER_VALUE'] = $cond->parseCondition($arTmpExtProductsFilter, $this->arParams);
													}
													catch(\Exception $e){
														$arRule['PROPERTY_EXT_PRODUCTS_FILTER_VALUE'] = array();
													}

													if($arRule['PROPERTY_EXT_PRODUCTS_FILTER_VALUE']){
														$arRule['PROPERTY_PRIORITY_VALUE'] = intval($arRule['PROPERTY_PRIORITY_VALUE']);

														$arRule['PROPERTY_SHOW_PLACE_ENUM_ID'] = ($arRule['PROPERTY_SHOW_PLACE_ENUM_ID'] ? (array)$arRule['PROPERTY_SHOW_PLACE_ENUM_ID'] : array_values($arShowPlaces));

														// next rule
														continue;
													}
												}
											}
										}
									}

									// remove bad rule
									unset($arRulesTmp[$i]);
								}
								unset($arRule);

								if($cond->arProductSelect){
									$this->arProductSelect = array_merge(
										$this->arProductSelect,
										$cond->arProductSelect
									);
								}
								unset($cond);

								$obCache->StartDataCache($cacheTime, $cacheID, $cachePath);
								if(strlen($cacheTag)){
									$GLOBALS['CACHE_MANAGER']->StartTagCache($cachePath);
									$GLOBALS['CACHE_MANAGER']->RegisterTag($cacheTag);
									$GLOBALS['CACHE_MANAGER']->EndTagCache();
								}

								$obCache->EndDataCache(array('arRulesTmp' => $arRulesTmp, 'arProductSelect' => $this->arProductSelect));
							}
						}

						// get some fields & properties of product
						if($arRulesTmp){
							$this->arProductSelect = array_unique($this->arProductSelect);

							$this->arProduct = Cache::CIBLockElement_GetList(
								array(
									'CACHE' => array(
										'MULTI' => 'N',
										'TAG' => Cache::GetIBlockCacheTag($this->productIblockId),
									)
								),
								array(
									'ID' => $this->productId,
									'IBLOCK_ID' => $this->productIblockId,
								),
								false,
								false,
								$this->arProductSelect
							);

							// get all parents sections of product
							if($this->arProduct['IBLOCK_SECTION_ID']){
								$arSectionsIDs = $this->arProduct['IBLOCK_SECTION_ID'] = (array)$this->arProduct['IBLOCK_SECTION_ID'];
								while($arSectionsIDs){
									if($arSections = Cache::CIBlockSection_GetList(
										array(
											'CACHE' => array(
												'MULTI' => 'Y',
												'TAG' => Cache::GetIBlockCacheTag($this->productIblockId),
												'GROUP' => array('IBLOCK_SECTION_ID'),
											)
										),
										array(
											'ID' => $arSectionsIDs,
											'IBLOCK_ID' => $this->productIblockId,
											'!SECTION_ID' => false,
										),
										false,
										array(
											'ID',
											'IBLOCK_SECTION_ID',
										)
									)){
										$arSectionsIDs = array_keys($arSections);
										$this->arProduct['IBLOCK_SECTION_ID'] = array_unique(array_merge($this->arProduct['IBLOCK_SECTION_ID'], $arSectionsIDs));
									}
									else{
										$arSectionsIDs = array();
									}
								}
							}

							// echo '<pre>';
							// print_r($this->arProduct);
							// echo '</pre>';

							$arLastRule = $arLastLevelRule = array();
							foreach($arRulesTmp as $i => &$arRule){
								// echo '<pre>';
								// print_r($arRule['PROPERTY_PRODUCTS_FILTER_VALUE']);
								// echo '</pre>';

								if($this->_checkParsedCondition($arRule['PROPERTY_PRODUCTS_FILTER_VALUE'])){
									$this->_replaceCond2ProductValues($arRule['PROPERTY_EXT_PRODUCTS_FILTER_VALUE']);

									$arRules['ALL'][$arRule['ID']] = &$arRule;
									foreach($arRule['PROPERTY_SHOW_PLACE_ENUM_ID'] as $showPlaceID){
										if(!$arLastRule[$showPlaceID]){
											if($arRule['PROPERTY_PRIORITY_VALUE'] > $arLastLevelRule[$showPlaceID]){
												$arRules[$arShowPlacesFlipped[$showPlaceID]][$arRule['ID']] = &$arRule;

												if($arRule['PROPERTY_LAST_RULE_VALUE']){
													$arLastRule[$showPlaceID] = true;
												}

												if($arRule['PROPERTY_LAST_LEVEL_RULE_VALUE']) {
													$arLastLevelRule[$showPlaceID] = $arRule['PROPERTY_PRIORITY_VALUE'];
												}
											}
										}
									}
								}
							}
							unset($arRule);
						}
					}
				}
			}

			$this->arRules = $arRules;
		}

		return $arRules;
	}

	public function getItemsFilter($showPlace = ''){
		$arFilter = array();

		if($this->arRules){
			if($arRules = strlen($showPlace) ? $this->arRules[$showPlace] : $this->arRules['ALL']){
				$arFilter = array_column($arRules, 'PROPERTY_EXT_PRODUCTS_FILTER_VALUE');

				if(count($arFilter) > 1){
					$arFilter['LOGIC'] = 'OR';
				}
				else{
					$arFilter = reset($arFilter);
				}

				$arFilter = array(
					'LOGIC' => 'AND',
					(
						($propertyExtProductsFilterIblockId = CustomFilter::getSettingsIblockId('EXT_PRODUCTS_FILTER', $this->iblockId)) ?
							array('!ID' => $this->productId, 'IBLOCK_ID' => $propertyExtProductsFilterIblockId, 'ACTIVE' => 'Y') :
							array('!ID' => $this->productId, 'ACTIVE' => 'Y')
					),
					$arFilter
				);

				if($GLOBALS['arRegion'] && $GLOBALS['arTheme']['USE_REGIONALITY']['VALUE'] === 'Y' && $GLOBALS['arTheme']['USE_REGIONALITY']['DEPENDENT_PARAMS']['REGIONALITY_FILTER_ITEM']['VALUE'] === 'Y'){
					$regionId = $GLOBALS['arRegion']['ID'];
					if($arSectionsIds = Solution::getSectionsIds_NotInRegion($this->iblockId, $regionId)){
						$arFilter['!IBLOCK_SECTION_ID'] = $arSectionsIds;
					}
				}
			}
		}

		return $arFilter;
	}

	public function getItems($showPlace = '', $sort = 'rand', $order = 'ASC'){
		if($arFilter = $this->getItemsFilter($showPlace)){
			$displayElementSlider = ($this->arParams['DISPLAY_ELEMENT_SLIDER'] ? $this->arParams['DISPLAY_ELEMENT_SLIDER'] : 10);

			$propertyExtProductsFilterIblockId = CustomFilter::getSettingsIblockId('EXT_PRODUCTS_FILTER', $this->iblockId);

			// echo '<pre>';
			// print_r($arFilter);
			// echo '</pre>';

			return Cache::CIBLockElement_GetList(
				array(
					$sort => $order,
					'CACHE' => array(
						'TIME' => ($this->arParams['CACHE_TYPE'] !== 'N' ? $this->arParams['CACHE_TIME'] : 0),
						'MULTI' => 'Y',
						'TAG' => Cache::GetIBlockCacheTag($propertyExtProductsFilterIblockId),
						'RESULT' => array('ID'),
					)
				),
				$arFilter,
				false,
				array('nTopCount' => $displayElementSlider),
				array(
					'ID',
					'IBLOCK_ID',
				)
			);
		}

		return false;
	}

	protected static function _getSiteIblockId($siteId){
		return Cache::$arIBlocks[$siteId][self::IBLOCK_TYPE][self::IBLOCK_CODE][0] ? Cache::$arIBlocks[$siteId][self::IBLOCK_TYPE][self::IBLOCK_CODE][0] : false;
	}

	protected static function _getProductIblockId($productId){
		return \CIBlockElement::GetIBlockByID($productId);
	}

	protected static function _getShowPlaces($iblockId){
		$arPlaces = array();

		if(($iblockId = intval($iblockId)) > 0){
			if(!isset(self::$arShowPlacesByIblockId[$iblockId])){
				$dbRes = \CIBlockProperty::GetPropertyEnum('SHOW_PLACE', array(), array('IBLOCK_ID' => $iblockId));
				while($arVariant = $dbRes->Fetch()){
					$arPlaces[$arVariant['XML_ID']] = $arVariant['ID'];
				}
			}
			else{
				$arPlaces = self::$arShowPlacesByIblockId[$iblockId];
			}
		}

		return $arPlaces;
	}

	protected static function _getUserGroups(){
		$arGroups = array();

		if(isset($GLOBALS['USER']) && $GLOBALS['USER']->IsAuthorized()){
			$resUserGroup = \Bitrix\Main\UserGroupTable::getList(
				array(
					'filter' => array(
						'USER_ID' => $GLOBALS['USER']->GetID(),
					),
					'select' => array('GROUP_ID'),
				)
			);
			while($arGroup = $resUserGroup->fetch()){
			   $arGroups[] = $arGroup['GROUP_ID'];
			}
		}

		$arGroups[] = self::ALL_USERS_GROUP_ID;

		return $arGroups;
	}

	public static function isCrossSalesIblock($iblockId){
		return $iblockId && isset(Cache::$arIBlocksInfo[$iblockId]) && strpos(Cache::$arIBlocksInfo[$iblockId]['CODE'], self::IBLOCK_CODE) !== false;
	}

	public function parseCondition($condition, $params)
	{
		$result = array();

		if (!empty($condition) && is_array($condition))
		{
			if ($condition['CLASS_ID'] === 'CondGroup')
			{
				if (!empty($condition['CHILDREN']))
				{
					foreach ($condition['CHILDREN'] as $child)
					{
						$childResult = $this->parseCondition($child, $params);

						// is group
						if ($child['CLASS_ID'] === 'CondGroup')
						{
							$result[] = $childResult;
						}
						// same property names not overrides each other
						elseif (isset($result[key($childResult)]))
						{
							$fieldName = key($childResult);

							if (!isset($result['LOGIC']))
							{
								$result = array(
									'LOGIC' => $condition['DATA']['All'],
									array($fieldName => $result[$fieldName])
								);
							}

							$result[][$fieldName] = $childResult[$fieldName];
						}
						else
						{
							$result += $childResult;
						}
					}

					if (!empty($result))
					{
						$this->parsePropertyCondition($result, $condition, $params);

						if (count($result) > 1)
						{
							$result['LOGIC'] = $condition['DATA']['All'];
						}
					}
				}
			}
			else
			{
				$result += $this->parseConditionLevel($condition, $params);
			}
		}

		return $result;
	}

	protected function parseConditionLevel($condition, $params)
	{
		$result = array();

		if (!empty($condition) && is_array($condition))
		{
			$name = $this->parseConditionName($condition);

			if (!empty($name))
			{
				$operator = $this->parseConditionOperator($condition);
				$value = $this->parseConditionValue($condition, $name);
				$result[$operator.$name] = array(
					'NAME' => $name,
					'OPERATOR' => $operator,
					'VALUE' => $value,
				);

				if(strpos($name, 'CondIBProp') === false){
					$this->arProductSelect[] = $name;
				}
			}
		}

		return $result;
	}

	protected function parseConditionName(array $condition)
	{
		$name = '';
		$conditionNameMap = array(
			'CondIBXmlID' => 'XML_ID',
			'CondIBActive' => 'ACTIVE',
			'CondIBSection' => 'IBLOCK_SECTION_ID',
			'CondIBDateActiveFrom' => 'DATE_ACTIVE_FROM',
			'CondIBDateActiveTo' => 'DATE_ACTIVE_TO',
			'CondIBSort' => 'SORT',
			'CondIBDateCreate' => 'DATE_CREATE',
			'CondIBCreatedBy' => 'CREATED_BY',
			'CondIBTimestampX' => 'TIMESTAMP_X',
			'CondIBModifiedBy' => 'MODIFIED_BY',
			'CondIBTags' => 'TAGS',
			'CondCatQuantity' => 'CATALOG_QUANTITY',
			'CondCatWeight' => 'CATALOG_WEIGHT',
			'CondIBName' => 'NAME',
			'CondIBElement' => 'ID',
		);

		if (isset($conditionNameMap[$condition['CLASS_ID']]))
		{
			$name = $conditionNameMap[$condition['CLASS_ID']];
		}
		elseif (strpos($condition['CLASS_ID'], 'CondIBProp') !== false)
		{
			$name = $condition['CLASS_ID'];
		}

		return $name;
	}

	protected function parseConditionOperator($condition)
	{
		$operator = '';

		switch ($condition['DATA']['logic'])
		{
			case 'Equal':
				$operator = '==';
				break;
			case 'Not':
				$operator = '!';
				break;
			case 'Contain':
				$operator = '%';
				break;
			case 'NotCont':
				$operator = '!%';
				break;
			case 'Great':
				$operator = '>';
				break;
			case 'Less':
				$operator = '<';
				break;
			case 'EqGr':
				$operator = '>=';
				break;
			case 'EqLs':
				$operator = '<=';
				break;
		}

		return $operator;
	}

	protected function parseConditionValue($condition, $name)
	{
		$value = $condition['DATA']['value'];

		switch ($name)
		{
			case 'DATE_ACTIVE_FROM':
			case 'DATE_ACTIVE_TO':
			case 'DATE_CREATE':
			case 'TIMESTAMP_X':
				$value = ConvertTimeStamp($value, 'FULL');
				break;
		}

		return $value;
	}

	protected function parsePropertyCondition(array &$result, array $condition, $params)
	{
		static $arPropertiesCodes;

		if (!empty($result))
		{
			$subFilter = array();

			foreach ($result as $name => $value)
			{
				if (!empty($result[$name]) && is_array($result[$name]) && !isset($result[$name]['NAME']))
				{
					$this->parsePropertyCondition($result[$name], $condition, $params);
				}
				else
				{
					if (($ind = strpos($name, 'CondIBProp')) !== false)
					{
						list($prefix, $iblock, $propertyId) = explode(':', $name);

						$operator = $ind > 0 ? substr($prefix, 0, $ind) : '';

						$catalogInfo = \CCatalogSku::GetInfoByIBlock($iblock);

						if(!isset($arPropertiesCodes)){
							$arPropertiesCodes = array();
						}

						if(!array_key_exists($propertyId, $arPropertiesCodes)){
							$propCode = \CIBlockProperty::GetByID($propertyId, $iblock)->Fetch()['CODE'];
							$propCode = strtoupper($propCode);
							$arPropertiesCodes[$propertyId] = $propCode;
						}
						else{
							$propCode = $arPropertiesCodes[$propertyId];
						}

						if (
							$catalogInfo['CATALOG_TYPE'] != \CCatalogSku::TYPE_CATALOG
							&& $catalogInfo['IBLOCK_ID'] == $iblock
						)
						{
							$subFilter[$operator.'PROPERTY_'.$propCode] = $value;
							$subFilter[$operator.'PROPERTY_'.$propCode]['NAME'] = 'PROPERTY_'.$propCode;
							$subFilter[$operator.'PROPERTY_'.$propCode]['PROPERTY'] = $propCode;
						}
						else
						{
							$result[$operator.'PROPERTY_'.$propCode] = $value;
							$result[$operator.'PROPERTY_'.$propCode]['NAME'] = 'PROPERTY_'.$propCode;
							$result[$operator.'PROPERTY_'.$propCode]['PROPERTY'] = $propCode;
						}

						$this->arProductSelect[] = 'PROPERTY_'.$propCode;

						unset($result[$name]);
					}
				}
			}

			if (!empty($subFilter) && !empty($catalogInfo))
			{
				$offerPropFilter = array(
					'IBLOCK_ID' => $catalogInfo['IBLOCK_ID'],
					'ACTIVE_DATE' => 'Y',
					'ACTIVE' => 'Y'
				);

				if ($params['HIDE_NOT_AVAILABLE_OFFERS'] === 'Y')
				{
					$offerPropFilter['HIDE_NOT_AVAILABLE'] = 'Y';
				}
				elseif ($params['HIDE_NOT_AVAILABLE_OFFERS'] === 'L')
				{
					$offerPropFilter[] = array(
						'LOGIC' => 'OR',
						'CATALOG_AVAILABLE' => 'Y',
						'CATALOG_SUBSCRIBE' => 'Y'
					);
				}

				if (count($subFilter) > 1)
				{
					$subFilter['LOGIC'] = $condition['DATA']['All'];
					$subFilter = array($subFilter);
				}

				$result += $subFilter;
			}
		}
	}

	protected function _checkParsedCondition($arParsedCondition){
		if($arParsedCondition && is_array($arParsedCondition)){
			foreach($arParsedCondition as $key => $value){
				if(!is_array($value)){
					continue;
				}

				if(is_numeric($key)){
					$arParsedCondition[$key] = $this->_checkParsedCondition($value);
				}
				else{
					unset($find);

					if(isset($value['PROPERTY'])){
						$find = isset($this->arProduct['PROPERTY_'.$value['PROPERTY'].'_ENUM_ID']) ? $this->arProduct['PROPERTY_'.$value['PROPERTY'].'_ENUM_ID'] : $this->arProduct['PROPERTY_'.$value['PROPERTY'].'_VALUE'];
					}
					else{
						$find = $this->arProduct[$value['NAME']];
					}

					if($find){
						if(is_array($value['VALUE'])){
							foreach($value['VALUE'] as &$v){
								if(($timestamp = MakeTimeStamp($v)) !== false){
									$v = $timestamp;
								}
							}
						}
						else{
							if(($timestamp = MakeTimeStamp($value['VALUE'])) !== false){
								$value['VALUE'] = $timestamp;
							}
						}

						if(is_array($find)){
							foreach($find as &$v){
								if(($timestamp = MakeTimeStamp($v)) !== false){
									$v = $timestamp;
								}
							}
						}
						else{
							if(($timestamp = MakeTimeStamp($find)) !== false){
								$find = $timestamp;
							}
						}
					}

					switch($value['OPERATOR']){
						case '==':
							if(is_array($value['VALUE'])){
								$arParsedCondition[$key] = isset($find) && in_array($find, $value['VALUE']);
							}
							else{
								if(isset($find) && is_array($find)){
									$arParsedCondition[$key] = in_array($value['VALUE'], $find);
								}
								else{
									$arParsedCondition[$key] = isset($find) && $find == $value['VALUE'];
								}
							}
							break;
						case '!':
							if(is_array($value['VALUE'])){
								$arParsedCondition[$key] = isset($find) && !in_array($find, $value['VALUE']);
							}
							else{
								if(isset($find) && is_array($find)){
									$arParsedCondition[$key] = !in_array($value['VALUE'], $find);
								}
								else{
									$arParsedCondition[$key] = isset($find) && $find != $value['VALUE'];
								}
							}
							break;
						case '<':
							$arParsedCondition[$key] = isset($find) && $find < $value['VALUE'];
							break;
						case '<=':
							$arParsedCondition[$key] = isset($find) && $find <= $value['VALUE'];
							break;
						case '>':
							$arParsedCondition[$key] = isset($find) && $find > $value['VALUE'];
							break;
						case '>=':
							$arParsedCondition[$key] = isset($find) && $find >= $value['VALUE'];
							break;
						case '%':
							$arParsedCondition[$key] = isset($find) && strpos($find, $value['VALUE']) !== false;
							break;
						case '!%':
							$arParsedCondition[$key] = isset($find) && strpos($find, $value['VALUE']) === false;
							break;
					}
				}

				if($arParsedCondition['LOGIC'] === 'AND' && $arParsedCondition[$key] == false){
					$arParsedCondition = false;
					break;
				}

				if($arParsedCondition['LOGIC'] === 'OR' && $arParsedCondition[$key] == true){
					$arParsedCondition = true;
					break;
				}

				if(!isset($arParsedCondition['LOGIC'])){
					if($arParsedCondition[$key] == true){
						$arParsedCondition = true;
					}
					else{
						$arParsedCondition = false;
					}
				}
			}

			if(is_array($arParsedCondition)){
				if($arParsedCondition['LOGIC'] === 'OR'){
					$arParsedCondition = false;
				}
				else{
					$arParsedCondition = true;
				}
			}
		}

		return boolval($arParsedCondition);
	}

	protected function _replaceCond2ProductValues(&$arFilter){
		static $arMultipleListPropertiesIDs;

		if($arFilter && is_array($arFilter)){
			foreach($arFilter as $key => $value){
				if(is_array($value)){
					$this->_replaceCond2ProductValues($arFilter[$key]);
				}
				else{
					if(strpos($value, 'CondCrossIBProp') !== false){
						list($prefix, $iblock, $propertyId) = explode(':', $value);

						$find = isset($this->arProduct['PROPERTY_'.$propertyId.'_ENUM_ID']) ? $this->arProduct['PROPERTY_'.$propertyId.'_ENUM_ID'] : $this->arProduct['PROPERTY_'.$propertyId.'_VALUE'];

						$bSIn = strpos($key, '=') !== false;
						$bNotEqual = strpos($key, '!') !== false;
						$bIn = $bEqual = !$bSIn && !$bNotEqual;

						if($bNotEqual || $bSIn){
							if($iblock){
								if(!isset($arrayrMultipleListPropertiesIDs)){
									$arMultipleListPropertiesIDs = array();
									$dbRes = \CIBlockProperty::GetList(
										array(),
										array('IBLOCK_ID' => $iblock, 'ACTIVE' => 'Y', 'PROPERTY_TYPE' => 'L', 'MULTIPLE' => 'Y')
									);
									while($arProperty = $dbRes->Fetch()){
										$arMultipleListPropertiesIDs[$arProperty['ID']] = true;
									}
								}
							}
						}

						if(!isset($find) || !$find){
							if($bEqual || $bIn || $bSIn){
								$arFilter[$key] = false;
							}
							elseif($bNotEqual){
								$arFilter[$key] = false;
							}
						}
						elseif(isset($find)){
							if(($bNotEqual || $bSIn) && $arMultipleListPropertiesIDs && $arMultipleListPropertiesIDs[$propertyId]){
								unset($arFilter[$key]);
								$arFind = is_array($find) ? $find : array($find);
								foreach($arFind as $find){
									$arFilter[] = array(
										($bNotEqual ? '!' : '').'ID' => \CIBlockElement::SubQuery('ID', array('IBLOCK_ID' => $iblock, 'PROPERTY_'.$propertyId => $find)),
									);
								}
							}
							else{
								$arFilter[$key] = $find;
							}
						}
					}
				}
			}
		}
	}
}
?>