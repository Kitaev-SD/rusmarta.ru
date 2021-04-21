<?
namespace Aspro\Next;
use CNextCache as Cache;

define('TREG_CYR', \Bitrix\Main\Localization\Loc::getMessage('TREG_CYR'));
define('CYR_E', \Bitrix\Main\Localization\Loc::getMessage('CYR_E'));
define('CYR_IO', \Bitrix\Main\Localization\Loc::getMessage('CYR_IO'));

class SearchQuery {
	const META_HASH_HAS_FIXED_COUNT = 1;
	const META_HASH_HAS_FIXED_ORDER = 2;
	const META_HASH_HAS_FIXED_FORMS = 4;
	const META_HASH_HAS_COMPLEX = 8;
	const META_HASH_HAS_STOP_WORDS = 16;
	const META_HASH_HAS_MINUS_WORDS = 32;
	const META_HASH_NOT_VALID = 'NOT VALID';
	const META_DATA_NOT_VALID = 'NOT VALID';
	const IBLOCK_TYPE = 'aspro_next_catalog';
	const IBLOCK_CODE = 'aspro_next_search';
	const IS_SEARCH_TITLE_NO_XML_ID = 'N';
	const IS_SEARCH_TITLE_BY_NAME_XML_ID = 'NAME';
	const IS_SEARCH_TITLE_BY_QUERY_XML_ID = 'QUERY';
	const IS_SEARCH_TITLE_BY_QUERY_NOT_STRONG_XML_ID = 'QUERY_NOT_STRONG';

	protected static $arStopWords;

	protected $query;
	protected $lang;
	protected $arWords;
	protected $cntWords;
	protected $arStems;

	public function __construct($query, $lang = 'ru'){
		$this->setQuery($query, $lang);
	}

