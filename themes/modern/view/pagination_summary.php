<?php
/**
 * @file /var/www/jeff.git/themes/modern/view/pagination_summary.php
 * @ingroup modern_theme
 * @brief Template containing the pagination summary, see @ref pagination::summary
 *
 * Available variables:
 * - **start**: start item
 * - **end**: end item 
 * - **total**: total number of items 
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2014
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
?>
<span class="pagination-summary"><?= $start ?>-<?= $end ?> / <?= $total ?></span>
