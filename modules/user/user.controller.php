<?php
/**
 * @file user.controller.php
 * @brief Contains the controller of the user module
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @defgroup user_module Users
 * @ingroup modules security
 *
 * Module for the management of the system users
 */

/**
 * @ingroup user_module
 * @brief User module controller
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class userController extends controller {

	/**
	 * module's administration privilege class 
	 */
	private $_class_privilege;

	/**
	 * module's administration privilege id 
	 */
	private $_admin_privilege;

	/**
	 * @brief Constructs a user controller instance 
	 * 
	 * @return user controller instance
	 */
	function __construct() {

		parent::__construct();

		$this->_cpath = dirname(__FILE__);
		$this->_mdl_name = "user";

		// privileges
		$this->_class_privilege = $this->_mdl_name;
		$this->_admin_privilege = 1;
	}
	
	/**
	 * @brief System users backoffice 
	 * 
	 * @access public
	 * @return the system users back office
	 */
	public function manage() {
	
		access::check($this->_class_privilege, $this->_admin_privilege, array("exitOnFailure"=>true));

		$s_fields = array(
			"password"=>array(
				"type"=>"password"
			),
			"groups"=>array(
				"type"=>"multicheck",
				"required"=>true,
				"value_type"=>'int',
				"table"=>TBL_SYS_GROUPS,
				"field"=>"label",
				"where"=>null,
				"order"=>"id"
			),
			'active'=>array(
				'type'=>'bool',
				'required'=>true,
				'true_label'=>__('yes'),
				'false_label'=>__('no')
			)
		);

    $helptext = isset($_GET['edit']) ? __("userEditPwdLabel") : '';

    $fields_labels = array(
      'password' => array(
        'label' => __('password'),
        'helptext' => $helptext
      )
    );

    $edit_deny = $this->_registry->user->id === 1 ? array() : array(1);

		$at = new adminTable(TBL_USERS, array("edit_deny" => $edit_deny));
		$at->setSpecialFields($s_fields);
    $at->setFieldsLabels($fields_labels);

		$table = $at->manage();

		$this->_view->setTpl('manage_table');
		$this->_view->assign('title', __("ManageTable")." ".TBL_USERS);
		$this->_view->assign('table', $table);

		return $this->_view->render();
	}

}

?>
