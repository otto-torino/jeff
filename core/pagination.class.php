<?php
/**
 * @file pagination.class.php
 * @brief Contains the pagination class.
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @ingroup core
 * @brief class used to manage paging of lists or content 
 * 
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class pagination {

	/**
	 * @brief a @ref view instance 
	 */
	private $_view;

	/**
	 * @brief elements for page 
	 */
	private $_range;

	/**
	 * @brief get parameter used to pass page number 
	 */
	private $_urlp;

	/**
	 * @brief current page number
	 */
	private $_actual;
	
	/**
	 * @brief last page number
	 */
	private $_last;

	/**
	 * @brief first element index of the current page
	 */
	private $_start;

	/**
	 * @brief last element index of the current page
	 */
	private $_end;
	
	/**
	 * @brief total number of items
	 */
	private $_tot;
	
	/**
	 * @brief number of pages displayed next to the current page in the page navigation  
	 */
	private $_npages;
	
	/**
	 * Constructs a pagination instance 
	 * 
	 * @param int $ifp items for page 
	 * @param int $tot total number of items
	 * @param array $opts 
	 *   Asociative array of options:
	 *   - **urlp**: string default 'p'. The get parameter used to pass page values
	 *   - **npages**: int default 2. number of pages displayed next to the current page in the page navigation
	 *   - **permalink**: bool default true. Whether to use permalinks whene generating the page navigation links
	 * @return void
	 */
	function __construct($ifp, $tot, $opts=null) {

		$this->_view = new view();

		$this->_urlp = gOpt($opts, 'urlp', 'p');
		$this->_npages = gOpt($opts, 'npages', 2);
		$this->_permalink = gOpt($opts, 'permalink', true);

		$this->_range = $ifp;
		$this->_tot = $tot;
		$this->_last = ceil($this->_tot/$this->_range);
		
		$this->_actual = $this->actual();
		$this->_start = $this->start()+1;
		$this->_end = ($this->_start + $this->_range - 1) > $this->_tot ? $this->_tot : $this->_start + $this->_range - 1;
		

	}
	
	/**
	 * @brief Returns the current page number
	 * 
	 * @return int current page number
	 */
	public function actual() {
	
		$actual = isset($_REQUEST[$this->_urlp]) ? cleanVar($_REQUEST[$this->_urlp], 'int') : 1;

		return $actual < 1 ? 1 : ($actual > $this->_last ? $this->_last : $actual);
	}
	
	/**
	 * @brief Returns the key of the first item of the current page
	 * 
	 * @return int first item key
	 */
	public function start() {

		$start = ($this->_actual - 1) * $this->_range;
		
		return $start;
	}
	
	/**
	 * @brief Returns the total number of items
	 * 
	 * @return int total number of items
	 */
	public function total() {

		return $this->_tot;

	}
	
	/**
	 * @brief Returns the key of the last item of the current page
	 * 
	 * @return int last item key
	 */
	public function limit() {

		return ($this->start() + $this->_range) > $this->_tot ? $this->_tot : $this->start() + $this->_range;

	}
	
	/**
	 * @brief Pagination summary 
	 * 
	 * Returns something like 'items 5-20 of 200'
	 *
	 * @access public
	 * @return string pagination summary
	 */
	public function summary() {

		if(!$this->_last) return null;

		$this->_view->setTpl('pagination_summary');
		$this->_view->assign('start', $this->_start);
		$this->_view->assign('end', $this->_end);
		$this->_view->assign('total', $this->_tot);

		return $this->_view->render();
	}

	/**
	 * @brief Navigation links
	 *
	 * @access public
	 * @return string page navigation
	 */
	public function navigation() {
		
		if($this->_last == 1) return "";
		
		$clean_uri = $this->_permalink 
			? preg_replace("#".$this->_urlp."/[0-9]*/#", "", $_SERVER['REQUEST_URI'])
			: preg_replace("#(&|\?)".$this->_urlp."=[0-9]*#", "", $_SERVER['REQUEST_URI']);
		preg_match("#(.*?)(/)([^/]*)$#", $clean_uri, $matches);

		if($this->_permalink) {
			$base_link = $matches[1]."/".$this->_urlp."/";
			$params = isset($matches[3]) ? $matches[3] : '';
		}
		else {
			$base_link = $matches[1]."/";
			$params = (isset($matches[3]) && $matches[3]) ? $matches[3]."&".$this->_urlp."=" : "?".$this->_urlp."=";
		}

		$pages = array();
		for($i=1; $i<$this->_last+1; $i++) {
			if($i == 1 || abs($this->_actual - $i) < ($this->_npages+1) || $i == $this->_last) {
				$page = array('number'=>$i);
				if($this->_actual != $i) 
					$page['link'] = $this->_permalink ? $base_link.$i."/".$params : $base_link.$params.$i;
				if($this->_actual == $i) $page['selected'] = true;
				$pages[] = $page;
			}
			elseif(abs($this->_actual - $i) == $this->_npages + 1) {
				$page = array('number'=>'GAP');
				$pages[] = $page;
			}
		}

		$this->_view->setTpl('pagination_navigation');
		$this->_view->assign('pages', $pages);
		$prev_p = $this->_actual == 1 
			? null 
			: anchor(($this->_permalink ? $base_link.($this->_actual-1)."/".$params : $base_link.$params.($this->_actual-1)), "<--");
		$next_p = $this->_actual == $this->_last 
			? null : 
			anchor(($this->_permalink ? $base_link.($this->_actual+1)."/".$params : $base_link.$params.($this->_actual+1)), "-->");
		$this->_view->assign('prev', $prev_p);
		$this->_view->assign('next', $next_p);

		return $this->_view->render();

	}

}

?>
