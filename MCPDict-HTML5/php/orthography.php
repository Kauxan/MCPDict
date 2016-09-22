<?php

/**
* Orthography
*/
class Orthography
{
	/**
	 * @var string, array => string : $_raw_folder, $_raw_file
	 * @access private
	 */
	private $_raw_folder = '../res/raw/';
	private $_raw_file = array(
		'orthography_hz_variants.txt', 
		'orthography_mc_initials.tsv', 
		'orthography_mc_finals.tsv', 
		'orthography_mc_bieng_sjyix.tsv', 
		'orthography_pu_pinyin.tsv', 
		'orthography_pu_bopomofo.tsv', 
		'orthography_ct_initials.tsv', 
		'orthography_ct_finals.tsv', 
		'orthography_vn.tsv', 
		'orthography_jp.tsv'
	);

	/**
	 * @var array $_file
	 * @access private
	 */
	private $_file = array(
		'mc_initials' => '../json/mc_initials.json', 
		'mc_finals' => '../json/mc_finals.json', 
		'mc_bieng_sjyix' => '../json/mc_bieng_sjyix.json', 
		'pu_pinyin' => '../json/pu_pinyin.json', 
		'pu_bpmf' => '../json/pu_bpmf.json', 
		'ct_initials' => '../json/ct_initials.json', 
		'ct_finals' => '../json/ct_finals.json', 
		'kr_initials' => '../json/kr_initials.json', 
		'kr_vowels' => '../json/kr_vowels.json', 
		'kr_finals' => '../json/kr_finals.json', 
		'vn' => '../json/vn.json', 
		'jp' => '../json/jp.json'
	);

	/**
	 * @var array(json-Object), $files
	 * @access private
	 */
	private $mc_initials;
	private $mc_finals;
	private $mc_bieng_sjyix;
	private $pu_pinyin;
	private $pu_bpmf;
	private $ct_initials;
	private $ct_finals;
	private $kr_initials;
	private $kr_vowels;
	private $kr_finals;
	private $vn;
	private $jp;

	/**
	 * constant
	 * Korean : Hangul-unicode
	 */
	const FIRST_HANGUL = 0xAC00;
	const LAST_HANGUL = 0xD7A3;

	/**
	 * constructor
	 * @access public
	 * @return 
	 */
	function __construct()
	{
		$this->mc_initials = json_decode(file_get_contents($this->_file['mc_initials']), true);
		$this->mc_finals = json_decode(file_get_contents($this->_file['mc_finals']), true);
		$this->mc_bieng_sjyix = json_decode(file_get_contents($this->_file['mc_bieng_sjyix']), true);
		$this->pu_pinyin = json_decode(file_get_contents($this->_file['pu_pinyin']), true);
		$this->pu_bpmf = json_decode(file_get_contents($this->_file['pu_bpmf']), true);
		$this->ct_initials = json_decode(file_get_contents($this->_file['ct_initials']), true);
		$this->ct_finals = json_decode(file_get_contents($this->_file['ct_finals']), true);
		$this->kr_initials = json_decode(file_get_contents($this->_file['kr_initials']), true);
		$this->kr_vowels = json_decode(file_get_contents($this->_file['kr_vowels']), true);
		$this->kr_finals = json_decode(file_get_contents($this->_file['kr_finals']), true);
		$this->vn = json_decode(file_get_contents($this->_file['vn']), true);
		$this->jp = json_decode(file_get_contents($this->_file['jp']), true);
	}

