<?php

namespace CarrotQuest\Marketing;

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\EventManager;
use Bitrix\Sale;
use Bitrix\Sale\Compatible\BasketCompatibility;
use Bitrix\Sale\Compatible\OrderCompatibility;
use Bitrix\Catalog;
use Bitrix\Main\EventResult;
use Bitrix\Main\Event;


/*
NOTE from user: please, tell your developers, they might be interested, I send "true" as second param
 when CCatalogProduct::GetByIDEx is called, to get all values of multiple prop,
other way it just returns the last one from the list

NOTE from dev: Not sure if it suits us. We actually trying to access and pass on info on current product
*/

class CarrotEventsBasket extends CarrotEvents
{

	/**
	 * Users visited there cart
	 */
	public static function VisitedBasket()
	{
		if (!\CModule::IncludeModule('sale') or !\CModule::IncludeModule('catalog') or !self::WatchSite()) return;
		$userId = $_COOKIE['carrotquest_uid'];
		if (defined('CARROTQUEST_API_KEY') && defined('CARROTQUEST_API_SECRET') && '' != $userId) {
			$dbBasketItems = \CSaleBasket::GetList(
				array(
					'NAME' => 'ASC',
					'ID' => 'ASC',
				),
				array(
					'FUSER_ID' => \CSaleBasket::GetBasketUserID(),
					'LID' => SITE_ID,
					'ORDER_ID' => 'NULL',
				),
				false,
				false,
				array()
			);

			$opArrItem[] = array(
				'op' => 'delete',
				'key' => '$cart_items',
				'value' => "0",
			);
			$i = 0;

			while ($bItem = $dbBasketItems->Fetch()) {
				$itemPic = "";
				$prodItem = \CCatalogProduct::GetByIDEx($bItem['PRODUCT_ID']);

				if ($prodItem['DETAIL_PICTURE'] > 0) {
					if (trim(\CFile::GetPath($prodItem['DETAIL_PICTURE'])) != "") {
						$itemPic = self::getImageUrl(\CFile::GetPath($prodItem['DETAIL_PICTURE']));
					}
				} else if ($prodItem['PREVIEW_PICTURE'] > 0) {
					if (trim(\CFile::GetPath($prodItem['PREVIEW_PICTURE'])) != "") {
						$itemPic = self::getImageUrl(\CFile::GetPath($prodItem['PREVIEW_PICTURE']));
					}
				}

				if (strlen($itemPic) <= 0) {
					if (count($prodItem['PROPERTIES'])) {
						if (count($prodItem['PROPERTIES']['CML2_LINK'])) {
							$cartItem = \CCatalogProduct::GetByIDEx($prodItem['PROPERTIES']['CML2_LINK']['VALUE']);
							if ($cartItem['DETAIL_PICTURE'] > 0) {
								if (trim(\CFile::GetPath($cartItem['DETAIL_PICTURE'])) != "") {
									$itemPic = self::getImageUrl(\CFile::GetPath($cartItem['DETAIL_PICTURE']));
								}
							} else if ($cartItem['PREVIEW_PICTURE'] > 0) {
								if (trim(\CFile::GetPath($cartItem['PREVIEW_PICTURE'])) != "") {
									$itemPic = self::getImageUrl(\CFile::GetPath($cartItem['PREVIEW_PICTURE']));
								}
							}
						}
					}
				}

				$strItem = $bItem["NAME"];
				$arrItem['$name'][] = $bItem["NAME"];
				$arrItem['$amount'][] = round(floatval($bItem["PRICE"])) * $bItem["QUANTITY"];
				$arrItem['$url'][] = self::getImageUrl($bItem["DETAIL_PAGE_URL"]);
				$arrItem['$img'][] = strlen($itemPic) > 0 ? $itemPic : '<Нет изображения>';

				if (strlen($strItem) > 0) {
					$opArrItem[] = array(
						"op" => "append",
						"key" => '$cart_items',
						"value" => $strItem
					);
				}
				$i++;

			}
			$cart = $arrItem;
			if (count($cart['$name']) == 0) {
				$cart = array();
			}

			CarrotEvents::SendEvent($userId, '$cart_viewed', $cart);
			CarrotEvents::SendOperations($userId, $opArrItem);
			self::BusketSumm($userId);

		}
	}

