<?php
require_once 'code.php';
require_once 'orthography.php';

/**
* HTML Templates
*/
class TemplatesAssemble
{
	/**
	 * @static Code Object
	 * @access public
	 */
	static public $code;

	/**
	 * @static Orthography Object
	 * @access public
	 */
	static public $orth;

	/**
	 * @var array
	 * @access protected
	 */
	protected $icon = array(
		// 'c0unicode' => ['', ''],
		'c1mc' => ['lang_mc.png', '中古'],
		'c2pu' => ['lang_pu.png', '普'],
		'c3ct' => ['lang_ct.png', '粵'],
		'c4sh' => ['lang_sh.png', '吳'],
		'c5mn' => ['lang_mn.png', '閩'],
		'c6kr' => ['lang_kr.png', '朝'],
		'c7vn' => ['lang_vn.png', '越'],
		'c8jp_go' => ['lang_jp_go.png', '日吳'],
		'c9jp_kan' => ['lang_jp_kan.png', '日漢'],
		'c10jp_tou' => ['lang_jp_tou.png', '日唐'],
		'c11jp_kwan' => ['lang_jp_kwan.png', '日慣'],
		'c12jp_other' => ['lang_jp_other.png', '日他']
	);

	/**
	 * @var array
	 * @access protected
	 */
	protected $display_setting;

	/**
	 * constructor
	 * @access public
	 * @param array $display_setting
	 * @return 
	 */
	public function __construct($display_setting)
	{
		$this->display_setting = $display_setting;
	}

	/**
	 * create HTML5 result-card by $query_result
	 * @access public
	 * @param array $query_result
	 * @return string $HTML HTML5-text
	 */
	public function create_templates($query_result)
	{
		$HTML = '';
		$n = count($query_result);
		for ($i = 0; $i < $n; $i++) { 
			$item = $query_result[$i];
			$unicode = $item['c0unicode'];
			// add head
			$HTML .= '<div class="container result-card" value="'.$item['docid'].'"><div class="row">';
			// add col-xs-char
			$HTML .= '<div class="col-xs-3 col-xs-char"><span class="h1">'.
					self::$code->unescape($unicode).'</span><br><span class="char-code"><small>U+'.
					$unicode.'</small></span>'.
					(($item['variants_base_unicode'] == '') ? '' : '<br><span class="h5"><small>（'.self::$code->unescape($item['variants_base_unicode']).'）</small></span>' ).'</div>';
			// add col-xs-detail
			$HTML .= '<div class="col-xs-9 col-xs-detail"><div class="container">'.
					$this->create_details($item).'</div></div>';
			// add tail
			$HTML .= '</div></div>';
			if ($i < $n-1) $HTML .= '<hr>';
		}
		return $HTML;
	}

	/**
	 * create right-side part details HTML
	 * @access private
	 * @param array $item
	 * @return string $HTML HTML5-text
	 */
	private function create_details($item)
	{
		$detail = '';
		$k = 0;
		foreach ($this->icon as $key => $value) {
			if ($item[$key]) {
				if ($key == 'c1mc') {
					$detail .= '<div class="row"><div class="col-xs-12 col-xs-orthography"><table><tr><td><img src="res/drawable/'.
							$value[0].'" alt="'.
							$value[1].'" width="24px" height="24px"></td><td>'.
							$this->formalization($item[$key], $key).'</td></tr></table></div></div>';
				} else {
					$k++;
					if ($k%2) $detail .= '<div class="row">';
					$detail .= '<div class="col-xs-6 col-xs-orthography"><table><tr><td><img src="res/drawable/'.
							$value[0].'" alt="'.
							$value[1].'" width="24px" height="24px"></td><td>'.
							$this->formalization($item[$key], $key).'</td></tr></table></div>';
					if (!($k%2)) $detail .= '</div>';
				}
			}
		}
		if ($k%2) $detail .= '</div>';
		return $detail;
	}

	/**
	 * convert <database-record> into <formalized-orthography>
	 * @access private
	 * @param string $record
	 * @param string $type : $key in $icon (from 'c1mc' to 'c12jp_other')
	 * @return string
	 */
	private function formalization($record, $type)
	{
		$formalized_show = '';
		// line break
		switch ($type) {
			case 'c1mc': $record = str_replace(",", "\n", $record); break;
			case 'c8jp_go': case 'c9jp_kan': case 'c10jp_tou': case 'c11jp_kwan': case 'c12jp_other': 
				if ($record[0] == '[') {
					$record = '['.str_replace("[", "\n[", substr($record, 1));
				}
				break;
		}
		// display one for each lang
		$L = strlen($record);
		$p = 0;
		while ($p < $L) {
			$q = $p;
			// take out substring of one-term-record
			while ($q < $L && $this->isLetterOrDigit($record[$q])) $q++;
			if ($q > $p) {
				$one = substr($record, $p, $q-$p);
				$displayed = self::$orth->get_display($one, $type, $this->display_setting);
				$formalized_show .= ($displayed == "") ? $one : $displayed ;
				$p = $q;
			}
			// remain symbols
			while ($p < $L && !$this->isLetterOrDigit($record[$p])) $p++;
			$formalized_show .= substr($record, $q, $p-$q);
		}
		// add space for line wrapping hints
		$formalized_show = str_replace(",", ", ", $formalized_show);
		$formalized_show = str_replace("(", " (", $formalized_show);
		$formalized_show = str_replace("]", "] ", $formalized_show);
		$formalized_show = str_replace("/", "/ ", $formalized_show);
		$formalized_show = str_replace(" +", " ", $formalized_show);
		$formalized_show = str_replace(" ,", ",", $formalized_show);
		$formalized_show = trim($formalized_show);
		// add HTML tags
		$formalized_show = str_replace("\n", "<br>", $formalized_show);
		$final_show = '';
		$f1 = true; $f2 = true;
		$L = strlen($formalized_show);
		$p = 0;
		while ($p < $L) {
			$q = $p;
			while ($q < $L && $formalized_show[$q] != '*' && $formalized_show[$q] != '|') $q++;
			if ($q > $p) {
				$final_show .= substr($formalized_show, $p, $q-$p);
				$p = $q;
			}
			if ($p < $L && $formalized_show[$p] == '*') {
				$final_show .= ($f1) ? '<strong>' : '</strong>' ;
				$f1 = !$f1;
				$p++;
			} else if ($p < $L && $formalized_show[$p] == '|') {
				$final_show .= ($f2) ? '<span class="light">' : '</span>' ;
				$f2 = !$f2;
				$p++;
			}
		}

		return $final_show;
	}

	/**
	 * isLetterOrDigit
	 * @access private
	 * @param string $c
	 * @return boolean
	 */
	private function isLetterOrDigit($c)
	{
		return (($c >= '0' && $c <= '9') || ($c >= 'a' && $c <= 'z') || ($c >= 'A' && $c <= 'Z'));
	}
}

TemplatesAssemble::$code = new Code();
TemplatesAssemble::$orth = new Orthography();