	/**
	 * convert $record into several display-string with accent
	 *   by $type and $display_setting
	 * @access public
	 * @param string $one
	 * @param string $type
	 * @param array $display_setting
	 * @return string
	 */
	public function get_display($one, $type, $display_setting)
	{
		switch ($type) {
			case 'c1mc': return $this->get_MiddleChinese_detail($one);
			case 'c2pu': return $this->get_Mandarin_detail($one, $display_setting[0]);
			case 'c3ct': return $this->get_Cantonese_detail($one, $display_setting[1]);
			case 'c4sh': case 'c5mn': return $one;
			case 'c6kr': return $this->get_Korean_detail($one, $display_setting[2]);
			case 'c7vn': return $this->get_Vietnamese_detail($one, $display_setting[3]);
			case 'c8jp_go': case 'c9jp_kan': case 'c10jp_tou': case 'c11jp_kwan': case 'c12jp_other': 
				return $this->get_Japanese_detail($one, $display_setting[4]);
			default:
				return $one;
		}
		return "";
	}

	/**
	 * c1mc : 中古拼音 (廣韻 平水韻)
	 * @access private
	 * @param string $s
	 * @return string
	 */
	private function get_MiddleChinese_detail($s)
	{
		// save raw $s
		$raw = $s;
		// get tone
		$tone = 0;
		$u = substr($s, 0, strlen($s)-1);
		switch ($s[strlen($s)-1]) {
			case 'x': $tone = 1; $s = $u; break;
			case 'h': $tone = 2; $s = $u; break;
			case 'd': $tone = 2; break;
			case 'p': $tone = 3; $s = $u.'m'; break;
			case 't': $tone = 3; $s = $u.'n'; break;
			case 'k': $tone = 3; $s = $u.'ng'; break;
		}

		// split initial and final
		$init = '';
		$fin = '';
		$extraJ = false;
		$p = strpos($s, '0');
		if ($p !== false) {
			$raw = str_replace("0", "\'", $s);
			$init = (substr($s, 0, $p) == 'i') ? '' : substr($s, 0, $p) ;
			$fin = substr($s, $p+1);
			if (!isset($this->mc_initials[$init]) || !isset($this->mc_finals[$fin])) return ""; // fail
		} else {
			for ($i = 3; $i >= 0; $i--) {
				if ($i <= strlen($s) && isset($this->mc_initials[substr($s, 0, $i)])) {
					$init = substr($s, 0, $i);
					$fin = substr($s, $i);
					break;
				}
			}
			if ($fin == '') return ""; // fail
			// extra 'j' in syllables that look like 重紐A類
			if ($fin[0] == 'j') {
				if (strlen($fin) < 2) return ""; // fail
				$extraJ = true;
				if ($fin[1] == 'i' || $fin[1] == 'y') {
					$fin = substr($fin, 1);
				} else {
					$fin = 'i'.substr($fin, 1);
				}
			}
			// recover omitted glide in final
			if (strlen($init) > 0 && $init[strlen($init)-1] == 'r') { // 只能拼二等或三等韻 二等韻省略介音'r'
				if (strlen($fin) > 0 && $fin[0] != 'i' && $fin[0] != 'y') {
					$fin = 'r'.$fin;
				}
			} else if (strlen($init) > 0 && $init[strlen($init)-1] == 'j') {
				if (strlen($fin) > 0 && $fin[0] != 'i' && $fin[0] != 'y') { // 只能拼三等韻 省略介音'i'
					$fin = 'i'.$fin;
				}
			}
		}
		if (!isset($this->mc_finals[$fin])) return ""; // fail

		// distinguish 重韻
		if ($fin == 'ia') { // 牙音声母為戈韻 其餘為麻韻
			$in_1 = array('k', 'kh', 'g', 'ng');
			if (in_array($init, $in_1)) {
				$fin = 'Ia';
			}
		} else if ($fin == 'ieng' || $fin == 'yeng') { // 唇牙喉声母直接接-ieng|-yeng者及莊組爲庚韻 其餘爲清韻
			$in_2 = array(
				'p', 'ph', 'b', 'm',
				'k', 'kh', 'g', 'ng',
				'h', 'gh', 'q', '',
				'cr', 'chr', 'zr', 'sr', 'zsr'
			);
			if (in_array($init, $in_2) && !$extraJ) {
				$fin = ($fin == 'ieng') ? 'Ieng' : 'Yeng' ;
			}
		} else if ($fin == 'in') { // 莊組声母爲臻韻 其餘爲眞韻
			$in_3 = array('cr', 'chr', 'zr', 'sr', 'zsr');
			if (in_array($init, $in_3)) {
				$fin = 'In';
			}
		} else if ($fin == 'yn') { // 脣牙喉音声母直接接-yn者爲眞韻 其餘爲諄韻
			$in_4 = array(
				'p', 'ph', 'b', 'm',
				'k', 'kh', 'g', 'ng',
				'h', 'gh', 'q', ''
			);
			if (in_array($init, $in_4) && !extraJ) {
				$fin = 'Yn';
			}
		}

		// resolve 重紐
		$dryung_nriux = '';
		$syuu = mb_substr($this->mc_finals[$fin][3], 0, 1, 'UTF-8');
		$p = mb_strpos("支脂祭眞仙宵侵鹽", $syuu, 0, 'UTF-8');
		$in_5 = array(
			'p', 'ph', 'b', 'm',
			'k', 'kh', 'g', 'ng',
			'h', 'gh', 'q', '', 'j'
		);
		if ($p !== false && in_array($init, $in_5)) {
			$dryung_nriux = ($extraJ || $init == 'j') ? 'A' : 'B' ;
		}

		// render details
		$mux = $this->mc_initials[$init];
		$sjep = $this->mc_finals[$fin][0];
		$yonh = mb_substr($this->mc_finals[$fin][3], 
					(strlen($fin) > 0 && $fin[strlen($fin)-1] == 'd') ? 0 : $tone , 1, 'UTF-8');
		$tongx = $this->mc_finals[$fin][1];
		$ho = $this->mc_finals[$fin][2];
		$bieng_sjyix = $this->mc_bieng_sjyix[$yonh];

		return $raw.'('.$mux.$sjep.$yonh.$dryung_nriux.$tongx.$ho.' '.$bieng_sjyix.')';
	}

