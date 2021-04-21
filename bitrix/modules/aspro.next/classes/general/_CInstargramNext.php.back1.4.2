<?
if(!defined('ASPRO_NEXT_MODULE_ID'))
	define('ASPRO_NEXT_MODULE_ID', 'aspro.next');


class CInstargramNext{
	const MODULE_ID = ASPRO_NEXT_MODULE_ID;
	const URL_INSTAGRAM_API = 'https://api.instagram.com/v1/';

	private $access_token = 0;
	public $token_params = 0;
	public $count_post = 0;
	public $error = "";
	public $App = "";

	public function __construct($token, $count = 10){
		global $APPLICATION;
		$this->token_params = $token;
		$this->count_post = $count;
		$this->App=$APPLICATION;
	}

	public function checkApiToken(){
		if(!strlen($this->token_params)){
			$this->error="No API token instagram";
		}
		$this->access_token='/?access_token='.$this->token_params;
	}

	public function getFormatResult($method){
		if(function_exists('curl_init'))
		{
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, self::URL_INSTAGRAM_API.$method.$this->access_token."&count=".$this->count_post);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
			$out = curl_exec($curl);
			$data =  $out ? $out : curl_error($curl);			
		}
		else
		{
			$data = file_get_contents(self::URL_INSTAGRAM_API.$method.$this->access_token);			
		}
		
		$data = json_decode($data, true);
		$data = $this->App->ConvertCharsetArray($data, 'UTF-8', LANG_CHARSET);

		return $data;
	}

	public function getInstagramPosts(){
		$this->checkApiToken();

		if($this->error){
			return array("ERROR" => "Y", "MESSAGE" => $this->error);
		}else{
			$data=$this->getFormatResult('users/self/media/recent');
		}

		return $data;
	}
	
	public function getInstagramUser(){
		$this->checkApiToken();

		if($this->error){
			return $this->error;
		}else{
			$data=$this->getFormatResult('users/self');
		}

		return $data;
	}

	public function getInstagramTag($tag) {
		$this->checkApiToken();

		if($this->error){
			return $this->error;
		}else{
			$data=$this->getFormatResult('tag/'.$tag.'/media/recent');
		}

		return $data;
	}
}?>