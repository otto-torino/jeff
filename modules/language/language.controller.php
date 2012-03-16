<?php
/**
 * @file language.controller.php
 * @brief Contains the controller of the language module
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.98
 * @date 2011-2012
 * @copyright Otto srl MIT License \see http://www.opensource.org/licenses/mit-license.php
 */

/**
 * @defgroup localization Localization
 *
 * <p>Jeff supports localization. Strings in different languages are translated in themes localization files and retrieved by their key identifier.</p>
 * <p>The localization function @ref __() returns the string translated in the active language.</p>
 */

/**
 * @defgroup language_module Language
 * @ingroup modules localization
 *
 * Module for the management of the system languages
 */

/**
 * @ingroup language_module
 * @brief Language module controller
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.98
 * @date 2011-2012
 * @copyright Otto srl MIT License \see http://www.opensource.org/licenses/mit-license.php 
 */
class languageController extends controller {
	
	/**
	 * @brief Constructs a language controller instance 
	 * 
	 * @return language controller instance
	 */
	function __construct() {

		parent::__construct();

		$this->_cpath = dirname(__FILE__);
		$this->_mdl_name = "language";

		// privileges
		$this->_class_privilege = $this->_mdl_name;

		/**
 		 * Module's administration privilege  
 		 */
		$this->_admin_privilege = 1;
	}
	
	/**
	 * @brief Language module backoffice 
	 * 
	 * @access public
	 * @return the language table back office
	 */
	public function manage() {
	
		access::check($this->_class_privilege, $this->_admin_privilege, array("exitOnFailure"=>true));

		$s_fields = array(
			"main"=>array(
				"type"=>"bool",
				"required"=>true,
				"true_label"=>__("yes"),
				"false_label"=>__("no")
			),
			"active"=>array(
				"type"=>"bool",
				"required"=>true,
				"true_label"=>__("yes"),
				"false_label"=>__("no")
			)
		);

		$at = new adminTable(TBL_LNG);
		$at->setSpecialFields($s_fields);
		$table = $at->manage();

		$this->_view->setTpl('manage_table');
		$this->_view->assign('title', __("ManageTable")." ".TBL_LNG);
		$this->_view->assign('table', $table);

		return $this->_view->render();
	}

	/**
	 * @brief Choose language view 
	 * 
	 * @return the choose language view
	 */
	public function choose() {
	
		$active_lngs = language::get($this->_registry, array('where'=>"active='1'"));
		
		$lngs = array();

		foreach($active_lngs as $l) {
			$href = "?lng=".$l->code;
			$link = anchor($href, htmlVar($l->language));
			$lngs[] = $link;
		}

		return implode(" | ", $lngs);
	}

}

?>
