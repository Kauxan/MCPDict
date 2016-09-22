<?php

/**
* Code : encode & decode, string processing
*/
class Code
{
	/**
	 * Chinese-character "/[ \u4E00-\u9FA5]+/"
	 * @access private
	 * @param string $hex_str
	 * @return boolean
	 */
	private function isHanzi($hex_str)
	{
		$dec_str = hexdec($hex_str);
		return ($dec_str >= hexdec("4E00") && $dec_str <= hexdec("9FA5"));
	}

	/**
	 * convert <UTF-8 characters> into <Unicode UCS-2>
	 * @access public
	 * @param string $string
	 * @param string in-encoding : default 'UTF-8'
	 * @param string out-encoding : default 'UCS-2'
	 * @return array $resx only multibytes-characters remained
	 */
	public function escape($string, $in_encoding='UTF-8', $out_encoding='UCS-2')
	{
		$resx = array();
		if (function_exists('mb_get_info')) {
			for ($x = 0; $x < mb_strlen($string, $in_encoding); $x++) {
				$str = mb_substr($string, $x, 1, $in_encoding);
				if (strlen($str) > 1) { // multibytes-character
					$hex_str = bin2hex(mb_convert_encoding($str, $out_encoding, $in_encoding));
					if ($this->isHanzi($hex_str)) array_push($resx, strtoupper($hex_str));
				} // else { array_push($resx, strtoupper(bin2hex($str)));}
			}
		}
		return $resx;
	}

	/**
	 * convert <Unicode> into <UTF-8>
	 * @access public
	 * @param string $unicode hex-string
	 * @return string $utf8_str
	 */
	public function unescape($unicode)
	{
		$utf8_str = '';
		$code = intval(hexdec($unicode));
		$ord_1 = decbin(0xe0|($code>>12));
		$ord_2 = decbin(0x80|(($code>>6)&0x3f));
		$ord_3 = decbin(0x80|($code&0x3f));
		$utf8_str = chr(bindec($ord_1)).chr(bindec($ord_2)).chr(bindec($ord_3));
		return $utf8_str;
	}

	/**
	 * remove white-character : "/[ \f\n\r\t\v]/" or "/\s/", remove empty
	 * @access public
	 * @param string $string
	 * @return array $sp2
	 */
	public function remove_white($string)
	{
		$sp1 = preg_split("/\s+/", $string);
		$sp2 = array();
		foreach ($sp1 as $value) {
			if ($value != "") {
				array_push($sp2, trim($value));
			}
		}
		return $sp2;
	}

	/**
	 * preprocess of $query_string before query
	 * @access public
	 * @param string $string
	 * @param string $mode : integer 0~10
	 * @return array
	 */
	public function preprocess($string, $mode)
	{
		if ($mode == "0") {
			return $this->escape($string);
		} else {
			return $this->remove_white($string);
		}
	}
}
