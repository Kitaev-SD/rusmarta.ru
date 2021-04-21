<?php

class rbsDiscount {

    private $cashBasket = [];
    private $equalPosition = -1;
    private $faultPrice;
    private $arrOrder = [
	    'order_id' =>  null,
	    'amount' =>  null,
	    'discount' =>  null,
	    'basket' => []
    ];
    private $arrRecountSumm = [
       'priceAmount' => 0,
       'priceDiscount' => 0,
       'discount' => 0
    ];

	function __construct($order) {
		$this->arrOrder = $order;
	}

	private function priceTransform() {
        $basket = $this->arrOrder['basket'];
        $this->arrOrder['discount'] = $this->arrOrder['discount'] * 100;
        foreach ($this->arrOrder['basket'] as $key => $item) {
            $item['discount'] = $item['discount'] * 100;
            $item['priceAmount'] = $item['priceAmount'] * 100;
            $item['priceBase'] = $item['priceBase'] * 100;
            $item['priceDiscount'] = $item['priceDiscount'] * 100;
            $this->arrOrder['basket'][$key] = $item;
        }
	}

	private function transformNumber($value, $count = 2) {
		$count = $count ? $count : 2; 
        return round($value,$count);
	}

	private function transformBasePrice($item) {
        $result = ($this->arrOrder['discount'] * $item['percentDiscount'] / 100) / $item['count'];
        $result = $this->transformNumber( $item['priceBase'] - $result);
        if($result < 0) {  $result = 0; }
        return $result;
	}


	private function onPositionDiscountPercent($item) {
        $price = $item['priceAmount'];
        return $this->transformNumber((100 / $this->arrOrder['amount'] * $price), 4);
	}

	private function onPositionDiscount($item) {
        $result = $this->transformNumber( ($item['priceBase'] - $item['priceBaseDiscount']) * $item['count']);
        return $result;
	}

	private function onPositionDiscountPrice($item) {
        $result = $this->transformNumber($item['priceBaseDiscount'] * $item['count']);
        if( $result < 0 ) { $result = 0; }
        return $result;
	}


	private function findMaxCount() {
        $maxValue = max(array_column( $this->arrOrder['basket'], 'count' ));
        $result = array_search( $maxValue, array_column( $this->arrOrder['basket'] ,'count') );
        return $result;
	}

	private function splitPosition($mode, $indexSeparate) {
        $newIndex = count($this->arrOrder['basket']);
        $separatePosition = $this->arrOrder['basket'][$indexSeparate];

        if( $mode === 'onePosition' ) {
            $this->arrOrder['basket'][$indexSeparate]['count'] = $this->transformNumber( $separatePosition['count'] - 1,3 );
            array_push( $this->arrOrder['basket'], $separatePosition );
            $this->equalPosition = $newIndex;
            $this->arrOrder['basket'][$newIndex] = $separatePosition;
            $this->arrOrder['basket'][$newIndex]['count'] = 1;
        } else if( $mode === 'fractionPosition' ) {
            $needPositionPrice = $this->transformNumber( $separatePosition['priceDiscount'] + $this->faultPrice );
            $originalCount = $separatePosition['count'];
            $success = false;
            $vishPrice = $this->transformNumber( $needPositionPrice / $originalCount );

            $separObject = [];
            $separObject['needSumm'] = $needPositionPrice;
            $separObject['oldPositionCount'] =  $this->transformNumber($originalCount / 2, 3 );
            $separObject['newPositionCount'] = $this->transformNumber($originalCount - $separObject['oldPositionCount'],3);

               
            for ($toIndex = 0; $toIndex < 5; $toIndex++) {
                $separObject['oldPositionCount'] = $this->transformNumber( $separObject['oldPositionCount'] - 0.001, 3) ;
                $separObject['newPositionCount'] = $this->transformNumber( $separObject['newPositionCount'] + 0.001, 3 );
                $separObject['oldPositionPrice'] = $this->transformNumber( $separObject['oldPositionCount'] * $vishPrice );
                $separObject['newPositionPrice'] = $this->transformNumber( $separObject['newPositionCount'] * $vishPrice );
                $separObject['oldPositionVishPrice'] = $this->transformNumber( $separObject['oldPositionPrice'] / $separObject['oldPositionCount'] );
                $separObject['newPositionVishPrice'] = $this->transformNumber( $separObject['newPositionPrice'] / $separObject['newPositionCount'] );
                $separObject['recountSumm'] = $this->transformNumber( $separObject['oldPositionPrice'] + $separObject['newPositionPrice']);
                if($separObject['recountSumm'] == $separObject['needSumm']) {
                    $success = true;
                    break;
                }
            }
            if( !$success ) {
                $res = $this->transformNumber(($separObject['newPositionPrice'] - ($separObject['recountSumm']-$separObject['needSumm'])) / $separObject['newPositionCount']);
                $separObject['newPositionPrice'] = $this->transformNumber( $res * $separObject['newPositionCount'] );
                $separObject['recountSumm'] = $this->transformNumber( $separObject['oldPositionPrice']+$separObject['newPositionPrice'] );
                if( $separObject['recountSumm'] === $separObject['needSumm'] ) { $success = true;}
            }
            if( $success ) {
                $separatePosition['count'] = $separObject['oldPositionCount'];        
                $separatePosition['priceBaseDiscount'] = $separObject['oldPositionVishPrice'];
                $separatePosition['priceAmount'] = $this->transformNumber( $separatePosition['count'] * $separatePosition['priceBase'] );  
                $separatePosition['priceDiscount'] = $separObject['oldPositionPrice'];
                $separatePosition['discount'] = $this->transformNumber( $separatePosition['priceAmount'] - $separatePosition['priceDiscount'] );
                $this->arrOrder['basket'][$indexSeparate] = $separatePosition;

                array_push($this->arrOrder['basket'], $separatePosition);
                $newPosition = $this->arrOrder['basket'][$newIndex];
                $newPosition['count'] = $separObject['newPositionCount'];
                $newPosition['priceAmount'] = $this->transformNumber( $newPosition['count'] * $newPosition['priceBase'] );
                $newPosition['priceBaseDiscount'] = $separObject['newPositionVishPrice'];
                $newPosition['priceDiscount'] = $separObject['newPositionPrice'];  
                $newPosition['discount'] = $this->transformNumber( $newPosition['priceAmount'] - $newPosition['priceDiscount'] );
                $this->arrOrder['basket'][$newIndex] = $newPosition;
            } else {

            }

        }
	}

