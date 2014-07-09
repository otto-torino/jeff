<?php
/**
 * @file core.class.php
 * @brief Contains the core class.
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @defgroup core Framework core
 * @brief Classes which forms the core of the framework
 */

/**
 * @ingroup core
 * @brief The core of the web application 
 * 
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class core {

  /**
   * @brief the registry singleton instance 
   */
  protected $_registry;
  
  /**
   * @brief path used to generate links 
   */
  private $_base_path;

  /**
   * @brief Constructs the core instance 
   * 
   * Initializes many registry properties used throughout the framework, checks for session timeout (if active) and checks for installed plugins. 
   * 
   * @return void
   */
  function __construct() {

    session_name(SESSIONNAME);
    session_start();
    require_once(ABS_CORE.DS.'tables.php');
    require_once(ABS_CORE.DS.'include.php');

    $this->_base_path = BASE_PATH;
    
    // initializing registry variable
    $this->_registry = registry::instance();
    $this->_registry->db = DB::instance();
    $this->_registry->url = $_SERVER['REQUEST_URI'];
    $this->_registry->admin_privilege = 1;
    $this->_registry->admin_view_privilege = 2;
    $this->_registry->public_view_privilege = 3;
    $this->_registry->private_view_privilege = 4;
    $this->_registry->theme = $this->getTheme();
    $this->_registry->lng = language::setLanguage($this->_registry);
    $this->_registry->site_settings = new siteSettings($this->_registry);
    $this->_registry->dtime = new dtime($this->_registry);
    $this->_registry->router = new router($this->_base_path);
    $this->_registry->isHome = preg_match("#^module=index&method=index(&.*)?$#", $_SERVER['QUERY_STRING']) ? true : false;
    $this->_registry->css = array();
    $this->_registry->js = array();
    $this->_registry->meta = array();
    $this->_registry->head_links = array();

    $this->loadTranslations();
    $this->setSessionTimeout();
    $this->loadPlugins();

  }

  /**
   * @biref Load applications' translations
   *
   * Loads the default theme translations and all translations files in the [current theme/[current language] directory
   *
   * @return void
   */
  private function loadTranslations() {

    // charge language translations
    // default theme translations
    $trnsl = array();
    if($dft_trnsl_files = scandir(ABS_THEMES.DS.'default'.DS.'languages'.DS.$this->_registry->lng)) {
      foreach($dft_trnsl_files as $file) {
        if(is_file(ABS_THEMES.DS.'default'.DS.'languages'.DS.$this->_registry->lng.DS.$file)) {
          $tr = include(ABS_THEMES.DS.'default'.DS.'languages'.DS.$this->_registry->lng.DS.$file);
          if(is_array($tr)) {
            $trnsl = array_merge($trnsl, $tr);
          }
        }
      }
    }
    if($this->_registry->theme) {
      $theme = $this->_registry->theme;
      $path = $theme->path();
      if(get_class($theme)!= 'defaultTheme') {
        if(is_dir($path.DS.'languages'.DS.$this->_registry->lng) and $trnsl_files = scandir($path.DS.'languages'.DS.$this->_registry->lng)) {
          foreach($trnsl_files as $file) {
            if(is_file($path.DS.'languages'.DS.$this->_registry->lng.DS.$file)) {
              $tr = include($path.DS.'languages'.DS.$this->_registry->lng.DS.$file);
              if(is_array($tr)) {
                $trnsl = array_merge($trnsl, $tr);
              }
            }
          }
        }
      }
    }

    $this->_registry->lng_dict = $trnsl;

  }

  /**
   * @brief Sets a session timeout if set in site settings preferences
   *
   * @return void
   */
  private function setSessionTimeout() {

    //set session timeout
    if($this->_registry->site_settings->session_timeout) {
      if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $this->_registry->site_settings->session_timeout)) {
        // last request was more than timeout seconds ago
        session_regenerate_id(true);
        session_destroy();
        unset($_SESSION);
        session_start();
      }
      $_SESSION['last_activity'] = time(); // update last activity time stamp
    }

  }

  /**
   * @brief Loads existent plugins
   *
   * @return void
   */
  private function loadPlugins() {

    // extra plugins
    $plugins_objs = array();
    if(is_readable(ABS_ROOT.DS.'plugins.php')) {
      require_once(ABS_ROOT.DS.'plugins.php');
      foreach($plugins as $k=>$v) { 
        if(is_readable(ABS_PLUGINS.DS.$k.DS.$k.".php")) {
          require_once(ABS_PLUGINS.DS.$k.DS.$k.".php");
          $plugins_objs[$k] = new $k($this->_registry, $v);
        }
        else 
          exit(error::syserrorMessage(get_class($this), '__construct', sprintf(__("cantFindPluginSource"), $k), __LINE__));
      }
    }
    $this->_registry->plugins = $plugins_objs;
  }

  /**
   * @brief Detects a mobile client 
   * 
   * @return boolean result, true if the client is mobile, false otherwise
   */
  public static function detectMobile() {

    $useragent=$_SERVER['HTTP_USER_AGENT'];
    if(preg_match('/android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|meego.+mobile|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))) {
      return true;
    }

    return false;

  }


  /**
   * @brief Renders the whole document 
   * 
   * @param string $site the requested site: main or admin
   * @return void
   */
  public function renderApp($site=null) {

    ob_start();

    // some other registry properties
    $this->_registry->site = $site=='admin' ? 'admin':'main';

    /*
     * check login/logout
     */
    authentication::check();

    // mobile check is done here, so that if mobile choice is to be made considering the logged in user, the registry user property can be used
    $this->setIsMobile();

    /*
     * create document
     */
    $doc = new document();
    $buffer = $doc->render();

    ob_end_flush();

  }

  /**
   * @brief Returns the output of the class method invoked through the url 
   * 
   * @return void
   */
  public function methodPointer() {

    ob_start();

    /*
     * check login/logout
     */
    authentication::check($this->_registry);

    // mobile check is done here, so that if mobile choice is to be made considering the logged in user, the registry user property can be used
    $this->setIsMobile();

    echo $this->_registry->router->loader(null);
    ob_end_flush();

    exit(); 
  }

  /**
   * @brief Contains the is_mobile condition, sets the is_mobile property of the registry
   * @description Add here your logic if you wanna set mobile only for some users or depending on other things
   * 
   * @return void
   */
  private function setIsMobile() {

    if(!$this->_registry->site_settings->mobile_site) {
      $this->_registry->is_mobile = false;
      return false;
    }

    if(self::detectMobile()) {
      $this->_registry->is_mobile = true;
    }
    else {
      $this->_registry->is_mobile = false;
    }

  }

  /**
   * @brief Retrieves the active theme object. 
   * 
   * @return the active theme or a sys error message
   */
  public function getTheme() {

    $rows = $this->_registry->db->select(array("name"), TBL_THEMES, array('active' => 1), '');
    $theme_name = $rows[0]['name'];

    if(is_readable(ABS_THEMES.DS.$theme_name.DS.$theme_name.'.php'))
      require_once(ABS_THEMES.DS.$theme_name.DS.$theme_name.'.php');
    else 
      Error::syserrorMessage('coew', 'getTheme', sprintf("Can't load theme %s", $theme_name), __LINE__);

    $theme_class = $theme_name.'Theme';

    if(class_exists($theme_class))
      return new $theme_class();
    else 
      Error::syserrorMessage('coew', 'getTheme', sprintf("Can't load theme %s", $theme_name), __LINE__);

  }

}

?>
