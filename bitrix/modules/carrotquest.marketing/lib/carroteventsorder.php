<?php

namespace CarrotQuest\Marketing;

use Bitrix\Catalog;
use Bitrix\Main\Application;
use Bitrix\Main\Event;
use Bitrix\Main\EventManager;
use Bitrix\Main\EventResult;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\UserTable;
use Bitrix\Sale;
use Bitrix\Sale\Compatible\BasketCompatibility;
use Bitrix\Sale\Compatible\EventCompatibility;
use Bitrix\Sale\Compatible\OrderCompatibility;


class CarrotEventsOrder extends CarrotEvents
{

	/**
	 * User placed an order
	 * D7 event
	 *
	 * @param Event|array|null $event
	 * @return EventResult
	 */
	public static function newOnOrderAdd( Event $event )
	{
		$order = $event->getParameter( 'ENTITY' );
		$isNew = $event->getParameter( 'IS_NEW' );

		if ( self::WatchLegacyEvents( true ) || !self::WatchSite( $order->getField( 'LID' ) ) ) {
			return new EventResult( EventResult::SUCCESS );
		}

		try {
			if ( defined( 'CARROTQUEST_API_KEY' ) && defined( 'CARROTQUEST_API_SECRET' ) && isset( $order ) && $isNew ) {
				$userId       = $order->getUserId();
				$byUserId     = true;
				$statusesList = Sale\OrderStatus::getAllStatusesNames();
				$status_id    = $order->getField( 'STATUS_ID' );
				if ( isset( $statusesList[ $status_id ] ) ) {
					$arrItem[] = array(
						'op'    => 'update_or_create',
						'key'   => '$last_order_status',
						'value' => $statusesList[ $status_id ],
					);
				}

				// Get data on user that placed an order
				$user = UserTable::getById( $userId )->fetch();

				// Get user phone from order (if we know in which fields to look for it)
				if ( Option::get( self::$MODULE_ID, 'phone_prop' ) ) {
					$phone_codes_str = Option::get( self::$MODULE_ID, 'phone_prop' );
					$phone_codes     = explode( ',', $phone_codes_str );
					$count           = count( $phone_codes );

					$i = -1;
					do {
						$i++;
						if ( isset( $_POST[ trim( $phone_codes[ $i ] ) ] ) ) {
							$arrItem[] = array(
								'op'    => 'update_or_create',
								'key'   => '$phone',
								'value' => $_POST[ trim( $phone_codes[ $i ] ) ],
							);
						}
					} while ( $i < $count - 1 && !isset( $_POST[ trim( $phone_codes[ $i ] ) ] ) );
				}

				if ( $user ) {
					$prop_name = $user['LAST_NAME'] ?? '';
					$prop_name .= ' ' . ( $user['NAME'] ?? '' );
					$prop_name = trim( $prop_name ) . ' ' . ( $user['SECOND_NAME'] ?? '' );

					// user name
					$arrItem[] = array(
						'op'    => 'update_or_create',
						'key'   => '$name',
						'value' => trim( $prop_name ),
					);


					if ( isset( $user['EMAIL'] ) ) {
						// user email
						$arrItem[] = array(
							'op'    => 'update_or_create',
							'key'   => '$email',
							'value' => $user['EMAIL'],
						);
					}
				}

				// Empty the cart amount prop in cq. It's 100% saved for user by carrotquest_uid, and other props and events might create new anon and this will go to vain.
				$arrItemCQUID = array(
					array(
						'op'    => 'delete',
						'key'   => '$cart_amount',
						'value' => 0,
					),
					array(
						'op'    => 'delete',
						'key'   => '$cart_items',
						'value' => '',
					),
				);

				$arrItem[] = array(
					'op'    => 'add',
					'key'   => '$revenue',
					'value' => round( floatval( $order->getPrice() ) ),
				);

				$arrItem[]       = array(
					'op'    => 'update_or_create',
					'key'   => '$last_payment',
					'value' => round( floatval( $order->getPrice() ) ),
				);
				$carrotquest_uid = CarrotEvents::GetCarrotquestUID( $userId );

				CarrotEvents::SendOperations( $carrotquest_uid, $arrItemCQUID );
				CarrotEvents::SendOperations( $userId, $arrItem, $byUserId );
				CarrotEvents::SendEvent( $userId, '$order_completed', array(
					'$order_id'     => $order->getId(),
					'$order_amount' => round( floatval( $order->getPrice() ) ),
				), $byUserId );
			}
		} catch ( \Exception $e ) {
			self::WriteLog( 'Error', $e->getMessage() );
		}

		return new EventResult( EventResult::SUCCESS ); // says that it is OK to continue CMS functions
	}

