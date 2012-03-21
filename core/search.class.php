<?php
/**
 * @file search.class.php
 * @brief Contains the search class.
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @ingroup core
 * @brief Full text search tool (mysql DBMS)
 *
 * This class is used to perform full text seraches optionally indicating different weights 
 * for different search fields.
 *
 * In order to work correctly the database engine must have defined the following function which is 
 * a case insensitive replace function. here is the SQL code necessary to create the replace_ci function:
 *
 * 	DELIMITER $$
 * 	DROP FUNCTION IF EXISTS `replace_ci`$$
 * 	CREATE FUNCTION `replace_ci` ( str TEXT,needle CHAR(255),str_rep CHAR(255)) 
 * 	RETURNS TEXT
 * 	DETERMINISTIC
 * 	BEGIN
 * 	DECLARE return_str TEXT;
 * 	SELECT replace(lower(str),lower(needle),str_rep) INTO return_str;
 * 	RETURN return_str;
 * 	END$$
 * 	DELIMITER ;
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class search {

	/**
	 * @brief the @ref registry singleton instance 
	 */
	private $_registry;
	
	/**
	 * @brief the database table 
	 */
	private $_table;

	/**
	 * @brief Constructs a search instance 
	 * 
	 * @param string $table table name
	 * @param mixed $opts 
	 *   associative array of options:
	 *   - **highlight_range**: int default 120. Number of characters which surrounds search keyword in the search result
	 * @return void
	 */
	function __construct($table, $opts=null) {
	
		$this->_registry = registry::instance();
		$this->_table = $table;
		$this->_highlight_range = gOpt($opts, 'highlight_range', 120);

	}

	/**
	 * @brief Clear the search string
	 *
	 * Removes words with no meaning
	 * 
	 * @param string $search_string search string
	 * @return the cleaned search string
	 */
	private function clearSearchString($search_string) {

		$unconsidered = array("lo", "l", "il", "la", "i", "gli", "le", "uno", "un", "una", "un", "su", "sul", "sulla", "sullo", "sull", "in", "nel", "nello", "nella", "nell", "con", "di", "da", "dei", "d",  "della", "dello", "del", "dell", "che", "a", "dal", "è", "e", "per", "non", "si", "al", "ai", "allo", "all", "al", "o", "the", "a", "an", "on", "in", "with", "of", "which", "that", "is", "for", "to");

		$clean_string = strtolower($search_string);

		$clean_string = preg_replace("#\b(".preg_quote(implode("|", $unconsidered)).")\b#", "", $clean_string);
		$clean_string = preg_replace("#\W|(\s+)#", " ", $clean_string);

		$clean_string = preg_quote($clean_string);
	
		return $clean_string;

	}

	/**
	 * @brief Gets keywords from a search string
	 * 
	 * @param string $search_string search string
	 * @return array keywords list
	 */
	private function getKeywords($search_string) {
		
		$clean_string = $this->clearSearchString($search_string);

		$empty_array = array(""," ");

		return  array_diff(array_unique(explode(" ", $clean_string)), $empty_array);

	}

	/**
	 * @brief Creation of the search query 
	 * 
	 * @param array $selected_fields fields to select. Each array element may be the field name or an array with the field name as value of the key 'field'.
	 * @param array $required_clauses 
	 *   associative array of required clauses in the form array('field_name'=>'field_clause')<br />
	 *   'field_clause' may be directly the field value to search for or an associative array specifying the 
	 *   search type (inside, start, end or field) and the value:
	 *   - **inside**: bool. Matches fields which contain value
	 *   - **start**: bool. Matches fields which starts with value
	 *   - **end**: bool. Matches fields which ends with value
	 *   - **field**: bool. matches fields equal to value
	 *   - **value**: mixed. field value
	 * @param array $weight_clauses 
	 *   associative array of weighted clauses in the form array('field_name'=>'field_clause')<br />
	 *   'field_clause' is an associative array:
	 *   - **value**: the string to search for (will be divided into keywords)
	 *   - **inside** bool: Wheather to match also words which contains a keyword 
	 *   - **weight** int: field weight 
	 * @return the search query string
	 */
	public function makeQuery($selected_fields, $required_clauses, $weight_clauses){
	
		$final_keywords = 0;

		$selected = array();
		foreach($selected_fields as $f) {
			$selected[] = is_array($f) ? $f['field'] : $f;
		}
		$relevance = "(";
		$occurrences = "(";
		$sqlwhere_r = "";
		$sqlwhere_w = "";
		$sql_where = '';
		foreach($required_clauses as $f=>$fp) {
			if(is_array($fp)) {
				if(isset($fp['inside']) && $fp['inside']) $sqlwhere_r .= "$f LIKE '%".$fp['value']."%' AND ";
				elseif(isset($fp['begin']) && $fp['begin']) $sqlwhere_r .= "$f LIKE '".$fp['value']."%' AND ";
				elseif(isset($fp['end']) && $fp['end']) $sqlwhere_r .= "$f LIKE '%".$fp['value']."' AND ";
				elseif(isset($fp['field']) && $fp['field']) $sqlwhere_r .= "$f=".$fp['value']." AND ";
				else $sqlwhere_r .= "$f='".$fp['value']."' AND ";
			}
			else {
				$sqlwhere_r .= "$f='$fp' AND ";
			}
		}
		foreach($weight_clauses as $f=>$fp) {
			$search_keywords = $this->getKeywords($fp['value']);
			$final_keywords += count($search_keywords);

			foreach($search_keywords as $keyw) {
				$occurrences .= "IFNULL(((LENGTH($f)-LENGTH(replace_ci($f,'$keyw','')))/LENGTH('$keyw')), 0) + ";
				if(isset($fp['inside']) && $fp['inside']) {
					$relevance .= "(INSTR(`$f`, '".$keyw."')>0)*".$fp['weight']." + ";
					$sqlwhere_w .= "`$f` LIKE '%".$keyw."%' OR ";
				}
				else {
					$relevance .= "IFNULL(((`$f` REGEXP '[[:<:]]".$keyw."[[:>:]]')>0)*".$fp['weight'].", 0) + ";
					$sqlwhere_w .= "`$f` REGEXP '[[:<:]]".$keyw."[[:>:]]' OR ";
				}
			}
		}
		if($final_keywords) $sqlwhere_w = substr($sqlwhere_w, 0, strlen($sqlwhere_w)-4);
		$relevance .= "0)";
		$occurrences .= "0)";
		if($sqlwhere_r || $sqlwhere_w) {
			$sqlwhere = "WHERE ";
			if($sqlwhere_r) $sqlwhere .= $sqlwhere_r;
			if($sqlwhere_w) $sqlwhere .= "(".$sqlwhere_w.")";
			else $sqlwhere = substr($sqlwhere, 0, strlen($sqlwhere)-5);
		}
		$query = "SELECT ".implode(",", $selected).", $relevance AS relevance, $occurrences AS occurrences FROM $this->_table $sqlwhere ORDER BY relevance DESC, occurrences DESC";
			
		return $final_keywords ? $query : false;

	}

	/**
	 * @brief Return the search results
	 *
	 * The returned text is an array whose elements are associative arrays with the following keys:
	 * - **relevance** relevance of the result (due to field weights)  
	 * - **occurences** keywords occurences  
	 * - **<selected_field_name>** search result:<br /> 
	 *   if the selected field has the option 'highlight' returns the highlighted text (the first occurrence of a keyword surrounded by two highlight ranges) if found or an empty string.<br />
	 *   If the option highlight is false returns the content stored in the database. 
	 * 
	 * @param array $selected_fields fields to select. Each array element may be the field name or an array:
	 *   - **highlight**: whether to highlight search keywords in the result or not
	 *   - **field**: the field name
	 * @param array $required_clauses 
	 *   associative array of required clauses in the form array('field_name'=>'field_clause')<br />
	 *   'field_clause' may be directly the field value to search for or an associative array specifying the 
	 *   search type (inside, start, end or field) and the value:
	 *   - **inside**: bool. Matches fields which contain value
	 *   - **start**: bool. Matches fields which starts with value
	 *   - **end**: bool. Matches fields which ends with value
	 *   - **field**: bool. matches fields equal to value
	 *   - **value**: mixed. field value
	 * @param array $weight_clauses 
	 *   associative array of weighted clauses in the form array('field_name'=>'field_clause')<br />
	 *   'field_clause' is an associative array:
	 *   - **value**: the string to search for (will be divided into keywords)
	 *   - **inside** bool: Wheather to match also words which contains a keyword 
	 *   - **weight** int: field weight
	 * @return array of search results
	 */
	public function getSearchResults($selected_fields, $required_clauses, $weight_clauses) {
	
		$res = array();

		$query = $this->makeQuery($selected_fields, $required_clauses, $weight_clauses);

		if($query===false) return array();
		$rows = $this->_registry->db->queryResult($query);
		if(sizeof($rows)>0) {
			$i = 0;
			foreach($rows as $row) {
				$res[$i] = array(); 
				foreach($selected_fields as $f) {
					$res[$i]['relevance'] = $row['relevance'];
					$res[$i]['occurrences'] = $row['occurrences'];
					if(is_array($f) && isset($f['highlight']) && $f['highlight']) {
						$fp = $weight_clauses[$f['field']];
						$get_search_keywords = $this->getKeywords($fp['value']);
						$search_keywords = array();
						foreach($get_search_keywords as $kw) {
							$search_keywords[] = preg_quote($kw);
						}
						$rexp = (isset($fp['inside']) && $fp['inside']) 
							? implode("|", $search_keywords) 
							: "\b".implode("\b|\b", $search_keywords)."\b";
						if(preg_match("#(.){0,$this->_highlight_range}($rexp)(.){0,$this->_highlight_range}#sui", cutHtmlText($row[preg_replace("#.*?\.#", "", $f['field'])], 50000000, '', true, false, true), $matches)) {
							$res[$i][$f['field']] = preg_replace("#".$rexp."#i", "<span class=\"evidence\">$0</span>", $matches[0]);
						}
						else $res[$i][$f['field']] = '';
					}
					else $res[$i][$f] = $row[preg_replace("#.*?\.#", "", $f)];
				}
				$i++;
			}
		}

		return $res;
	
	}


}

?>