	/**
	 * c2pu : 普通話 (漢語拼音 or 注音符號)
	 * @access private
	 * @param string $s
	 * @param string $mode
	 * @return string
	 */
	private function get_Mandarin_detail($s, $mode)
	{
		// get tone
		$tone = $s[strlen($s)-1];
		if (in_array($tone, array('1', '2', '3', '4'))) {
			$s = substr($s, 0, strlen($s)-1);
		} else {
			$tone = '_';
		}

		// switch mode
		switch ($mode) {
			case '0': // pinyin
				// find letter to carry the tone
				$pos = -1;
				if (strlen($s) > 2 && substr($s, strlen($s)-2) == 'iu') { // in combination 'iu', 'u' gets the tone
					$pos = strlen($s)-1;
				} else { // find letter in vowels order
					$vowels = array('a', 'o', 'e', 'i', 'u', 'v', 'n', 'm');
					foreach ($vowels as $c) {
						$pos = strpos($s, $c);
						if ($pos !== false) break;
					}
				}
				$display = '';
				if ($pos === false) return ""; // fail
				// transform $s and add tone to letter
				for ($i = 0; $i < strlen($s); $i++) { 
					$t = ($i == $pos) ? $tone : '_' ;
					$key = $s[$i].$t;
					if (isset($this->pu_pinyin[$key])) {
						$display .= $this->pu_pinyin[$key];
					} else {
						$display .= $s[$i];
						if ($t != '_') $display .= $this->pu_pinyin['_'.$t];
					}
				}
				return $display;
			case '1': // bopomofo
				$_to_tone = $this->pu_bpmf['to_Tone'];
				$_to_partial = $this->pu_bpmf['to_Partial'];
				$_to_whole = $this->pu_bpmf['to_Whole'];
				if (isset($_to_whole[$s])) {
					$s = $_to_whole[$s];
				} else {
					if (in_array(substr($s, 0, 2), array('ju', 'qu', 'xu'))) {
						$s = $s[0].'v'.substr($s, 2);
					}
					$p = strlen($s);
					if ($p > 2) $p = 2;
					while ($p > 0) {
						if (isset($_to_partial[substr($s, 0, $p)])) break;
						$p--;
					}
					if ($p == 0) return ""; // fail
					if (!isset($_to_partial[substr($s, $p)])) return ""; // fail
					$s = $_to_partial[substr($s, 0, $p)].$_to_partial[substr($s, $p)];
				}
				// set tone
				switch ($tone) {
					case '2': case '3': case '4':
						return $s.$_to_tone[$tone];
						break;
					case '_':
						return $_to_tone[$tone].$s;
						break;
					default:
						return $s;
				}
			default:
				return ""; // fail
		}
	}