	private function transformBasket() {
        $onePositionFind = false;
        $positionSeparateIndex = -1;
        $basket = $this->arrOrder['basket'];
        $transformMode = 'none';
        $this->equalPosition = -1;

        if( count($basket) === 0 ) { return false; }
        
        foreach ($basket as $key => $position) {
            if($position['count'] > 1) {
                $positionSeparateIndex = $key; 
            }
            if ($position['count'] == 1) {
                $this->equalPosition = $key;
                $onePositionFind = true;
            }
        }
        
        if(!$onePositionFind && $positionSeparateIndex >= 0) {
            $transformMode = 'onePosition';
        } else if(!$onePositionFind) {
            $positionSeparateIndex = $this->findMaxCount();
            $transformMode = 'fractionPosition';
        }
        $this->splitPosition( $transformMode,$positionSeparateIndex );
        return $transformMode;
	}



	private function generateRecieptAmount() {
        $summ = 0;
        foreach ($this->arrOrder['basket'] as $key => $item) {
        	$summ += $this->transformNumber($item['priceBase'] * $item['count']);
        }
        $this->arrOrder['amount'] = $this->transformNumber($summ);   
        if($this->arrOrder['amount'] < $this->arrOrder['discount']) {
            $this->arrOrder['discount'] = $this->arrOrder['amount'];
        }
        
	}

	private function generatePositionsDiscount() {
        $difference = $this->arrOrder['amount'] - $this->arrOrder['discount'];
        if( $difference > 1 ) {
        	foreach ($this->arrOrder['basket'] as $key => $item) {
                $item['priceAmount'] = $this->transformNumber( $item['priceBase'] * $item['count'] );
                $item['percentDiscount'] = $this->onPositionDiscountPercent( $item );
                $item['priceBaseDiscount'] = $this->transformBasePrice( $item );
                $item['discount'] = $this->onPositionDiscount( $item );
                $item['priceDiscount'] = $this->onPositionDiscountPrice( $item );

                $this->arrOrder['basket'][$key] = $item;
            }
        } else {
            foreach ($this->arrOrder['basket'] as $key => $item) {
                $item['priceAmount'] = $this->transformNumber( $item['priceBase'] * $item['count'] );
                $item['percentDiscount'] = 0;
                $item['priceBaseDiscount'] = 0;
                $item['discount'] = $item['priceAmount'];
                $item['priceDiscount'] = $item['priceAmount'] - $item['discount'];

                $this->arrOrder['basket'][$key] = $item;
            }
        }
	}

