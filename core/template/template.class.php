<?php
/**
 * @file template.class.php
 * @brief Contains the template class
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @ingroup templates core
 * @brief Class used to manage and parse templates
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class template {

    /**
     * @brief the @ref registry singleton instance
     */
    protected $_registry;

    /**
     * @brief template file path
     */
    protected $_path;

    /**
     * @brief content outputted by the method called through url
     */
    protected $_mdl_url_content;

    /**
     * @brief Constructs a template instance
     * 
     * @param string $tpl_path template file path
     * @return void
     */
    function __construct($tpl_path) {

        $this->_registry = registry::instance();
        $this->_path = $tpl_path;
    }

    /**
     * @brief Template file getter
     * 
     * @return string template file path
     */
    public function getPath() {

        return $this->_path;

    }

    /**
     * @brief Template parser
     *
     * Parses the tempate file and replaces variables and module's outputs
     * 
     * @return void
     */
    public function parse() {

        if(!is_readable($this->getPath())) {
            JeffError::syserrorMessage('template', 'parse', sprintf(__("TplNotFound"), $this->getPath()), __LINE__);
        }

        $mdl_url_content = $this->_registry->router->loader(null);
        $registry = $this->_registry;

        ob_start();
        include($this->getPath());
        $buffer .= ob_get_contents();
        ob_clean();

        return $buffer;

    }

}