	/**
	 * c3ct : 粵語 (Jyutping-粵拼 or CantonesePinyin-教院式 or Yale-耶魯式 or LauSidney-劉錫祥式)
	 * @access private
	 * @param string $s
	 * @param string $mode
	 * @return string
	 */
	private function get_Cantonese_detail($s, $mode)
	{
		// switch mode
		$ct_init = array();
		$ct_fin = array();
		switch ($mode) {
			case '0': return $s; // J
			case '1': $ct_init = $this->ct_initials['J2C']; $ct_fin = $this->ct_finals['J2C']; break; // C
			case '2': $ct_init = $this->ct_initials['J2Y']; $ct_fin = $this->ct_finals['J2Y']; break; // Y
			case '3': $ct_init = $this->ct_initials['J2L']; $ct_fin = $this->ct_finals['J2L']; break; // L
		}

		// get tone
		$tone = $s[strlen($s)-1];
		if ($tone >= '1' && $tone <= '6') {
			$s = substr($s, 0, strlen($s)-1);
		} else {
			$tone = '_';
		}
		// get final
		$pos = 0;
		while ($pos < strlen($s) && !isset($ct_fin[substr($s, $pos)])) $pos++;
		if ($pos == strlen($s)) return ""; // fail
		$fin = $ct_fin[substr($s, $pos)];
		// get initial
		$init = ($pos > 0 && isset($ct_init[substr($s, 0, $pos)])) ? $ct_init[substr($s, 0, $pos)] : '' ;
		if ($init == '') return ""; // fail

		// in CantonesePinyin, tones 7, 8, 9 are used for entering tones
		if ($mode == '1' && in_array($fin[strlen($fin)-1], array('p', 't', 'k'))) {
			switch ($tone) {
				case '1': $tone = '7'; break;
				case '3': $tone = '8'; break;
				case '6': $tone = '9'; break;
			}
		}
		// in Yale, initial 'y' is omitted if final begins with 'yu'
		if ($mode == '2' && $init == 'y' && substr($fin, 0, 2) == 'yu') $init = "";

		return $init.$fin.( ($tone == '_') ? "" : $tone );
	}

	// c4sh, c5mn 吳語和閩南語 直接返回原型

	/**
	 * c6kr : 朝鮮語 (Hangul-諺文 or Romanization-文觀部2000年羅馬字)
	 * @access private
	 * @param string $s
	 * @param string $mode
	 * @return string
	 */
	private function get_Korean_detail($s, $mode)
	{
		// Romanization
		if ($mode == '1') return $s;

		// Hangul
		$L = strlen($s);
		// initial
		$p = 0;
		for ($i = 2; $i > 0; $i--) { 
			if ($i <= $L && isset($this->kr_initials[substr($s, 0, $i)])) {
				$p = $i;
				break;
			}
		}
		$init = substr($s, 0, $p);
		$x = $this->kr_initials[$init];
		// final
		$q = $L;
		for ($i = $L-2; $i < $L; $i++) { 
			if ($i >= $p && isset($this->kr_finals[substr($s, $i)])) {
				$q = $i;
				break;
			}
		}
		$fin = (strlen($s) == $q) ? '' : substr($s, $q) ;
		$z = $this->kr_finals[$fin];
		// vowels
		if ($p > $q || !isset($this->kr_vowels[substr($s, $p, $q-$p)])) return ""; // fail
		$vow = substr($s, $p, $q-$p);
		$y = $this->kr_vowels[$vow];

		$code = self::FIRST_HANGUL+($x*21+$y)*28+$z;
		$ord_1 = decbin(0xe0|($code>>12));
		$ord_2 = decbin(0x80|(($code>>6)&0x3f));
		$ord_3 = decbin(0x80|($code&0x3f));
		return chr(bindec($ord_1)).chr(bindec($ord_2)).chr(bindec($ord_3));
	}

