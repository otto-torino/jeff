<?php
/**
 * @file template.factory.php
 * @brief Contains template factory class
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @defgroup templates Global templates
 *
 * The global templates are those which renders the whole document starting from <html> to </html>, they stays in the theme folder and are called by the template factory
 * basing upon url request and user authentication.
 *
 * The global templates are file with extension '.tpl'. Are written in html with some special tags parsed by the template engine. \n
 * Special tags are rounded by {} parenthesis. Jeff supports only some tags by now, but they can be easily extended to perform more actions. \n 
 * In particular Jeff base has two kinds of special tags:
 * - **Variable tags**
 * are tags like {JAVASCRIPT} or {TITLE}. When the template engine parses the template file they are replaced by some values or functions. \n
 * For example the tag {JAVASCRIPT} will be replaced with the inclusions of javascript files stored in the @ref registry->js properties.\n 
 * The tag {TITLE} will be replaced with the value of the @ref registry->title property, that is why it's possible to set the application title depending on the module called by url.\n
 * This variables are parsed in the parseVariables method of the template class (called by the public method parse).
 * - **Module's methods inclusion tags**
 * are tags used to insert module methods generated content inside the template. For example: \n
 * 	{module:page method:view params:credits}<br />
 * when the template is parsed this tag is replaced by what returns the view method of the page module passing it the parameter 'credits', 
 * it's possible to pass also more parameters in the same string separating them by a character and then implement the parameters separation in the method called. 
 * This tags are parsed in the template::parseModules method (called by the public method parse).
 */

/**
 * @ingroup templates core
 * @brief Factory Class which creates concrete templates objects
 *
 * This is an abstract class which operates to return specific template objects depending on some logic, 
 * which in the default framework is based over admin/logged/unlogged user states.
 *
 * It's very easy here to add your desired logic and call different templates basing upon your needs, 
 * for example you may return different templates basing upon url request.
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
abstract class templateFactory {
	
	/**
	 * @brief Creation of the global template object 
	 * 
	 * @return the template object or error
	 */
	public static function create() {

		$registry = registry::instance();

		if($registry->site == 'admin') {
			$tpl = access::check('main', $registry->admin_view_privilege) ? "admin_private" : "admin_public";
		}
		elseif($registry->site == 'main') {
			if($registry->user->id) $tpl = $registry->isHome ? "home_private" : "page_private";
			elseif($registry->isHome) $tpl = 'home_public'; 
			else $tpl = 'page_public';
		}

		$registry->theme->setTpl($tpl);

		$tplObj = $registry->theme->getTemplate();

		if($tplObj) return $tplObj;
		else
			Error::syserrorMessage('templateFactory', 'create', sprintf(__("CantChargeTplError"), $tpl), __LINE__);

	}

}

?>