    private function equalizePositionDiscount() {
    	
        if( $this->equalPosition < 0 ) { return false; }
        $equalPosition = $this->arrOrder['basket'][$this->equalPosition];
        $baseSumm = $this->arrOrder['amount'] - $this->arrOrder['discount'];
        $recountSumm = $this->arrRecountSumm['priceDiscount'];
        $remain = 0;

        if( $baseSumm != $recountSumm ) {
            $remain = $this->transformNumber($baseSumm - $recountSumm);
            $this->arrOrder['basket'][$this->equalPosition]['priceBaseDiscount'] = $this->transformNumber($equalPosition['priceBaseDiscount'] + $remain);
            $this->arrOrder['basket'][$this->equalPosition]['priceDiscount'] = $equalPosition['priceBaseDiscount'];
            $this->arrOrder['basket'][$this->equalPosition]['discount'] = $this->transformNumber($equalPosition['discount'] - $remain);
            $this->arrOrder['basket'][$this->equalPosition]['priceDiscount'] = $this->arrOrder['basket'][$this->equalPosition]['priceBaseDiscount'];
        }
    }



	private function provideProductsToOrder() {
		$this->arrOrder['basket'] = $this->cashBasket;
	}

    private function finalCheck() {
        $baseAmount = $this->transformNumber( $this->arrOrder['amount'] - $this->arrOrder['discount'] );
        $finalAmount = $this->arrRecountSumm['priceDiscount'];
        
        $this->faultPrice =  $this->transformNumber($baseAmount - $finalAmount);
        if($this->faultPrice != 0) {
            $transformMode = $this->transformBasket();
            if( $transformMode !== 'fractionPosition' ) {
                $this->generatePositionsDiscount();
                $this->recountRecieptAmount();
                $this->equalizePositionDiscount();
            }
            $this->recountRecieptAmount();
        }       
    }

	public function addProduct($product) {
        $product['priceAmount'] = $product['count'] * $product['priceBase'];
        $this->cashBasket[] = $product; 
	}



	public function recountRecieptAmount() {
        $summ = [
           'priceAmount' =>  0,
           'priceDiscount' =>  0,
           'discount:' => 0  
        ];
        foreach ($this->arrOrder['basket'] as $key => $item) {
            $summ['priceAmount'] += $item['priceAmount'];
            $summ['priceDiscount'] += $item['priceBaseDiscount'] * $item['count'];
            $summ['discount'] += $item['discount'];
        }
        $this->arrRecountSumm['priceAmount'] = $this->transformNumber( $summ['priceAmount'] );
        $this->arrRecountSumm['priceDiscount'] = $this->transformNumber( $summ['priceDiscount'] );
        $this->arrRecountSumm['discount'] = $this->transformNumber( $summ['discount'] );

	}

	public function getOrder() {
		return $this->arrOrder;
	}


	public function getProducts() {
		return $this->cashBasket;
	}
	public function getBasketResult() {
		$order;
		foreach ($this->arrOrder['basket'] as $key => $item) {
			$order[$key] = [
	            'positionId' => $key+1,
                'name' => $item['name'],
                'quantity' => array(
                    'value' => $item['count'],
                    'measure' => $item['arrGate']['quantity']['measure'],
                ),
                'itemAmount' => $this->transformNumber($item['priceDiscount'] * 100,0),
                'itemCode' => $item['id'],
                'itemPrice' => $this->transformNumber($item['priceBaseDiscount'] * 100,0),
	            'tax' => array(
	                'taxType' => $item['arrGate']['tax']['taxType'],
	            ),
	        ];

            if(isset($item['itemAttributes'])) {
                $order[$key]['itemAttributes'] = $item['itemAttributes'];
            }
		}
		return $order;

	}
	public function getRecountSumm() {
		return $this->arrRecountSumm;
	}

	public function setOrderDiscount($value) {
		$this->arrOrder['discount'] = $value;
	}



	public function updateOrder() {
		$this->provideProductsToOrder();
		$this->generateRecieptAmount();
		$this->generatePositionsDiscount();
        $this->recountRecieptAmount();
        $this->finalCheck();
	}

	public function test($message = '') {
		echo "<pre>";
		print_r($this->arrOrder);
		print_r($this->arrRecountSumm);
		echo "</pre>";
	}

}

?>