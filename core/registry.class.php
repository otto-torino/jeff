<?php
/**
 * @file registry.class.php
 * @brief Contains the registry class.
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @ingroup core
 * @brief Global framework registry
 *
 * The registry object acts through all the application like a singleton dictionary.
 *
 * Basically it has a setter and a getter methods by which other objects my create new registry properties.
 * Since the registry is a singleton instance, each of them has access to the properties setted by the others 
 * through the __get method.
 *
 * Hence the registry is a **global dictionary** (actually an associative array, but I'll call it dictionary 
 * since it's what it represents) used to store public and sharable properties and objects.
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
class registry extends singleton {

    /*
     * @brief array containing the registry variables
     */
    private $vars = array();

    /**
     * @brief Sets a registry variable 
     * 	
     * @param string $index variable name 
     * @param mixed $value variable value
     * @return void
     */
    public function __set($index, $value) {
            $this->vars[$index] = $value;
    }

    
    /**
     * @brief Adds css file paths to registry 
     * 
     * @param string $css relative path 
     * @return void
     */
    public function addCss($css) {
        $this->vars['css'][] = $css;	
    }

    /**
     * @brief Retrieve html code for style tags 
     * 
     * @return html code
     */
    public function cssHtml() {
        $r = '';
        foreach(array_unique($this->css) as $css) {
            $r .= "<link rel=\"stylesheet\" href=\"$css\" type=\"text/css\" />\n";
        }
        return $r;
    }
       
    /**
     * @brief Adds js file paths to registry 
     * 
     * @param string $js relative path
     * @return void
     */
    public function addJs($js) {
        $this->vars['js'][] = $js;
    }

    /**
     * @brief Retrieve html code for script tags 
     * 
     * @return html code
     */
    public function jsHtml() {
        $r = '';
        foreach(array_unique($this->js) as $js) {
            $r .= "<script type=\"text/javascript\" src=\"$js\"></script>\n";
        }
        return $r;
    }
       
    /**
     * @brief Adds meta tags to the registry 
     * 
     * @param array $meta 
     *   associative array:
     *   - **name**: name attribute 
     *   - **property**: property attribute 
     *   - **content**: content attribute 
     * @return void
     */
    public function addMeta($meta) {
        $this->vars['meta'][] = $meta;	
    }

    /**
     * @brief Retrieve html code for meta tags 
     * 
     * @return html code
     */
    public function metaHtml() {
        $r = '';
        foreach($this->meta as $meta) { 
            $r .= "<meta"
                .(isset($meta['name']) ? " name=\"".$meta['name']."\"" : '')
                .(isset($meta['property']) ? " property=\"".$meta['property']."\"" : '')
                ." content=\"".$meta['content']."\" />\n";
        }
        return $r;
    }
 
       
    /**
     * @brief Adds link tags to the registry 
     * 
     * @param array $link 
     *   associative array:
     *   - **rel**: rel attribute 
     *   - **type**: type attribute 
     *   - **title**: title attribute 
     *   - **href**: href attribute 
     * @return void
     */
    public function addHeadLink($link) {	
        $this->vars['head_links'][] = $link;
    }

    /**
     * @brief Retrieve html code for link tags 
     * 
     * @return html code
     */
    public function headLinkHtml() {
        $r = '';
        foreach($this->head_links as $hlink) { 
            $r .= "<link"
                .(isset($hlink['rel']) ? " rel=\"".$hlink['rel']."\"" : '')
                .(isset($hlink['type']) ? " type=\"".$hlink['type']."\"" : '')
                .(isset($hlink['title']) ? " title=\"".$hlink['title']."\"" : '')
                ." href=\"".$hlink['href']."\" />\n";
        }
        return $r;
    }

    /**
     * @brief Gets a registry variable
     * 
     * @param string $index  variable name
     * @return void
     */
    public function __get($index) {
        return $this->vars[$index];
    }

}

?>