	/**
	 * User placed an order
	 *
	 * @param string|integer $id
	 * @param Sale\Order|array|null $order
	 * @return EventResult
	 */
	public static function onOrderAdd( $id, $order )
	{
		if ( !self::WatchLegacyEvents( true ) || !self::WatchSite( $order['LID'] ) ) {
			return new EventResult( EventResult::SUCCESS );
		}

		try {
			if ( defined( 'CARROTQUEST_API_KEY' ) && defined( 'CARROTQUEST_API_SECRET' ) && $order != null ) {
				$userId   = $order['USER_ID'];
				$byUserId = true;

				$status = \CSaleStatus::GetByID( $order['STATUS_ID'] );
				if ( isset( $status['NAME'] ) && $status['NAME'] ) {
					$arrItem[] = array(
						'op'    => 'update_or_create',
						'key'   => '$last_order_status',
						'value' => $status['NAME'],
					);
				}

				// Get data on user that placed an order
				$dbOrdUser = \CUser::GetByID( $userId );
				$ord_user  = $dbOrdUser->Fetch();

				// Get user phone from order (if we know in which fields to look for it)
				if ( Option::get( self::$MODULE_ID, 'phone_prop' ) ) {
					$phone_codes_str = Option::get( self::$MODULE_ID, 'phone_prop' );
					$phone_codes     = explode( ',', $phone_codes_str );
					$count           = count( $phone_codes );

					$i = -1;
					do {
						$i++;
						if ( isset( $_POST[ trim( $phone_codes[ $i ] ) ] ) ) {
							$arrItem[] = array(
								'op'    => 'update_or_create',
								'key'   => '$phone',
								'value' => $_POST[ trim( $phone_codes[ $i ] ) ],
							);
						}
					} while ( $i < $count - 1 && !isset( $_POST[ trim( $phone_codes[ $i ] ) ] ) );
				}

				if ( $ord_user ) {
					$prop_name = $user['LAST_NAME'] ?? '';
					$prop_name .= ' ' . ( $user['NAME'] ?? '' );
					$prop_name = trim( $prop_name ) . ' ' . ( $user['SECOND_NAME'] ?? '' );

					// user name
					$arrItem[] = array(
						'op'    => 'update_or_create',
						'key'   => '$name',
						'value' => trim( $prop_name ),
					);

					if ( isset( $ord_user['EMAIL'] ) ) {
						// user email
						$arrItem[] = array(
							'op'    => 'update_or_create',
							'key'   => '$email',
							'value' => $ord_user['EMAIL'],
						);
					}
				}

				// Empty the cart amount prop in cq. It's 100% saved for user by carrotquest_uid, and other props and events might create new anon and this will go to vain.
				$arrItemCQUID = array(
					array(
						'op'    => 'delete',
						'key'   => '$cart_amount',
						'value' => 0,
					),
					array(
						'op'    => 'delete',
						'key'   => '$cart_items',
						'value' => '',
					),
				);

				$arrItem[] = array(
					'op'    => 'add',
					'key'   => '$revenue',
					'value' => round( floatval( $order['PRICE'] ) ),
				);

				$arrItem[] = array(
					'op'    => 'update_or_create',
					'key'   => '$last_payment',
					'value' => round( floatval( $order['PRICE'] ) ),
				);

				$carrotquest_uid = CarrotEvents::GetCarrotquestUID( $userId );

				CarrotEvents::SendOperations( $carrotquest_uid, $arrItemCQUID );
				CarrotEvents::SendOperations( $userId, $arrItem, $byUserId );
				CarrotEvents::SendEvent( $userId, '$order_completed', array(
					'$order_id'     => $id,
					'$order_amount' => round( floatval( $order['PRICE'] ) ),
				), $byUserId );
			}
		} catch ( \Exception $e ) {
			self::WriteLog( 'Error', $e->getMessage() );
		}

		return new EventResult( EventResult::SUCCESS );
	}

