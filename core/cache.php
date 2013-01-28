<?php
/**
 * @file cache.php
 * @brief Contains the \ref cache, outputCache and dataCache classes.
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @defgroup cache Outputs and data caching
 * Library to store outputs (text, html, xml) and data structures writing to file
 */

/**
 * @ingroup cache core
 * @brief cache super class
 *
 * Provides methods to handle file operations.
 * 
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class cache {

	/**
	 * @brief directory separator 
	 */
	protected $_ds;
	
	/**
	 * @brief absolute path to the cache folder storing all the cache files 
	 */
	protected $_fld;
	
	/**
	 * @brief cache files prefix 
	 */
	protected $_prefix;
	
	/**
	 * @brief cache content group 
	 */
	protected $_grp;
	
	/**
	 * @brief cache content identifier 
	 */
	protected $id;
	
	/**
	 * @brief caching time 
	 */
	protected $_tc;
	
	/**
	 * @brief status 
	 */
	protected $_enabled;

	/**
	 * @brief Construct a cache instance 
	 * 
	 * @return cache instance
	 */
	function __construct() {

		$this->_ds = DS;
		$this->_fld = ABS_CACHE;
		$this->_prefix = 'cache_';
		$this->_enabled = true;
	}

	/**
	 * @brief Writes data to file 
	 * 
	 * @param string $data 
	 * @return void
	 */
	protected function write($data) {

		$filename = $this->getFilename();

		if($fp = @fopen($filename, "xb")) {
			if(flock($fp, LOCK_EX)) fwrite($fp, $data);
			fclose($fp);
			touch($filename, time());
		}

	}

	/**
	 * @brief Reads data from file 
	 * 
	 * @return data in string format
	 */
	protected function read() {
		
		return file_get_contents($this->getFilename());

	}

	/**
	 * @brief Gets the cached filename 
	 * 
	 * @return the filename
	 */
	protected function getFilename() {

		return $this->_fld . $this->_ds . $this->_prefix . $this->_grp ."_". md5($this->_id);

	}

	/**
	 * @brief Cheks if data are cached and not expired 
	 * 
	 * @return true if data are cached and the cache time is not expired, false otherwise
	 */
	protected function isCached() {

		$filename = $this->getFilename();
		if($this->_enabled && file_exists($filename) && time() < (filemtime($filename) + $this->_tc)) return true; 
		else @unlink($filename);
			
		return false;

	}

}

/**
 * @ingroup cache core
 * @brief Cache implemetation to store html/text/xml outputs
 * 
 * ### Usage
 *
 * ~~~~~~~~~~~~~~~~~~~{.php}
 * $buffer = "previous text-";
 * $cache = new outputCache($buffer);
 * if($cache->start("group_name", "id", 3600)) {
 *	
 *	$cache_buffer = "some content-";
 *
 *	$cache->stop($cache_buffer);
 *
 * }
 * $buffer .= "next text";
 * ~~~~~~~~~~~~~~~~~~~
 *
 * the result is: 
 * ~~~~~~~~~~~~~~~~~~~{.php}
 * $buffer = "previous text-somec content-next text";
 * ~~~~~~~~~~~~~~~~~~~
 *
 * if the content is cached the if statement is skipped and the content is
 * concatenated to $buffer,
 * if content is not cached the if statemet runs, the content is prepared
 * and then saved in cache and added to $buffer (through stop method)
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class outputCache extends cache {

	/**
	 * @brief reference to the output to which add the cached data 
	 */
	protected $_buffer;

	/**
	 * @brief Constructs a outputCache instance 
	 * 
	 * @param mixed &$buffer reference to the $_buffer variable 
	 * @param mixed $enable status
	 * @return outputCache instance
	 */
	function __construct(&$buffer, $enable = true) {

		parent::__construct();
		$this->_buffer = &$buffer;
		$this->_enabled = $enable;
	}

	/**
	 * @brief Starts the caching process 
 	 *
	 * If data are cached and not expired adds data to the $_buffer member and returns false, else returns true.
	 * 
	 * @param string $grp the output data group
	 * @param mixed $id the output data identifier
	 * @param mixed $tc the caching time
	 * @return false if data are already cached and not expired, true otherwise
	 */
	public function start($grp, $id, $tc) {
	
		$this->_grp = $grp;
		$this->_id = $id;
		$this->_tc = $tc;

		if($this->isCached()) {
			$this->_buffer .= $this->read();
			return false;
		}
		
		return true;

	}

	/**
	 * @brief Stops the caching process
	 *
	 * Writes data to file and adds it to the $_buffer member.
	 * 
	 * @param string $data the output data
	 * @return void
	 */
	public function stop($data) {
		
		if($this->_enabled) $this->write($data);
		$this->_buffer .= $data;

	}

}

/**
 * @ingroup cache core
 * @brief Cache implementation to store data structures
 * 
 * ### Usage
 *
 * ~~~~~~~~~~~~~~~~~~~{.php}
 * $cache = new dataCache();
 * if(!$data = $cache->get('group_name', 'id', 3600)) {
 *
 *	$data = someCalculations();
 *	$cache->save($data);
 *
 *}
 * ~~~~~~~~~~~~~~~~~~~
 *
 * if data is stored it's returned by get method and if statement is not processed, otherwise data 
 * is calculated and saved in cache
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class dataCache extends cache {

	/**
	 * @brief Constructs a dataCache instance 
	 * 
	 * @param mixed $enable status
	 * @return dataCache instance
	 */
	function __construct($enable = true) {

		parent::__construct();
		
		$this->_enabled = $enable;

	}
	
	/**
	 * @brief Tries to retrieve cached data 
	 *
	 * If data are cached and not expired returns them, else returns false.
	 * 
	 * @param string $grp the output data group
	 * @param mixed $id the output data identifier
	 * @param mixed $tc the caching time
	 * @return data if are already cached and not expired, false otherwise
	 */
	public function get($grp, $id, $tc) {
	
		$this->_grp = $grp;
		$this->_id = $id;
		$this->_tc = $tc;

		if($this->isCached()) return unserialize($this->read());
		return false;

	}
	
	/**
	 * @brief Saves data in cache
	 *
	 * @param string $data the data to put in cache
	 * @return void
	 */
	public function save($data) {
		
		if($this->_enabled) $this->write(serialize($data));

	}

}

?>
