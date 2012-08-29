<?php
/**
 * @file language.controller.php
 * @brief Contains the controller of the language module
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @defgroup localization Localization
 *
 * Jeff supports localization. Strings in different languages are translated in themes localization files and retrieved by their key identifier.
 *
 * The localization function @ref __() returns the string translated in the active language.
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
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class languageController extends controller {
	
	/**
	 * module's administration privilege class 
	 */
	private $_class_privilege;

	/**
	 * module's administration privilege id 
	 */
	private $_admin_privilege;

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

		$this->_view->setTpl('language_choose');
		$this->_view->assign('lngs', $lngs);

		return $this->_view->render();
	}

}

?>