	/**
	 * Order status changing
	 *
	 * @param Sale\Order|array|null $order
	 * @param string|integer $value New order status ID
	 * @param string|integer $oldValue Old order status ID
	 * @return EventResult
	 */
	public static function onSaleStatusOrderChange( $order, $value, $oldValue )
	{
		try {
			if ( defined( "CARROTQUEST_API_KEY" ) && defined( "CARROTQUEST_API_SECRET" )
				&& $order != null && $order instanceof Sale\Order
				&& self::WatchSite( $order->getField( 'LID' ) )
			) {
				// send info to customer linked to order
				$userId   = $order->getUserId();
				$arFilter = array(
					"USER_ID" => $userId,
				);
				// list of orders, sorted desc by there date of creation
				$rsSales      = \CSaleOrder::GetList( array( "DATE_INSERT" => "DESC" ), $arFilter );
				$rsSalesFirst = $rsSales->Fetch();

				// if it is currently the last order changing then send data to cq
				if ( $rsSalesFirst && $rsSalesFirst["ID"] == $order->getId() ) {
					$status    = \CSaleStatus::GetByID( $value );
					$arrItem[] = array(
						"op"    => "update_or_create",
						"key"   => '$last_order_status',
						"value" => $status["NAME"] // new status name
					);
					CarrotEvents::SendOperations( $userId, $arrItem, true );
				}

				if ( strtoupper( $value ) == "F" ) {
					CarrotEvents::SendEvent( $userId, '$order_closed', array( '$order_id' => $order->getId() ), true );
				}
			}
		} catch ( \Exception $e ) {
			self::WriteLog( "Error", $e->getMessage() );
		}

		return new EventResult( EventResult::SUCCESS ); // says that it is OK to continue CMS functions
	}

	/**
	 * Sends data when order or order cancelation is canceled
	 * D7 event
	 *
	 * @param Event $event
	 * @return EventResult
	 */
	public static function newOnSaleCancelOrder( Event $event )
	{
		$order = $event->getParameter( 'ENTITY' );

		if ( self::WatchLegacyEvents( true ) || !self::WatchSite( $order->getField( 'LID' ) ) ) {
			return new EventResult( EventResult::SUCCESS );
		}

		try {
			if ( defined( 'CARROTQUEST_API_KEY' ) && defined( 'CARROTQUEST_API_SECRET' ) && isset( $order ) ) {
				$userId   = $order->getUserId();
				$arFilter = array(
					'USER_ID' => $userId,
				);
				// list of orders, sorted desc by creation date
				$rsSales      = Sale\Order::getList( array( "order" => array( 'DATE_INSERT' => 'DESC' ), "filter" => $arFilter ) );
				$rsSalesFirst = $rsSales->fetch();
				// if it is the last order changing then send data to Carrot
				if ( isset( $rsSalesFirst ) && $rsSalesFirst['ID'] === $order->getId() ) {
					$statusesList = Sale\OrderStatus::getAllStatusesNames();
					$status_id    = $order->getField( 'STATUS_ID' );
					$arrItem[]    = array(
						'op'    => 'update_or_create',
						'key'   => '$last_order_status',
						'value' => $order->isCanceled() ? 'Заказ отменён' : $statusesList[ $status_id ]// set status to canceled or to active (depending on action)
					);
					CarrotEvents::SendOperations( $userId, $arrItem, true );
				}

				if ( $order->isCanceled() ) {
					$eArrItem = array(
						'$order_id' => $order->getId(),
					);

					$reasonCanceled = $order->getField( 'REASON_CANCELED' );
					if ( isset( $reasonCanceled ) && $reasonCanceled ) {
						$eArrItem['$comment'] = $reasonCanceled;
					}
					CarrotEvents::SendEvent( $userId, '$order_cancelled', $eArrItem, true );
				}
			}
		} catch ( \Exception $e ) {
			self::WriteLog( 'Error', $e->getMessage() );
		}

		return new EventResult( EventResult::SUCCESS );
	}


