<?php
/**
 * \file modules/index/index.php
 * \brief Index module's model.
 *
 * \author abidibo abidibo@gmail.com
 * \version 0.98
 * \date 2011-2012
 * Otto srl MIT License \see http://www.opensource.org/licenses/mit-license.php
 */

class index extends model {

	public $title,$text,$image;

	function __construct($id) {

		parent::__construct();

		$this->init($id);
	}

	private function init($id) {

		$this->title = null;
		$this->text = null;
		$this->image = null;

	}

}
 
?>