	public function __set($name, $value){
		switch($name){
			case 'query':
				$this->setQuery($value, $this->lang);
				break;
			case 'lang':
				$this->setQuery($this->query, $value);
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

	private function _reset(){
		$this->query = '';
		$this->lang = 'ru';
		$this->cntWords = 0;
		$this->arWords = $this->arStems = array();
	}

	public function setQuery($query, $lang = 'ru'){
		$this->_reset();

		if(strlen($query)){
			$query = \ToLower($query, $lang);
			$query = str_replace(CYR_IO, CYR_E, $query);
			$query = preg_replace('/&#?[a-z0-9]{2,8};/'.BX_UTF_PCRE_MODIFIER, '' ,$query);
			$query = preg_replace('/[^-a-zA-Z'.TREG_CYR.'0-9\s]/'.BX_UTF_PCRE_MODIFIER, '', $query);
			$query = preg_replace('/[^-+a-zA-Z'.TREG_CYR.'0-9]/'.BX_UTF_PCRE_MODIFIER, ' ', $query);
			$query = preg_replace('/[-]{2,}/'.BX_UTF_PCRE_MODIFIER, '-', $query);
			$query = preg_replace('/[+]{2,}/'.BX_UTF_PCRE_MODIFIER, '+', $query);
			$query = preg_replace('/([\s+-]|^)[+-]/'.BX_UTF_PCRE_MODIFIER, ' ', $query);
			$query = preg_replace('/[+-]([\s+-]|$)/'.BX_UTF_PCRE_MODIFIER, ' ', $query);
			$query = trim(preg_replace('/\s+/'.BX_UTF_PCRE_MODIFIER, ' ', $query));
		}

		$this->query = $query;
		$this->lang = $lang;

		$this->arWords = self::sentence2words($this->query);
		$this->cntWords = count($this->arWords);
		$this->arStems = self::stemming($this->query, $lang);
	}

	public function getLandings($arOrder = array('SORT' => 'ASC', 'ID' => 'ASC'), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array(), $bOne = false, $bStrongCheck = true){
		$arPossibleLandings = $arPossibleLandingsQuery = $arLandingsIDs = $arLandings = array();

		if($this->cntWords){
			if(!$arFilter || !is_array($arFilter)){
				$arFilter = array();
			}
			if(isset($arFilter['IBLOCK_ID'])){
				$IBLOCK_ID = $arFilter['IBLOCK_ID'];
			}
			else{
				if(isset($arFilter['SITE_ID'])){
					$SITE_ID = $arFilter['SITE_ID'];
				}
				else{
					$SITE_ID = SITE_ID;
				}
				$IBLOCK_ID = Cache::$arIBlocks[$SITE_ID][self::IBLOCK_TYPE][self::IBLOCK_CODE][0];
			}
			$arFilter['IBLOCK_ID'] = $IBLOCK_ID;

			if($this->arStems){
				if(count($this->arStems) == 1){
					$arFilter['%PROPERTY_QUERY'] = $this->arStems[0];
				}
				else{
					$arQueryFilter = array(
						'LOGIC' => 'OR'
					);
					foreach($this->arStems as $stem){
						$arQueryFilter[] = array('%PROPERTY_QUERY' => $stem);
					}
					$arFilter[] = $arQueryFilter;
				}
			}
			else{
				$arFilter['%PROPERTY_QUERY'] = $this->query;
			}

			$arPossibleLandingsQuery = Cache::CIBLockElement_GetList(
				array(
					'ID' => 'ASC',
					'CACHE' => array(
						'MULTI' => 'N',
						'TAG' => Cache::GetIBlockCacheTag($IBLOCK_ID),
						'GROUP' => array('PROPERTY_QUERY_VALUE'),
						'RESULT' => array('ID'),
					),
				),
				$arFilter,
				false,
				false,
				array(
					'ID',
					'IBLOCK_ID',
					'PROPERTY_QUERY',
				)
			);

			if($arPossibleLandingsQuery){
				$arOrder_ = array('SORT' => 'ASC', 'ID' => 'ASC');
				$arFilter_ = array('ID' => array_values($arPossibleLandingsQuery));
				$arSelectFields_ = array('ID', 'IBLOCK_ID');

				$obCache = new \CPHPCache();
				$cacheTime = 36000000;
				$cacheTag = Cache::GetIBlockCacheTag($IBLOCK_ID);
				$cachePath = '/CNextCache/iblock/CIBlockElement_GetList/'.$cacheTag.'/';
				$cacheID = 'CIBlockElement_GetList_'.$cacheTag.md5(serialize(array_merge($arOrder_, array(SITE_ID), $arFilter_, array(), array(), $arSelectFields_)));
				if($obCache->InitCache($cacheTime, $cacheID, $cachePath)){
					$res = $obCache->GetVars();
					$arPossibleLandings = $res['arRes'];
				}
				else{
					$dbRes = \CIBlockElement::GetList($arOrder_, $arFilter_, false, false, $arSelectFields_);
					while($obItem = $dbRes->GetNextElement()){
						$arPossibleLanding = $obItem->GetFields();
						$arMetaHash = $obItem->GetProperties(array('value_id' => 'asc'), array('CODE' => 'META_HASH'));
						$arPossibleLanding['PROPERTY_META_HASH_VALUE'] = $arMetaHash ? $arMetaHash['META_HASH']['VALUE'] : array();
						$arMetaData = $obItem->GetProperties(array('value_id' => 'asc'), array('CODE' => 'META_DATA'));
						$arPossibleLanding['PROPERTY_META_DATA_VALUE'] = $arMetaData ? $arMetaData['META_DATA']['~VALUE'] : array();
						$arQuery = $obItem->GetProperties(array('value_id' => 'asc'), array('CODE' => 'QUERY'));
						$arPossibleLanding['PROPERTY_QUERY_VALUE'] = $arQuery ? $arQuery['QUERY']['VALUE'] : array();
						$arPossibleLandings[] = $arPossibleLanding;
					}

					if($cacheTime > 0 && \Bitrix\Main\Config\Option::get('main', 'component_cache_on', 'Y') !== 'N'){
						$obCache->StartDataCache($cacheTime, $cacheID, $cachePath);
						if(strlen($cacheTag)){
							$GLOBALS['CACHE_MANAGER']->StartTagCache($cachePath);
							$GLOBALS['CACHE_MANAGER']->RegisterTag($cacheTag);
							$GLOBALS['CACHE_MANAGER']->EndTagCache();
						}

						$obCache->EndDataCache(array('arRes' => $arPossibleLandings));
					}
				}
			}

			if($arPossibleLandings){
				$arStrongCheckedIDs = array();

				foreach($arPossibleLandings as &$arLanding){
					if(isset($arLanding['PROPERTY_QUERY_VALUE']) && isset($arLanding['PROPERTY_META_HASH_VALUE']) && isset($arLanding['PROPERTY_META_DATA_VALUE'])){
						$arLanding['PROPERTY_QUERY_VALUE'] = (array)$arLanding['PROPERTY_QUERY_VALUE'];
						$arLanding['PROPERTY_META_HASH_VALUE'] = (array)$arLanding['PROPERTY_META_HASH_VALUE'];
						$arLanding['PROPERTY_META_DATA_VALUE'] = (array)$arLanding['PROPERTY_META_DATA_VALUE'];
						$bFinded = false;

						foreach($arLanding['PROPERTY_QUERY_VALUE'] as $i => $query){
							$query = htmlspecialchars_decode($query);

							if(strlen($query) && isset($arPossibleLandingsQuery[$query]) && isset($arLanding['PROPERTY_META_HASH_VALUE'][$i]) && strlen($hash = $arLanding['PROPERTY_META_HASH_VALUE'][$i]) && isset($arLanding['PROPERTY_META_DATA_VALUE'][$i]) && strlen($arData = $arLanding['PROPERTY_META_DATA_VALUE'][$i])
							){
								$bStrongChecked = true;

								// check min count
								$cntAll = ($hash & (255 << 8)) >> 8;
								if($cntAll > count($this->arWords)){
									$bStrongChecked = false;
									if($bStrongCheck){
										continue;
									}
								}

								// check fixed count
								if($bStrongChecked){
									if($hash & self::META_HASH_HAS_FIXED_COUNT){
										$cntFixedCount = $hash >> 16;
										if($cntFixedCount != count($this->arWords)){
											$bStrongChecked = false;
											if($bStrongCheck){
												continue;
											}
										}
									}
								}

								$minusWords = $stopWords = $fixedForms = $fixedOrder = $other = false;
								$arComplex = array();

								if($arData = unserialize($arData)){
									$minusWords = $arData['MINUS'];
									$stopWords = $arData['STOP'];
									$arComplex = $arData['COMPLEX'];
									$fixedForms = $arData['FORMS'];
									$fixedOrder = $arData['ORDER'];
									$other = $arData['OTHER'];
								}

								// check minus words
								if($bHasMinusWords = ($hash & self::META_HASH_HAS_MINUS_WORDS && ($minusWords['WORDS'] || $minusWords['STEM']))){
									foreach($arMinusWords = array_filter(explode(';', $minusWords['WORDS'])) as $word){
										if(in_array($word, $this->arWords)){
											$bStrongChecked = false;
											continue 2;
										}
									}

									foreach($arMinusWords = array_filter(explode(';', $minusWords['STEM'])) as $word){
										if(in_array($word, $this->arStems)){
											$bStrongChecked = false;
											continue 2;
										}
									}
								}

								// check stop words
								if($bStrongChecked){
									if($hash & self::META_HASH_HAS_STOP_WORDS && $stopWords){
										foreach($arStopWords = array_filter(explode(';', $stopWords)) as $word){
											if(!in_array($word, $this->arWords)){
												$bStrongChecked = false;
												if($bStrongCheck){
													continue 2;
												}
												else{
													break;
												}
											}
										}
									}
								}

								// check complex
								if($bStrongChecked){
									if($bHasComplex = ($hash & self::META_HASH_HAS_COMPLEX && $arComplex)){
										foreach($arComplex as $complex){
											if(!preg_match('/'.$complex.'/'.BX_UTF_PCRE_MODIFIER, $this->query)){
												$bStrongChecked = false;
												if($bStrongCheck){
													continue 2;
												}
												else{
													break;
												}
											}
										}
									}
								}

								// check fixed forms
								if($bStrongChecked){
									if($bHasFixedForms = ($hash & self::META_HASH_HAS_FIXED_FORMS && strlen($fixedForms))){
										foreach($arFixedForms = array_filter(explode(';', $fixedForms)) as $fixedForm){
											if(!in_array($fixedForm, $this->arWords)){
												$bStrongChecked = false;
												if($bStrongCheck){
													continue 2;
												}
												else{
													break;
												}
											}
										}
									}
								}

								// check fixed order
								if($bStrongChecked){
									if($bHasFixedOrder = ($hash & self::META_HASH_HAS_FIXED_ORDER && strlen($fixedOrder))){
										foreach($arFixedOrder = array_filter(explode(';', $fixedOrder)) as $fixedOrder){
											if(strlen($fixedOrder)){
												if(!preg_match('/'.$fixedOrder.'/'.BX_UTF_PCRE_MODIFIER, $this->query)){
													$bStrongChecked = false;
													if($bStrongCheck){
														continue 2;
													}
													else{
														break;
													}
												}
											}
										}
									}
								}

								// check all words
								if($bStrongChecked){
									if(strlen($other)){
										foreach($arOther = array_filter(explode(';', $other)) as $other){
											if(strlen($other)){
												if(!in_array($other, $this->arStems) && !in_array($other, $this->arWords)){
													$bStrongChecked = false;
													if($bStrongCheck){
														continue 2;
													}
													else{
														break;
													}
												}
											}
										}
									}
								}

								if($bStrongChecked){
									$arStrongCheckedIDs[] = $arLanding['ID'];
									$bFinded = true;
									break;
								}
								elseif(!$bStrongCheck){
									$cntFindedStems = $cntNotFindedStems = 0;
									$sAll = implode(' ', array($arComplex, $fixedForms, $fixedOrder, $other));
									$sMinus = implode(' ', array($minusWords['STEM'], $minusWords['WORDS']));
									foreach($this->arStems as $stem){
										if(preg_match('/(^|[^a-zA-Z'.TREG_CYR.'0-9]+)'.$stem.'/'.BX_UTF_PCRE_MODIFIER, $sAll)){
											++$cntFindedStems;
										}
										else{
											if(preg_match('/(^|[^a-zA-Z'.TREG_CYR.'0-9]+)'.$stem.'/'.BX_UTF_PCRE_MODIFIER, $sMinus)){
												continue 2;
											}
											else{
												++$cntNotFindedStems;
											}
										}
									}

									if(($cntFindedStems >= 1) && (($cntNotFindedStems <= 1) || ($cntFindedStems >= $cntNotFindedStems))){
										$bFinded = true;
										break;
									}
								}
							}
							else{
								continue;
							}
						}

						if($bFinded){
							$arLandingsIDs[] = $arLanding['ID'];
							if($bOne){
								break;
							}
						}
					}
				}
				unset($arPossibleLandings, $arLanding);
			}

			if($arLandingsIDs){
				$arFilter = array('ID' => $arLandingsIDs, 'IBLOCK_ID' => $IBLOCK_ID);

				if(!$arOrder || !is_array($arOrder)){
					$arOrder = array('SORT' => 'ASC', 'ID' => 'ASC');
				}
				if(isset($arOrder['CACHE'])){
					$arCache = $arOrder['CACHE'];
				}
				else{
					$arCache = array();
				}
				$arCache['MULTI'] = isset($arCache['MULTI']) ? $arCache['MULTI'] : 'Y';
				$arCache['TAG'] = isset($arCache['TAG']) ? $arCache['TAG'] : Cache::GetIBlockCacheTag($IBLOCK_ID);
				$arOrder['CACHE'] = $arCache;

				if(!$arSelectFields || !is_array($arSelectFields)){
					$arSelectFields = array();
				}
				$arSelectFields = array_merge($arSelectFields, array('ID', 'IBLOCK_ID'));

				$arLandings = Cache::CIBLockElement_GetList($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields);
				if($bOne){
					$arLandings = reset($arLandings);
				}

				if(!$bStrongCheck){
					foreach($arLandings as &$arLanding){
						$arLanding['STRONG_CHECKED'] = in_array($arLanding['ID'], $arStrongCheckedIDs);
					}
				}
				unset($arLanding);
			}
		}

		return $arLandings;
	}

	public static function getTitleLandings($query, $altQuery, $arFilter, $cnt){
		$arTitleLandings = array();

		if(($cnt = intval($cnt)) > 0){
			$arLandings = $arLandingIDs = array();
			$arFilter = is_array($arFilter) ? $arFilter : array($arFilter);

			$arSelect = array(
				'ID',
				'IBLOCK_ID',
				'NAME',
				'DETAIL_PAGE_URL',
				'PROPERTY_URL_CONDITION',
				'PROPERTY_REDIRECT_URL',
				'PROPERTY_QUERY',
				'PROPERTY_IS_SEARCH_TITLE',
			);

			$oSearchQuery = new self($query);
			$arLandings = $oSearchQuery->getLandings(array(), $arFilter, false, false, $arSelect, false, false);

			// alt query
			if(!$arLandings && strlen($altQuery)){
				$oSearchQuery->setQuery($altQuery);
				$arLandings = $oSearchQuery->getLandings(array(), $arFilter, false, false, $arSelect, false, false);
			}

			if($arLandings){
				$arEnums = array();
				$dbRes = \CIBlockPropertyEnum::GetList(array('DEF' => 'DESC', 'SORT' => 'ASC'), array('CODE' => 'IS_SEARCH_TITLE'));
				while($arValue = $dbRes->Fetch()){
					if(!isset($arEnums[$arValue['EXTERNAL_ID']])){
						$arEnums[$arValue['EXTERNAL_ID']] = array();
					}

					$arEnums[$arValue['EXTERNAL_ID']][] = $arValue['ID'];
				}

				foreach($arLandings as $arLanding){
					if(
						in_array($arLanding['PROPERTY_IS_SEARCH_TITLE_ENUM_ID'], $arEnums[self::IS_SEARCH_TITLE_BY_QUERY_NOT_STRONG_XML_ID]) ||
						($arLanding['STRONG_CHECKED'] && in_array($arLanding['PROPERTY_IS_SEARCH_TITLE_ENUM_ID'], $arEnums[self::IS_SEARCH_TITLE_BY_QUERY_XML_ID]))
					){
						$catalogDir = preg_replace('/[\?].*/', '', $arLanding['DETAIL_PAGE_URL']);
						$url = self::getLandingUrl(
							$catalogDir,
							$arLanding['PROPERTY_URL_CONDITION_VALUE'],
							$arLanding['PROPERTY_REDIRECT_URL_VALUE'],
							$arLanding['PROPERTY_QUERY_VALUE']
						);

						$arTitleLandings[] = array(
							'NAME' => $arLanding['NAME'],
							'URL' => $url,
							'MODULE_ID' => 'iblock',
							'PARAM1' => self::IBLOCK_TYPE,
							'PARAM2' => $arLanding['IBLOCK_ID'],
							'ITEM_ID' => $arLanding['ID'],
						);

						if(count($arTitleLandings) >= $cnt){
							break;
						}
					}
				}
			}
		}

		return $arTitleLandings;
	}

	public static function getLandingUrl($catalogDir, $urlCondition, $redirectUrl, $arQuery){
		$url = false;

		if(strlen($urlCondition = trim($urlCondition))){
			$url = $urlCondition;
		}
		elseif(strlen($redirectUrl = trim($redirectUrl))){
			$url = $redirectUrl;
		}
		else{
			$catalogDir = '/'.trim(trim($catalogDir), '/').'/';
			$arQuery = (array)$arQuery;
			if(strlen($query = $arQuery ? trim(htmlspecialchars_decode($arQuery[0])) : '')){
				if(strlen($query = self::getSentenceExampleQuery($query))){
					$url = $catalogDir.'?q='.urlencode($query).'&spell=1';
				}
			}
		}

		return $url;
	}

	protected static function isBxSearch(){
		static $bIncluded;

		if(!isset($bIncluded)){
			$bIncluded = \Bitrix\Main\Loader::includeModule('search');
		}

		return $bIncluded;
	}

	public static function vail($count, $arStr, $bStrOnly = false) {
		$ost10 = $count % 10;
		$ost100 = $count % 100;
		$val = $arStr[2];
		if(!$count || !$ost10 || ($ost100 > 10 && $ost100 < 20)){
			$val = $arStr[2];
		}
		elseif($ost10 == 1){
			$val = $arStr[0];
		}
		elseif($ost10 > 1 && $ost10 < 5){
			$val = $arStr[1];
		}

		return ($bStrOnly ? '' : intval($count).' ').$val;
	}

	public static function correctingSentence($sentence, $lang = 'ru'){
		if(strlen($sentence = \ToLower(trim($sentence), $lang))){
			$sentence = str_replace(CYR_IO, CYR_E, $sentence);

			// remove all symbols exclude +-"!|()[] and a-zA-Z#TREG_CYR#0-9
			$sentence = preg_replace('/[^\-+"!|\(\)\[\]a-zA-Z'.TREG_CYR.'0-9]/'.BX_UTF_PCRE_MODIFIER, ' ', $sentence);

			// replace !- to -!
			$sentence = str_replace('!-', '-!', $sentence);

			// remove all repeats
			$sentence = preg_replace('/[-]{2,}/'.BX_UTF_PCRE_MODIFIER, '-', $sentence);
			$sentence = preg_replace('/[+]{2,}/'.BX_UTF_PCRE_MODIFIER, '+', $sentence);
			$sentence = preg_replace('/["]{2,}/'.BX_UTF_PCRE_MODIFIER, '"', $sentence);
			$sentence = preg_replace('/[!]{2,}/'.BX_UTF_PCRE_MODIFIER, '!', $sentence);
			$sentence = preg_replace('/[|]{2,}/'.BX_UTF_PCRE_MODIFIER, '|', $sentence);
			$sentence = preg_replace('/[\[]{2,}/'.BX_UTF_PCRE_MODIFIER, '[', $sentence);
			$sentence = preg_replace('/[\]]{2,}/'.BX_UTF_PCRE_MODIFIER, ']', $sentence);
			$sentence = preg_replace('/[\(]{2,}/'.BX_UTF_PCRE_MODIFIER, '(', $sentence);
			$sentence = preg_replace('/[\)]{2,}/'.BX_UTF_PCRE_MODIFIER, ')', $sentence);

			// remove bad complex
			$sentence = str_replace(array('(', ')', '|'), ' ', preg_replace('/[\(][\s]*([^|\)\(]+)[\s]*[|][\s]*([^|\)\(]+)[\s]*[\)]/'.BX_UTF_PCRE_MODIFIER, ' #$1@$2% ', $sentence));
			$sentence = str_replace(array('#', '%', '@'), array('(', ')', '|'), $sentence);

			// remove bad orders
			$sentence = str_replace(array('[', ']'), ' ', preg_replace('/[\[][\s]*([^\]\[\s]+[\s]+[^\]\[]+)[\s]*[\]]/'.BX_UTF_PCRE_MODIFIER, ' #$1% ', $sentence));
			$sentence = str_replace(array('#', '%'), array('[', ']'), $sentence);

			// after - remove all symbols exclude ! and a-zA-Z#TREG_CYR#0-9
			$sentence = preg_replace('/[-][!]*([^!a-zA-Z'.TREG_CYR.'0-9]|$)/'.BX_UTF_PCRE_MODIFIER, ' ', $sentence);

			// after + remove all symbols exclude ! and a-zA-Z#TREG_CYR#0-9
			$sentence = preg_replace('/[+]([^!a-zA-Z'.TREG_CYR.'0-9]|$)/'.BX_UTF_PCRE_MODIFIER, ' ', $sentence);

			// +! is correct, but it`s also +
			$sentence = str_replace(array('+!', '!+'), '+', $sentence);

			// after ! remove all symbols exclude a-zA-Z#TREG_CYR#0-9
			$sentence = preg_replace('/[!]([^a-zA-Z'.TREG_CYR.'0-9]|$)/'.BX_UTF_PCRE_MODIFIER, ' ', $sentence);

			// before and after | remove all symbols exclude -+!() and a-zA-Z#TREG_CYR#0-9
			$sentence = preg_replace('/[^\(\sa-zA-Z'.TREG_CYR.'0-9][|]/'.BX_UTF_PCRE_MODIFIER, ' ', $sentence);
			$sentence = preg_replace('/[|]([^\-+!\)\sa-zA-Z'.TREG_CYR.'0-9]|$)/'.BX_UTF_PCRE_MODIFIER, ' ', $sentence);

			// remove spaces before and after |
			$sentence = preg_replace('/\s*[|]\s*/'.BX_UTF_PCRE_MODIFIER, '|', $sentence);

			// remove space after ([ and before ]) again
			$sentence = preg_replace('/([\(\[])\s+/'.BX_UTF_PCRE_MODIFIER, '$1', $sentence);
			$sentence = preg_replace('/\s+([\)\]])/'.BX_UTF_PCRE_MODIFIER, '$1', $sentence);

			if(strpos($sentence, '"') !== false){
				$bHasFixedCount = preg_match('/^["](.*)["]$/'.BX_UTF_PCRE_MODIFIER, $sentence);
				// remove symbol "
				$sentence = str_replace('"', '', $sentence);
				if($bHasFixedCount){
					// exclude in begin and in end
					$sentence = '"'.trim($sentence).'"';
				}
			}

			$sentence = trim(preg_replace('/\s+/'.BX_UTF_PCRE_MODIFIER, ' ', $sentence));
		}

		return $sentence;
	}

	public static function getSentenceMeta($sentence, $lang = 'ru'){
		static $arCache;

		if(!isset($arCache)){
			$arCache = array();
		}
		if(!isset($arCache[$lang])){
			$arCache[$lang] = array();
		}

		$originalSentence = $sentence;

		if(!isset($arCache[$lang][$originalSentence])){
			$hash = 0;
			$arData = array();

			if(strlen($correctSentence = self::correctingSentence($sentence, $lang))){
				$sentence = $correctSentence;

				$bHasFixedCount = false;
				$cntFixedCount = $cntMinusWords = $cntStopWords = $cntComplex = $cntFixedOrder = $cntOther = $cntAll = 0;
				$arStopWords = $arOrder = $arComplex = array();

				if(strpos($sentence, '-') !== false){
					if(preg_match_all('/([\s|(\["]+|^)([-]([a-zA-Z'.TREG_CYR.'0-9-]+))|([\s|(\["]+|^)([-][!]([a-zA-Z'.TREG_CYR.'0-9-]+))/'.BX_UTF_PCRE_MODIFIER, $sentence, $arMatches)){
						$sentence = str_replace(array_filter(array_merge($arMatches[2], $arMatches[5])), '', $sentence);

						$arMinus = array(
							'WORDS' => array_filter($arMatches[6]),
							'STEM' => array(),
						);

						if($arMatches[3] = array_filter($arMatches[3])){
							foreach($arMatches[3] as $word){
								if($stem = self::stemming($word, $lang)){
									$arMinus['STEM'][] = reset($stem);
								}
								else{
									$arMinus['STEM'][] = $word;
								}
							}
						}

						if($arMinus){
							$hash |= self::META_HASH_HAS_MINUS_WORDS;
							$cntMinusWords = count($arMinus['WORDS']) + count($arMinus['STEM']);
							$arData['MINUS'] = array(
								'WORDS' => $arMinus['WORDS'] ? implode(';', array_unique($arMinus['WORDS'])) : '',
								'STEM' => $arMinus['STEM'] ? implode(';', array_unique($arMinus['STEM'])) : '',
							);
						}
					}
				}

				if(preg_match_all('/([a-zA-Z'.TREG_CYR.'0-9-]+)/'.BX_UTF_PCRE_MODIFIER, $sentence, $arMatches)){
					$cntAll = count(array_unique($arMatches[0]));
				}

				if($bHasFixedCount = (strpos($sentence, '"') !== false)){
					$hash |= self::META_HASH_HAS_FIXED_COUNT;
					$sentence = str_replace('"', '', $sentence);
					$cntFixedCount = $cntAll;
				}

				if(strpos($sentence, '+') !== false){
					if(preg_match_all('/([\s|(\["]+|^)([+]([a-zA-Z'.TREG_CYR.'0-9-]+))/'.BX_UTF_PCRE_MODIFIER, $sentence, $arMatches)){
						foreach($arMatches[3] as $i => $match){
							if(self::isStopWord($match)){
								$arStopWords[] = $match;
							}
							else{
								$correctSentence = str_replace($arMatches[2][$i], $match, $correctSentence);
							}

							$sentence = str_replace($arMatches[2][$i], $match, $sentence);
						}

						if($arStopWords){
							$hash |= self::META_HASH_HAS_STOP_WORDS;
							$arStopWords = array_unique($arStopWords);
							$cntStopWords = count($arStopWords);
							$arData['STOP'] = implode(';', $arStopWords);
						}
					}
				}

				if(strpos($sentence, '|') !== false && strpos($sentence, '(') !== false && strpos($sentence, ')') !== false){
					if(preg_match_all('/[(]([^|]+)[|]([^|]+)[)]/'.BX_UTF_PCRE_MODIFIER, $sentence, $arMatches)){
						foreach($arMatches[0] as $i => $match){
							$complex1 = $arMatches[1][$i];
							$bStem1 = false;
							if(strpos($complex1 = trim($complex1), '!') === false && !in_array($complex1, $arStopWords)){
								if(strpos($complex1, '+') === false){
									if($stem = self::stemming($complex1, $lang)){
										$complex1 = reset($stem);
										$bStem1 = true;
									}
								}
							}
							else{
								$complex1 = str_replace(array('!', '+'), '', $complex1);
							}

							$complex2 = $arMatches[2][$i];
							$bStem2 = false;
							if(strpos($complex2 = trim($complex2), '!') === false && !in_array($complex2, $arStopWords)){
								if(strpos($complex2, '+') === false){
									if($stem = self::stemming($complex2, $lang)){
										$complex2 = reset($stem);
										$bStem2 = true;
									}
								}
							}
							else{
								$complex2 = str_replace(array('!', '+'), '', $complex2);
							}

							$arComplex[] = '('.$complex1.($bStem1 ? '[a-zA-Z'.TREG_CYR.'0-9-]*' : '([\s]|$)').')|('.$complex2.($bStem2 ? '[a-zA-Z'.TREG_CYR.'0-9-]*' : '([\s]|$)').')';
						}

						if($arComplex){
							$hash |= self::META_HASH_HAS_COMPLEX;
							$arComplex = array_unique($arComplex);
							$cntComplex = count($arComplex);
							$arData['COMPLEX'] = $arComplex;
						}
					}
				}

				if(strpos($sentence, '[') !== false && strpos($sentence, ']') !== false){
					if(preg_match_all('/[\[]([^\]]*)[\]]/'.BX_UTF_PCRE_MODIFIER, $sentence, $arMatches)){
						foreach($arMatches[0] as $i => $match){
							$arOrder[$i] = array();

							if(preg_match_all('/[!a-zA-Z'.TREG_CYR.'0-9-]+|[\(]([!a-zA-Z'.TREG_CYR.'0-9-]+)[|]([!a-zA-Z'.TREG_CYR.'0-9-]+)[\)]/'.BX_UTF_PCRE_MODIFIER, $match, $arFixedOrder)){
								if($arFixedOrder[0]){
									foreach($arFixedOrder[0] as $j => $word){
										if(strlen($arFixedOrder[1][$j]) && strlen($arFixedOrder[2][$j])){
											if(in_array($complex1, $arStopWords)){
												--$cntAll;
											}

											if(strpos($complex1 = trim($arFixedOrder[1][$j]), '!') === false && !in_array($complex1, $arStopWords)){
												if($stem = self::stemming($complex1, $lang)){
													$complex1 = reset($stem).'[a-zA-Z'.TREG_CYR.'0-9-]*';
												}
											}
											else{
												$complex1 = str_replace(array('!', '+'), '', $complex1).'([\s]|$)';
											}

											if(in_array($complex2, $arStopWords)){
												--$cntAll;
											}

											if(strpos($complex2 = trim($arFixedOrder[2][$j]), '!') === false && !in_array($complex2, $arStopWords)){
												if($stem = self::stemming($complex2, $lang)){
													$complex2 = reset($stem).'[a-zA-Z'.TREG_CYR.'0-9-]*';
												}
											}
											else{
												$complex2 = str_replace(array('!', '+'), '', $complex2).'([\s]|$)';
											}

											$word = '('.implode('|', array($complex1, $complex2)).')';
										}
										else{
											if(in_array($word, $arStopWords)){
												--$cntAll;
											}

											if(strpos($word = trim($word), '!') === false && !in_array($word, $arStopWords)){
												if($stem = self::stemming($word, $lang)){
													$word = reset($stem).'[a-zA-Z'.TREG_CYR.'0-9-]*';
												}
											}
											else{
												$word = str_replace(array('!', '+'), '', $word);
											}
										}

										$arOrder[$i][] = $word;
									}

									if($arOrder[$i]){
										if(count($arOrder[$i]) > 1){
											$arOrder[$i] = implode('[\s]', $arOrder[$i]);
										}
										else{
											unset($arOrder[$i]);
										}
									}
								}
							}
						}

						if($arOrder){
							$hash |= self::META_HASH_HAS_FIXED_ORDER;
							$arOrder = array_unique($arOrder);
							$cntFixedOrder = count($arOrder);
							$arData['ORDER'] = implode(';', $arOrder);
						}
					}
				}

				$sentence = preg_replace('/[\(][^\)]*[\)]/'.BX_UTF_PCRE_MODIFIER, ' ', $sentence);
				$sentence = preg_replace('/[\[][^\]]*[\]]/'.BX_UTF_PCRE_MODIFIER, ' ', $sentence);

				if(strpos($sentence, '!') !== false){
					if(preg_match_all('/([\s|(\["]+|^)[!]([a-zA-Z'.TREG_CYR.'0-9-]+)/'.BX_UTF_PCRE_MODIFIER, $sentence, $arMatches)){
						$hash |= self::META_HASH_HAS_FIXED_FORMS;

						foreach($arMatches[0] as $match){
							$sentence = str_replace($match, ' ', $sentence);
						}

						$arData['FORMS'] = implode(';', array_unique($arMatches[2]));
					}
				}

				$sentence = trim(preg_replace('/\s+/'.BX_UTF_PCRE_MODIFIER, ' ', $sentence));

				if(strlen($sentence) && $arWords = self::sentence2words($sentence)){
					$arData['OTHER'] = array();

					foreach($arWords as $word){
						if(self::isStopWord($word)){
							if(!$bHasFixedCount){
								--$cntAll;
							}
						}
						else{
							if($stem = self::stemming($word, $lang)){
								$word = reset($stem);
							}

							$arData['OTHER'][] = $word;
						}
					}

					if($arData['OTHER']){
						$arData['OTHER'] = array_unique($arData['OTHER']);
						$cntOther = count($arData['OTHER']);
						$arData['OTHER'] = implode(';', $arData['OTHER']);
					}
					else{
						unset($arData['OTHER']);
					}
				}

				$cntAll -= $cntComplex;

				if($bHasFixedCount){
					$cntFixedCount -= $cntComplex;
					$hash = $hash | ($cntFixedCount << 16);
				}
				else{
					$cntAll += $cntStopWords;
				}

				$hash = $hash | ($cntAll << 8);
			}
			else{
				$correctSentence = $sentence;
				$hash = self::META_DATA_NOT_VALID;
				$arData = self::META_DATA_NOT_VALID;
			}

			$correctSentence = str_replace('[ (', '[(', $correctSentence);
			$correctSentence = str_replace(') ]', ')]', $correctSentence);

			$arCache[$lang][$originalSentence] = array($correctSentence, $hash, $arData);
		}

		return $arCache[$lang][$originalSentence];
	}

	public static function getSentenceExampleQuery($sentence, $lang = 'ru'){
		$query = false;

		list($correctSentence, $hash, $arData) = self::getSentenceMeta($sentence, $lang);

		if(strlen($correctSentence) && $hash !== self::META_HASH_NOT_VALID){
			$query = $correctSentence;

			if($hash & self::META_HASH_HAS_MINUS_WORDS){
				if(preg_match_all('/([\s|(\["]+|^)([-]([a-zA-Z'.TREG_CYR.'0-9-]+))|([\s|(\["]+|^)([-][!]([a-zA-Z'.TREG_CYR.'0-9-]+))/'.BX_UTF_PCRE_MODIFIER, $query, $arMatches)){
					$query = str_replace(array_filter(array_merge($arMatches[2], $arMatches[5])), '', $query);
				}
			}

			if($hash & self::META_HASH_HAS_FIXED_COUNT){
				$query = preg_replace('/^["](.*)["]$/'.BX_UTF_PCRE_MODIFIER, '$1', $query);
			}

			if($hash & self::META_HASH_HAS_STOP_WORDS){
				$query = preg_replace('/[+]([a-zA-Z'.TREG_CYR.'0-9-]+)/'.BX_UTF_PCRE_MODIFIER, ' $1', $query);
			}

			if($hash & self::META_HASH_HAS_COMPLEX){
				$query = preg_replace('/[(][!]*([^|]+)[|][!]*([^|]+)[)]/'.BX_UTF_PCRE_MODIFIER, ' $1', $query);
			}

			if($hash & self::META_HASH_HAS_FIXED_ORDER){
				$query = preg_replace('/[\[]([^\]]*)[\]]/'.BX_UTF_PCRE_MODIFIER, ' $1', $query);
			}

			if($hash & self::META_HASH_HAS_FIXED_FORMS){
				$query = preg_replace('/([\s|(\["]+|^)[!]([a-zA-Z'.TREG_CYR.'0-9-]+)/'.BX_UTF_PCRE_MODIFIER, ' $2', $query);
			}

			$query = trim(preg_replace('/\s+/'.BX_UTF_PCRE_MODIFIER, ' ', $query));
		}

		return $query;
	}

	public static function getStopWordsList(){
		if(!isset(self::$arStopWords)){
			\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
			self::$arStopWords = array_flip(explode(' ', \Bitrix\Main\Localization\Loc::getMessage('STOP_WORDS')));
		}

		return array_keys(self::$arStopWords);
	}

	public static function isStopWord($word, $lang = 'ru'){
		if(!isset(self::$arStopWords)){
			self::getStopWordsList();
		}

		return boolval(isset(self::$arStopWords[\ToLower($word, $lang)]));
	}

	public static function sentence2words($sentence){
		return strlen($sentence) ? (preg_match_all('/[a-zA-Z'.TREG_CYR.'0-9-]+/'.BX_UTF_PCRE_MODIFIER, $sentence, $arMatches) ? $arMatches[0] : array()) : array();
	}

	public static function stemming($sentence, $lang = 'ru'){
		if(self::isBxSearch()){
			if($stem = \stemming($sentence, $lang)){
				$arStems = array();
				foreach(array_keys($stem) as $word){
					$arStems[] = \ToLower($word, $lang);
				}
				return $arStems;
			}
		}

		return array();
	}

	public static function isLandingSearchIblock($IBLOCK_ID){
		return $IBLOCK_ID && isset(Cache::$arIBlocksInfo[$IBLOCK_ID]) && strpos(Cache::$arIBlocksInfo[$IBLOCK_ID]['CODE'], self::IBLOCK_CODE) !== false;
	}

	public static function getLandingSearchIblocksIDs($siteId = false, $indexElement = false){
		$arIBlockIDs = array();

		foreach(Cache::$arIBlocksInfo as $IBLOCK_ID => $arIBlock){
			if(strpos($arIBlock['CODE'], self::IBLOCK_CODE) !== false){
				if(!$siteId || in_array($siteId, $arIBlock['LID'])){
					if(!$indexElement || ($indexElement === $arIBlock['INDEX_ELEMENT'])){
						$arIBlockIDs[] = $IBLOCK_ID;
					}
				}
			}
		}

		return $arIBlockIDs;
	}
}
?>