	/**
	 * Sends data when order or order cancelation is canceled
	 *
	 * @param string|integer $id
	 * @param string|bool $cancel
	 * @param string $description
	 * @return EventResult
	 */
	public static function onSaleCancelOrder( $id, $cancel, $description )
	{
		if ( !self::WatchLegacyEvents( true ) ) {
			return new EventResult( EventResult::SUCCESS );
		}

		try {
			$order = \CSaleOrder::GetByID( $id );

			if ( defined( 'CARROTQUEST_API_KEY' )
				&& defined( 'CARROTQUEST_API_SECRET' )
				&& $order != null && count( $order ) > 0
				&& self::WatchSite( $order['LID'] )
			) {
				$userId   = $order['USER_ID'];
				$arFilter = array(
					'USER_ID' => $userId,
				);
				// list of orders, sorted desc by creation date
				$rsSales      = \CSaleOrder::GetList( array( 'DATE_INSERT' => 'DESC' ), $arFilter );
				$rsSalesFirst = $rsSales->Fetch();
				// if it is last order changing then send data to Carrot
				if ( $rsSalesFirst && $rsSalesFirst['ID'] == $order['ID'] ) {
					$status    = \CSaleStatus::GetByID( $order['STATUS_ID'] );
					$arrItem[] = array(
						'op'    => 'update_or_create',
						'key'   => '$last_order_status',
						'value' => $cancel == 'Y' ? 'Заказ отменён' : $status['NAME'] // set status to canceled or to active (depending on action)
					);
					CarrotEvents::SendOperations( $userId, $arrItem, true );
				}

				if ( $cancel == 'Y' ) {
					$eArrItem = array(
						'$order_id' => $id,
					);

					if ( isset( $order['ORDER_CANCEL_DESCRIPTION'] ) && $order['ORDER_CANCEL_DESCRIPTION'] ) {
						$eArrItem['$comment'] = $order['ORDER_CANCEL_DESCRIPTION'];
					}
					CarrotEvents::SendEvent( $userId, '$order_cancelled', $eArrItem, true );
				}
			}
		} catch ( \Exception $e ) {
			self::WriteLog( 'Error', $e->getMessage() );
		}

		return new EventResult( EventResult::SUCCESS );
	}

	/**
	 * Sends data when order is set to paid
	 * D7 event
	 *
	 * @param Event $event
	 * @return EventResult
	 */
	public static function newOnSaleOrderPaid( Event $event )
	{
		$order = $event->getParameter( 'ENTITY' );

		if ( self::WatchLegacyEvents( true ) || !self::WatchSite( $order->getField( 'LID' ) ) ) {
			return new EventResult( EventResult::SUCCESS );
		}

		try {
			if ( defined( 'CARROTQUEST_API_KEY' ) && defined( 'CARROTQUEST_API_SECRET' ) && isset( $order ) ) {
				$userId = $order->getUserId();
				CarrotEvents::SendEvent( $userId, $order->isPaid() ? '$order_paid' : '$order_refunded', array( '$order_id' => $order->getId() ), true );

			}
		} catch ( \Exception $e ) {
			self::WriteLog( 'Error', $e->getMessage() );
		}

		return new EventResult( EventResult::SUCCESS );
	}

	/**
	 * Sends data when order is set to paid
	 *
	 * @param string|integer $id
	 * @param string|bool $value
	 * @return EventResult
	 */
	public static function onSalePayOrder( $id, $value )
	{
		if ( !self::WatchLegacyEvents( true ) ) {
			return new EventResult( EventResult::SUCCESS );
		}

		try {
			$order = \CSaleOrder::GetByID( $id );
			if ( defined( 'CARROTQUEST_API_KEY' )
				&& defined( 'CARROTQUEST_API_SECRET' )
				&& $order != null && count( $order ) > 0
				&& self::WatchSite( $order['LID'] )
			) {
				$userId   = $order['USER_ID'];
				$arFilter = array(
					'USER_ID' => $userId,
				);
				CarrotEvents::SendEvent( $userId, $value == 'Y' ? '$order_paid' : '$order_refunded', array( '$order_id' => $id ), true );

			}
		} catch ( \Exception $e ) {
			self::WriteLog( 'Error', $e->getMessage() );
		}

		return new EventResult( EventResult::SUCCESS );
	}

}
