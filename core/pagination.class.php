<?php

class pagination {

	private $_view;
	private $_range, $_urlp;

	private $_actual, $_last;
	private $_start, $_end, $_tot;
	
	private $_npages;
	
	function __construct($registry, $ifp, $tot, $opts=null) {

		$this->_view = new view($registry);

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
	
	public function actual() {
	
		$actual = isset($_REQUEST[$this->_urlp]) ? cleanVar($_REQUEST[$this->_urlp], 'int') : 1;

		return $actual < 1 ? 1 : ($actual > $this->_last ? $this->_last : $actual);
	}

	public function start() {

		$start = ($this->_actual - 1) * $this->_range;
		
		return $start;
	}
	
	public function total() {

		return $this->_tot;

	}

	public function limit() {

		return ($this->start() + $this->_range) > $this->_tot ? $this->_tot : $this->start() + $this->_range;

	}
	
	public function summary() {

		if(!$this->_last) return null;

		$this->_view->setTpl('pagination_summary');
		$this->_view->assign('start', $this->_start);
		$this->_view->assign('end', $this->_end);
		$this->_view->assign('total', $this->_tot);

		return $this->_view->render();
	}

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
