<?php

Class Registry {

 /*
 * @the vars array
 * @access private
 */
 private $vars = array();

 /**
 *
 * @set undefined vars
 *
 * @param string $index
 *
 * @param mixed $value
 *
 * @return void
 *
 */
 public function __set($index, $value)
 {
        $this->vars[$index] = $value;
 }

 public function addCss($css) {
 	$this->vars['css'][] = $css;
 }
 
 public function addJs($js) {
 	$this->vars['js'][] = $js;
 }

 /**
 *
 * @get variables
 *
 * @param mixed $index
 *
 * @return mixed
 *
 */
 public function __get($index)
 {
        return $this->vars[$index];
 }

}

?>
