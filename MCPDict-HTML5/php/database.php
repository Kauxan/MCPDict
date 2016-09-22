<?php

/**
* Database [SQLite3]
*/
class Database
{
	/**
	 * @var string database-dump filepath
	 * @access private
	 */
	private $_dn_name = "../db/mcpdict.db";

	/**
	 * @var database-file handle
	 * @access private
	 */
	private $_db_handle;

	/**
	 * @var string(1) $query_as mode
	 * @access protected
	 */
	protected $query_as;

	/**
	 * @var array(3) $query_flag boolean-string
	 * @access protected
	 */
	protected $query_flag;

	/**
	 * constructor
	 * @access public
	 * @param string $query_as
	 * @param array $query_flag
	 * @return boolean
	 */
	public function __construct($query_as, $query_flag)
	{
		set_time_limit(60);
		$this->_db_handle = new SQLite3($this->_dn_name);
		$this->query_as = $query_as;
		$this->query_flag = $query_flag;
		return ($this->_db_handle) ? true : false ;
	}

	/**
	 * destructor
	 * @access public
	 * @return 
	 */
	public function __destruct()
	{
		$this->_db_handle->close();
	}

	/**
	 * public interface for query
	 * @access public
	 * @param array $query_item
	 * @return array $query_result
	 */
	public function mcpdict_query($query_item)
	{
		$query_result = array();
		if ($this->query_as == "0") {
			return $this->mcpdict_query_hz($query_item);
		}
		return $query_result;
	}

	/**
	 * submission-query with mode="0"
	 * @access private
	 * @param array $query_item
	 * @return array $query_result
	 */
	private function mcpdict_query_hz($query_item)
	{
		// fetchArray: SQLITE3_ASSOC or SQLITE3_BOTH
		$query_result = array();
		// KuangxYonh-only
		$KuangxYonh_string = ($this->query_flag[0] == '1') ? "AND c1mc IS NOT NULL" : "" ;
		// remove repeat
		$query_item = array_unique($query_item);
		$unique_unicode = array();
		// TODO: query optimization needed
		// ...
		foreach ($query_item as $value) {
			if (in_array($value, $unique_unicode)) continue;
			$main_string = "SELECT * FROM mcpdict_content WHERE c0unicode='".$value."' ".$KuangxYonh_string;
			$main_result = $this->_db_handle->query($main_string);
			if ($main_row = $main_result->fetchArray(SQLITE3_ASSOC)) {
				$main_row['variants_base_unicode'] = '';
				array_push($query_result, $main_row);
				array_push($unique_unicode, $value);
			}
			// allow Variants
			if ($this->query_flag[1] == '1') {
				$second_string = "SELECT json FROM mcpdict_variants WHERE unicode='".$value."' ";
				$second_result = $this->_db_handle->query($second_string);
				$second_row = $second_result->fetchArray(SQLITE3_ASSOC);
				$variants_arr = json_decode($second_row['json'], true);
				if (!($variants_arr)) continue;
				foreach ($variants_arr as $variants_value) {
					if (in_array($variants_value, $unique_unicode)) continue;
					$variants_string = "SELECT * FROM mcpdict_content WHERE c0unicode='".$variants_value."' ".$KuangxYonh_string;
					$variants_result = $this->_db_handle->query($variants_string);
					if ($variants_row = $variants_result->fetchArray(SQLITE3_ASSOC)) {
						$variants_row['variants_base_unicode'] = $value;
						array_push($query_result, $variants_row);
						array_push($unique_unicode, $variants_value);
					}
				}
			}
		}
		return $query_result;
	}

	/**
	 * get columns in $result
	 * @access private
	 * @param sqlite3-query Object
	 * @return array $columns
	 */
	private function get_columns(&$result)
	{
		$i = 0; 
		while ($result->columnName($i)) {
			$columns[] = $result->columnName($i);
			$i++;
		}
		return $columns;
	}
}
