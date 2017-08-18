<?php
/**
 * Function
 *
 * LICENSE
 *
 * 微笑
 */

if (!\function_exists(__NAMESPACE__ . '\var_dump_die')) {
	/**
	 * var_dump and die
	 * @author 
	 * @date 2018-8-18
	 * @param $expression
	 * @param null $_
	 */
	function var_dump_die($expression) {
		var_dump($expression);
		die;
	}
}


if (!\function_exists(__NAMESPACE__ . '\delArrValue')) {
	/*
	 * 清除掉数组中的某个值
	 * @author 微笑
	 * @date 2017-8-18
	 *
	 * @param 
	 * @return array
	 */

	function delArrValue($arr, $val) {
		foreach ($arr as $key => $value)
			if ($value == $val)
				unset($arr[$key]);
		return $arr;
	}
}



if (!\function_exists(__NAMESPACE__ . '\ensure_path_exists')) {
	/**
	 * 确认路径存在 ，不存在创建
	 * @author 微笑
	 * @date 2017-8-18
	 *
	 * @param type $save_path
	 * @return bool
	 */
	function ensure_path_exists($save_path) {
		if (!file_exists($save_path)) {
			$result = mkdir($save_path, 0777, true);
			chmod($save_path, 0777);
			return $result;
		}
		return true;
	}
}

if (!\function_exists(__NAMESPACE__ . '\getLogFile')) {
	/**
	 * 获取日志文件目录名称，目录在xx/log 下面 
	 * @author 微笑
	 * @date 2017-8-18
	 * @deprecated
	 *
	 * @return string
	 */
	function getLogFile($name) {
		$ymd = date('Y-m-d', time());
		$logFile = "log/{$ymd}/$name";
		$logPath = pathinfo($logFile)['dirname'];
		if (!ensure_path_exists($logPath))
			return false;//var_dump_die('logger error');

		return $logFile;
	}
}

if (!\function_exists(__NAMESPACE__ . '\logFile')) {
	/**
	 * 接口调用记录错误日志 
	 * @author 微笑
	 * @date 2017-8-18
	 *
	 * @deprecated
	 * @param type $filename
	 * @param type $msg
	 */
	function logFile($filename, $msg) {
		$save_path = __ROOT__ . '/xx/log/';
		if (!file_exists($save_path)) {
			mkdir($save_path, 0777);
		}
		//创建文件夹
		$ymd = date("Y-m-d");
		$save_path .= $ymd . "/";

		if (!file_exists($save_path)) {
			mkdir($save_path);
		}
		chmod($save_path, 0777);
		//打开文件
		$fd = fopen($save_path . $filename, "a");
		//增加文件
		$str = "[" . date("Y/m/d h:i:s", time()) . "]" . $msg;
		//写入字符串
		fwrite($fd, $str . "\r\n");
		//关闭文件
		fclose($fd);
	}

}

if (!\function_exists(__NAMESPACE__ . '\encrypt')) {
	/* * *******************************************************************
	  * 函数名称:encrypt
	  * 函数作用:加密解密字符串
	  * @author 微笑
	  * @date 2017-8-18
	  * 使用方法:
	  * 加密     :encrypt('str','E','nowamagic');
	  * 解密     :encrypt('被加密过的字符串','D','nowamagic');
	  * 参数说明:
	  * $string   :需要加密解密的字符串
	  * $operation:判断是加密还是解密:E:加密   D:解密
	  * $key      :加密的钥匙(密匙);
	 * ******************************************************************* */

	function encrypt($string, $operation, $key = '') {
		$key = md5($key);
		$key_length = strlen($key);
		$string = $operation == 'D' ? base64_decode($string) : substr(md5($string . $key), 0, 8) . $string;
		$string_length = strlen($string);
		$rndkey = $box = array();
		$result = '';
		for ($i = 0; $i <= 255; $i++) {
			$rndkey[$i] = ord($key[$i % $key_length]);
			$box[$i] = $i;
		}
		for ($j = $i = 0; $i < 256; $i++) {
			$j = ($j + $box[$i] + $rndkey[$i]) % 256;
			$tmp = $box[$i];
			$box[$i] = $box[$j];
			$box[$j] = $tmp;
		}
		for ($a = $j = $i = 0; $i < $string_length; $i++) {
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;
			$tmp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $tmp;
			$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
		}
		if ($operation == 'D') {
			if (substr($result, 0, 8) == substr(md5(substr($result, 8) . $key), 0, 8)) {
				return substr($result, 8);
			} else {
				return '';
			}
		} else {
			return str_replace('=', '', base64_encode($result));
		}
	}
}



if (!\function_exists(__NAMESPACE__ . '\guId')) {
	/**
	 * 随机生成ID（time+shopId+rand）
	 * @author weixiao
	 * Date: ${DATE}
	 * Time: ${TIME}
	 * @param null $shopId int 店铺ID
	 * @param int $Length int 生成长度 ($minLength=15,$maxLength=18)
	 * @return string time+shopId+rand
	 */
	function guId($shopId=null,$Length=15){
		//生成的ID最小位数
		$minLength = 15;
		//生成的ID最大位数
		$maxLength = 18;
		//随机数生成位数
		$randLength = 0;
		//shopId位数
		$shopIdLength = 0;
		//当前时间戳
		$time = time();
		$timeLength = strlen($time);
		//判断店铺ID是否带入，并计算其长度
		if(!empty($shopId)&&is_numeric($shopId)){
			$shopIdLength = strlen($shopId);
			$shopId = $shopId;
		}else{
			$shopIdLength = 0;
			$shopId = '';
		}
		//判断需要生成位数
		if($Length<=$minLength){
			$Length = $minLength;
		}
		if($Length>=$maxLength){
			$Length = $maxLength;
		}
		//计算需要生成的随机码位数
		$randLength = $Length-$shopIdLength-$timeLength;
		if($randLength<=0){
			//如果shopId+time的总位数多余所要生成的Length位数，直接用time+rand
			$guid = $time.mt_rand(sprintf("%-0".($Length-$timeLength)."s", 1),sprintf("%-'9".($Length-$timeLength)."s", 9));
		}else{
			$guid = $time.$shopId.mt_rand(sprintf("%-0".$randLength."s", 1),sprintf("%-'9".$randLength."s", 9));
		}
		return $guid;
	}
}

if (!\function_exists(__NAMESPACE__ . '\uuid')) {

	/**
	 * 获取uuid值
	 * @return string
	 */
	function uuid() {
		mt_srand((double)microtime() * 10000);
		$charid = strtoupper(md5(uniqid(rand(), true)));
		$hyphen = chr(45); //"-"
		$uuid = substr($charid, 0, 8) . $hyphen . substr($charid, 8, 4)
			. $hyphen . substr($charid, 12, 4) . $hyphen . substr($charid, 16, 4)
			. $hyphen . substr($charid, 20, 12);
		return $uuid;
	}
}
