<?php
/**
 * @file /var/www/jeff.git/themes/default/view/credits.php
 * @ingroup default_theme page_module
 * @brief Template of the credits section
 *
 * Available variables:
 * - **registry**: the @ref registry singleton object
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.98
 * @date 2011-2012
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
?>
<section>
<div class="line"></div>
<?= $registry->dtime->now(); ?> | Jeff Php Framework, <?= __('poweredby') ?> <a href="http://www.otto.to.it">Otto srl</a> 
</section>