	/**
	 * c7vn : 越南語 (舊式 or 新式)
	 * @access private
	 * @param string $s
	 * @param string $mode
	 * @return string
	 */
	private function get_Vietnamese_detail($s, $mode)
	{
		// get tone
		$tone = $s[strlen($s)-1];
		if (in_array($tone, array('f', 'r', 'x', 's', 'j'))) {
			$s = substr($s, 0, strlen($s)-1);
		} else {
			$tone = '_';
		}

		// vowel with quality marker carries tone marker
		// in combination "ươ", "ơ" gets the tone
		$result = '';
		$p = 0;
		while ($p < strlen($s)) {
			if ($p == strlen($s)-1) {
				$result .= $s[$p];
				break;
			}
			$key = substr($s, $p, 2);
			if (isset($this->vn[$key.'_'])) {
				if ($key == 'dd' || $p+4 <= strlen($s) && substr($s, $p, 4) == 'uwow') {
					$result .= $this->vn[$key.'_']; // no tone marker
				} else {
					$result .= $this->vn[$key.$tone]; // tone marker here
					$tone = '_';
				}
				$p += 2;
			} else {
				$result .= $s[$p++];
			}
		}
		if ($tone == '_') return $result; // no tone marker to place

		// find first and last vowel
		$p = 0;
		while ($p < strlen($result) && !in_array($result[$p], array('a', 'e', 'i', 'o', 'u', 'y'))) $p++;
		if ($p == strlen($result)) return ""; // fail
		$q = $p+1;
		while ($q < strlen($result) && in_array($result[$q], array('a', 'e', 'i', 'o', 'u', 'y'))) $q++;
		// decide which vowel to get the tone marker
		if ($q-$p == 3 || 
			$q-$p == 2 && ($q < strlen($result) || 
							substr($s, 0, 2) == 'gi' || 
							substr($s, 0, 2) == 'qu' || 
							$mode == '1' && (substr($s, $p, $q-$p) == 'oa' || 
											substr($s, $p, $q-$p) == 'oe' || 
											substr($s, $p, $q-$p) == 'uy'))) $p++;
		// place tone marker
		$u = $this->vn[$result[$p].$tone];
		$result = substr($result, 0, $p).$u.substr($result, $p+1);

		return $result;
	}

	/**
	 * c8jp_go, c9jp_kan, c10jp_tou, c11jp_kwan, c12jp_other : 
	 *   日語 (Hiragana-平假名 or Katakana-片假名 or Nippon-日本式 or Hepburn-黑本式)
	 * @access private
	 * @param string $s
	 * @param string $mode
	 * @return string
	 */
	private function get_Japanese_detail($s, $mode)
	{
		// Nippon
		if ($mode == '2') {
			return $s;
		}

		// switch mode
		$jp = array();
		switch ($mode) {
			case '0': $jp = $this->jp['hiragana']; break;
			case '1': $jp = $this->jp['katakana']; break;
			case '3': $jp = $this->jp['hepburn']; break;
		}

		$result = '';
		$p = 0;
		while ($p < strlen($s)) {
			$q = $p;
			for ($i = 4; $i > 0; $i--) { 
				if ($p+$i <= strlen($s) && isset($jp[substr($s, $p, $i)])) {
					$q = $p+$i;
					$result .= $jp[substr($s, $p, $q-$p)];
					break;
				}
			}
			if ($q == $p) return "";
			$p = $q;
		}

		return $result;
	}

}