	/**
	 * Users added item to there cart (wrap-up for the current cart total and added item handlers)
	 * D7 event
	 *
	 * @param Event $event
	 * @return EventResult
	 */
	public static function newOnBasketAdd(Event $event) {
		$item = $event->getParameter('ENTITY');
		$oldValues = $event->getParameter('VALUES');

		$needsCheck = array_key_exists("ID", $oldValues) || array_key_exists("QUANTITY", $oldValues);
		$oldItemId = $oldValues["ID"];
		$oldItemQuantity = $oldValues["QUANTITY"];

		if (self::WatchLegacyEvents() || !$needsCheck || !self::WatchSite($item->getField('LID'))) { return new EventResult(EventResult::SUCCESS); }
		try {
			$carrotquest_uid = $_COOKIE['carrotquest_uid'];
			if (defined('CARROTQUEST_API_KEY')
				&& defined('CARROTQUEST_API_SECRET')
				&& isset($item)
				&& '' !== $carrotquest_uid
				&& (!isset($oldItemId) || isset($oldItemQuantity) && intval($oldItemQuantity) > $item->getQuantity())
			) {
				self::AddItem($item->getFields()->getValues(), $carrotquest_uid);
				self::BusketSumm($carrotquest_uid, $item->getFields()->getValues());
			}
		} catch (\Exception $e) {
			self::WriteLog('Error', $e->getMessage());
		}

		return new EventResult(EventResult::SUCCESS);
	}

	/**
	 * Users added item to there cart (wrap-up for the current cart total and added item handlers)
	 *
	 * @param string|integer $id
	 * @param array $item
	 * @return EventResult
	 */
	public static function onBasketAdd($id, $item)
	{
		if (!self::WatchLegacyEvents()) { return new EventResult(EventResult::SUCCESS); }
		try {
			$carrotquest_uid = $_COOKIE['carrotquest_uid'];
			if (defined('CARROTQUEST_API_KEY') && defined('CARROTQUEST_API_SECRET')
				&& $item != null
				&& '' != $carrotquest_uid
				&& self::WatchSite($item['LID'])
			) {
				self::AddItem($item, $carrotquest_uid);
				self::BusketSumm($carrotquest_uid, $item);
			}
		} catch (\Exception $e) {
			self::WriteLog('Error', $e->getMessage());
		}

		return new EventResult(EventResult::SUCCESS);
	}

	/**
	 * Iem added to the cart (item info handler)
	 *
	 * @param array $item
	 * @param string|integer $carrotquest_uid
	 */
	private static function AddItem($item, $carrotquest_uid)
	{
		if (!\CModule::IncludeModule("catalog")) return;
		$HTTP = $_SERVER['HTTPS'] ? "https://" : "http://";
		$arrItem = array(
			'$name' => $item["NAME"],
			'$amount' => round(floatval($item["PRICE"])),
			'$url' => $HTTP . $_SERVER['SERVER_NAME'] . $item["DETAIL_PAGE_URL"]
		);

		$prodItem = \CCatalogProduct::GetByIDEx($item["PRODUCT_ID"]);
		if ($prodItem["DETAIL_PICTURE"] > 0) {
			if (trim(\CFile::GetPath($prodItem["DETAIL_PICTURE"])) !== "") {
				$itemPic = self::getImageUrl(\CFile::GetPath($prodItem["DETAIL_PICTURE"]));
			}
		} else if ($prodItem["PREVIEW_PICTURE"] > 0) {
			if (trim(\CFile::GetPath($prodItem["PREVIEW_PICTURE"])) != "") {
				$itemPic = self::getImageUrl(\CFile::GetPath($prodItem["PREVIEW_PICTURE"]));
			}
		}

		if (strlen($itemPic) <= 0) {
			if (count($prodItem["PROPERTIES"])) {
				if (count($prodItem["PROPERTIES"]["CML2_LINK"])) {
					$cartItem = \CCatalogProduct::GetByIDEx($prodItem["PROPERTIES"]["CML2_LINK"]["VALUE"]);
					if ($cartItem["DETAIL_PICTURE"] > 0) {
						if (trim(\CFile::GetPath($cartItem["DETAIL_PICTURE"])) != "") {
							$itemPic = self::getImageUrl(\CFile::GetPath($cartItem["DETAIL_PICTURE"]));
						}
					} else if ($cartItem["PREVIEW_PICTURE"] > 0) {
						if (trim(\CFile::GetPath($cartItem["PREVIEW_PICTURE"])) != "") {
							$itemPic = self::getImageUrl(\CFile::GetPath($cartItem["PREVIEW_PICTURE"]));
						}
					}
				}
			}
		}
		if (strlen($itemPic) > 0) {
			$arrItem['$img'] = $itemPic;
		}
		CarrotEvents::SendEvent($carrotquest_uid, '$cart_added', $arrItem);
	}

	/**
	 * Cart total handler
	 *
	 * @param string|integer $carrotquest_uid
	 * @param array|null $item
	 */
	private static function BusketSumm($carrotquest_uid, $item = null)
	{
		if (!\CModule::IncludeModule('sale')) return;
		$dbBasketItems = \CSaleBasket::GetList(
			array(
				'NAME' => 'ASC',
				'ID' => 'ASC',
			),
			array(
				'FUSER_ID' => \CSaleBasket::GetBasketUserID(),
				'LID' => SITE_ID,
				'ORDER_ID' => 'NULL',
			),
			false,
			false,
			array()
		);

		$basketSumm = 0;

		while ($bItem = $dbBasketItems->Fetch()) {
			if ($item == null || $item['PRODUCT_ID'] != $bItem['PRODUCT_ID']) {
				$basketSumm += (float)$bItem['PRICE'] * (float)$bItem['QUANTITY'];
			}
		}
		if ($item != null) {
			$basketSumm += (float)$item['PRICE'] * (float)$item['QUANTITY'];
		}
		if ($basketSumm) {
			$arrItem[] = array(
				'op' => 'update_or_create',
				'key' => '$cart_amount',
				'value' => round($basketSumm),
			);
		} else {
			$arrItem[] = array(
				'op' => 'delete',
				'key' => '$cart_amount',
				'value' => 0,
			);
		}

		CarrotEvents::SendOperations($carrotquest_uid, $arrItem);
	}

