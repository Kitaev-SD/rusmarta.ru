<?php 

namespace CiFrame\BeruAPI;

use CiFrame\DB\AbstractModel;
use CiFrame\DB\Connection;
use CiFrame\DB\DBWorker;

class BeruAPI {

    private $host_api = 'https://api.partner.market.yandex.ru/v2';
    public $client_id;
    public $campaign_id;
    private $token;
    public $table_label = 'beru';
    public $label_another_service;

    public function getTableNameLink()
    {
        return 'ci_' . $this->table_label . '_' . $this->label_another_service . '_link';
    }

    public function getTableNameOrder()
    {
        return 'ci_' . $this->table_label . '_' . $this->label_another_service . '_order';
    }

    public function __construct($clientID, $token, $campaign_id, $label_another_service)
    {
        $this->client_id = $clientID;
        $this->token = $token;
        $this->campaign_id = $campaign_id;
        $this->label_another_service = $label_another_service;
    }

    public function get($link)
    {
        return $this->curlQuery($link, 'GET');
    }

    public function post($link, $ms_data)
    {
        return $this->curlQuery($link, 'POST', $ms_data);
    }

    public function put($link, $ms_data)
    {
        return $this->curlQuery($link, 'PUT', $ms_data);
    }

    public function put_pdf($link, $ms_data)
    {
        return $this->curlQuery($link, 'PUT', $ms_data, true);
    }

    public function post_pdf($link, $ms_data)
    {
        return $this->curlQuery($link, 'POST', $ms_data, true);
    }

    public function curlQueryPDF($link, $data = null) {

        $link = $this->host_api . $link;

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $link);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $out = curl_exec($curl);
        curl_close($curl);
        return $out;
    }

    public function curlLoadLabel($link, $path, $request='GET') {
        $link = $this->host_api . $link;
        $ch = curl_init($link);
        $fp2 = fopen ($path, 'w+');
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_URL, $link);
        curl_setopt($curl, CURLOPT_FILE, $fp2);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $request);
    
        if (strtolower($request) == "post" || strtolower($request) == "put") {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($send_data));
        }

        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            "Authorization: OAuth oauth_token=$this->token, oauth_client_id=$this->client_id"
        ));

        $out = curl_exec($curl);

        print_r(curl_error($curl));
        fclose($fp2);
        curl_close($curl);
        $data = json_decode($out, JSON_UNESCAPED_UNICODE);
        
        return $data;
    }

    private function curlQuery($link, $request, $send_data = null, $pdf=false) {
        
        $link = $this->host_api . $link;

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_URL, $link);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $request);
    
        if (strtolower($request) == "post" || strtolower($request) == "put") {
            if ($pdf) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($send_data, JSON_FORCE_OBJECT));
            }
            else {
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($send_data));
            }
        }

        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            "Authorization: OAuth oauth_token=$this->token, oauth_client_id=$this->client_id"
        ));
        $out = curl_exec($curl);

        //print_r(curl_error($curl));
        
        curl_close($curl);
        $json = json_decode($out, JSON_UNESCAPED_UNICODE);
        
        return $json;
    }

    /**
     * @param Connection $connect
     * @return bool
     */
    public function verifierTables($connect)
    {
        $check_resp1 = DBWorker::query($connect, "SHOW TABLES LIKE '".'ci_' . $this->table_label . '_' . $this->label_another_service . '_orders'."';");
        $check_resp2 = DBWorker::query($connect, "SHOW TABLES LIKE '".'ci_' . $this->table_label . '_' . $this->label_another_service . '_link'."';");
        return $check_resp1 && $check_resp2;
    }
}

class BeruProduct extends AbstractModel
{
    public function getTableName()
    {
        return 'ci_beru_products';
    }

    public function getFields()
    {
        return [
            'ID' => null,
            'MS_ID' => null,
            'NAME' => '',
            'MS_ARTICLE' => '',
            'MS_CODE' => '',
            'MS_EXTERNALCODE' => '',
            'MS_PRODUCTFOLDER' => '',
            'BERU_ID' => '',
            'BERU_OFFER_ID' => '',
            'PRICE' => '',
            'VAT' => '',
            'STOCK' => '',
            'DATE_UPDATE_STOCK' => '',
            'WAREHOUSE_ID' => ''
        ];
    }

    public function findByProductSKU($productSku, $select = '*')
    {
        $data = $this->findFirst([
            'select' => $select,
            'where'  => 'MS_CODE = :prod_sku',
            'bind'   => [
                ':prod_sku' => $productSku,
            ]
        ]);
        
        if ($data) {
            $this->load($data);
            return true;
        }

        return false;
    }

    public function findByProductCode($productCode, $select = '*')
    {
        $data = $this->findFirst([
            'select' => $select,
            'where'  => 'MS_CODE = :prod_code',
            'bind'   => [
                ':prod_code' => $productCode,
            ]
        ]);
        
        if ($data) {
            $this->load($data);
            return true;
        }

        return false;
    }

    public function findByMsID($msID, $select = '*')
    {
        $data = $this->findFirst([
            'select' => $select,
            'where'  => 'MS_ID = :ms_id',
            'bind'   => [
                ':ms_id' => $msID,
            ]
        ]);
        
        if ($data) {
            $this->load($data);
            return true;
        }

        return false;
    }

    public function downloadImage($imageName, $url)
    {
        $imgSrc = "images/$imageName.jpg";
        $ch = curl_init($url);
        $fp = fopen($imgSrc, 'wb');
        @curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        list($width, $height) = getimagesize($imgSrc);
        $myImage = imagecreatefromjpeg($imgSrc);

        if ($width > $height) {
            $y = 0;
            $x = ($width - $height) / 2;
            $smallestSide = $height;
        } else {
            $x = 0;
            $y = ($height - $width) / 2;
            $smallestSide = $width;
        }

        $thumbSize = 250;
        $thumb = imagecreatetruecolor($thumbSize, $thumbSize);
        imagecopyresampled($thumb, $myImage, 0, 0, $x, $y, $thumbSize, $thumbSize, $smallestSide, $smallestSide);
        imagejpeg($thumb, "images/{$imageName}_mini.jpg");

        $img = file_get_contents("images/{$imageName}_mini.jpg");
        return base64_encode($img);
    }
} //TODO: need work

class BeruLink extends AbstractModel
{
    private $beruAPI;
    private $table_name;

    public function __construct($connection, $beruAPI, $id = false) {
        $this->beruAPI = $beruAPI;
        parent::__construct($connection, $id);
        $this->campaign_id = $this->beruAPI->campaign_id;
        $this->table_name = 'ci_' . $beruAPI->table_label . '_' . $beruAPI->label_another_service . '_link';
    }

    public function getTableName()
    {
        return $this->table_name;
    }

    public function getFields()
    {
        return array(
            'ID' => null,
            'BERU_SKU' => null,
            'SHOP_SKU' => null,
            'CRM_ID' => null,
            'CRM_MODIFICATION_ID' => null,
            'PRODUCT_TYPE' => '',
            'B_PRICE' => 0,
            'B_STOCK' => null,
            'B_RESERVED' => null,
			'CAMPAIGN_ID' => null,
            'SALE_HIDE' => 0,
            'WAREHOUSE_ID' => null,
            'DATE_UPDATE_STOCK' => null,
            'UPDATED' => null
        );
    }

    public static function getByShopSKU($connect, $beruAPI, $shopSKU)
    {
        $obj = new BeruLink($connect, $beruAPI);
        $obj->findByShopSKU($shopSKU);
        return $obj;
    }

    public function findByShopSKU($shopSKU, $select = '*')
    {
        $data = $this->findLast([
            'select' => $select,
            'where'  => "SHOP_SKU='$shopSKU' AND CAMPAIGN_ID='" . $this->beruAPI->campaign_id . "'",
            'bind'   => [
                ':prod_sku' => $shopSKU,
            ]
        ]);
        
        if ($data) {
            $this->load($data);
            return true;
        }

        return false;
    }

    public function findByBeruSKU($beruSKU, $select = '*')
    {
        $data = $this->findLast([
            'select' => $select,
            'where'  => "BERU_SKU='$beruSKU' AND CAMPAIGN_ID='" . $this->beruAPI->campaign_id . "'",
            'bind'   => [
                ':prod_sku' => $beruSKU,
            ]
        ]);
        
        if ($data) {
            $this->load($data);
            return true;
        }

        return false;
    }

    public function findByMsID($msID, $select = '*')
    {
        $data = $this->findLast([
            'select' => $select,
            'where'  => "MS_ID='$msID' AND CAMPAIGN_ID='" . $this->beruAPI->campaign_id . "'",
            'bind'   => [
                ':ms_id' => $msID,
            ]
        ]);
        
        if ($data) {
            $this->load($data);
            return true;
        }

        return false;
    }
}

class BeruOrder extends AbstractModel
{
    private $beruAPI;
    private $table_name;

    public function getTableName()
    {
        return $this->table_name;
    }

    public function __construct($connection, $beruAPI, $id = false) {
        $this->beruAPI = $beruAPI;
        parent::__construct($connection, $id);
        $this->table_name = 'ci_' . $beruAPI->table_label . '_' . $beruAPI->label_another_service . '_orders';
    }

    public function getFields()
    {
        return array(
            'ID' => null,
            'NAME' => '',
            'CRM_ID' => '',
            'BERU_ID' => '',
            'CRM_POSITION_ID' => '',
            'APP_CLIENT_ID' => '',
            'PACKAGED' => '',
            'READYSHIP' => '',
            'DELIVERY' => '',
            'DELIVERED' => '',
            'PRINTED' => '',
            'READY_TO_SHIP' => '0',
            'STATE_READY_SEND' => '0',
            'STATE_DELIVERY_SEND' => '0',
            'STATE_CANCEL_SEND' => '0',
            'STATUS' => '',
            'SUBSTATUS' => '',
            'CRM_STATUS' => '',
            'UPDATED' => ''
        );
    }

    public static function getByBeruID($connect, $beruAPI, $beruID)
    {
        $obj = new BeruOrder($connect, $beruAPI);
        $obj->findByOrderID($beruID);
        return $obj;
    }

    public function findByMsID($id, $select = '*')
    {
        $data = $this->findFirst(array(
            'select' => $select,
            'where'  => "MS_ID='$id'",
            'bind'   => array(
                ':ord_id' => $id,
            )
        ));

        if ($data) {
            $this->load($data);
            return true;
        }

        return false;
    }

    public function findByOrderID($orderId, $select = '*')
    {
        $data = $this->findFirst(array(
            'select' => $select,
            'where'  => "BERU_ID='$orderId'",
            'bind'   => array(
                ':ord_id' => $orderId,
            )
        ));
        
        if ($data) {
            $this->load($data);
            return true;
        }

        return false;
    }

    public function findByOrderName($orderName, $select = '*')
    {
        $data = $this->findFirst(array(
            'select' => $select,
            'where'  => "NAME='$orderName'",
            'bind'   => array(
                ':ord_id' => $orderName,
            )
        ));
        
        if ($data) {
            $this->load($data);
            return true;
        }

        return false;
    }
}