	/**
	 * Product viewed (D7 core's event on repeated views)
	 *
	 * @param array $arFields
	 */
	public static function onProductViewedUpdate($arFields)
	{
		if (!self::WatchLegacyEvents()) {
			try {
				$ID = intval($arFields['ID']);
				$viewedProd = \Bitrix\Catalog\CatalogViewedProductTable::getRowById($ID);
			} catch (\Exception $e) {
				self::WriteLog("Error", $e->getMessage());
			}
			if (isset($viewedProd)
				&& array_key_exists('PRODUCT_ID', $viewedProd)) {
				self::catalogOnBeforeViewed($viewedProd);
			}
		}
	}

	/**
	 * Product viewed (D7 core's event on first view)
	 *
	 * @param array $arResult
	 */
	public static function onProductViewedAdd($viewedProd)
	{
		if (!self::WatchLegacyEvents()) {
			self::catalogOnBeforeViewed($viewedProd);
		}

	}

	/**
	 * Product viewed (legacy event)
	 *
	 * @param array $arResult
	 */
	public static function onBeforeViewedAdd($viewedProd)
	{
		if (self::WatchLegacyEvents()) {
			self::catalogOnBeforeViewed($viewedProd);
		}
	}

	/**
	 * Product viewed (common handler for all view events, runs only once per actual view)
	 *
	 * @param array $arResult
	 */
	private static function catalogOnBeforeViewed($arResult)
	{
		if (!\CModule::IncludeModule('catalog') || !self::WatchSite($arResult['LID'])) return;
		$HTTP = $_SERVER['HTTPS'] ? 'https://' : 'http://';
		$userId = $_COOKIE['carrotquest_uid'];

		$arProduct = \CCatalogProduct::GetByIDEx($arResult['PRODUCT_ID']);
		if ($arProduct) {
			$arrItem = array(
				'$name' => $arProduct['NAME'],
				'$url' => $HTTP . $_SERVER['SERVER_NAME'] . $arProduct['DETAIL_PAGE_URL'],
			);


			if ($arProduct['DETAIL_PICTURE'] > 0) {
				$img = \CFile::GetPath($arProduct['DETAIL_PICTURE']);
			} else if ($arProduct['PREVIEW_PICTURE'] > 0) {
				$img = \CFile::GetPath($arProduct['PREVIEW_PICTURE']);
			}
			if (strlen($img) <= 0) {
				if (count($arProduct['PROPERTIES'])) {
					if (count($arProduct['PROPERTIES']['CML2_LINK'])) {
						$prodItem = \CCatalogProduct::GetByIDEx($arProduct['PROPERTIES']['CML2_LINK']['VALUE']);
						if ($prodItem['DETAIL_PICTURE'] > 0) {
							if (trim(\CFile::GetPath($prodItem['DETAIL_PICTURE'])) != '') {
								$img = self::getImageUrl(\CFile::GetPath($prodItem['DETAIL_PICTURE']));
							}
						} else if ($arProduct['PREVIEW_PICTURE'] > 0) {
							if (trim(\CFile::GetPath($arProduct['PREVIEW_PICTURE'])) != '') {
								$img = self::getImageUrl(\CFile::GetPath($arProduct['PREVIEW_PICTURE']));
							}
						}
					}
				}
			}
			if (trim($img) != '')
				$arrItem['$img'] = self::getImageUrl($img);
			CarrotEvents::SendEvent($userId, '$product_viewed', $arrItem);

			$opArrItems[] = array(
				'op' => 'union'
			, 'key' => '$viewed_products'
			, 'value' => $arProduct['NAME']
			);
			CarrotEvents::SendOperations($userId, $opArrItems);
		}
	}

	/**
	 * Refines image link from given url
	 *
	 * @param string $url
	 * @return string
	 */
	private static function getImageUrl($url)
	{
		if (self::startsWith($url, "https://") or self::startsWith($url, "http://")) {
			return $url;
		} else {
			$HTTP = $_SERVER['HTTPS'] ? "https://" : "http://";
			return $HTTP . $_SERVER['SERVER_NAME'] . $url;
		}
	}

	/**
	 * Check if string starts with given substring
	 *
	 * @param $haystack
	 * @param $needle
	 * @return bool
	 */
	private static function startsWith($haystack, $needle)
	{
		$length = strlen($needle);
		return (substr($haystack, 0, $length) === $needle);
	}


}
