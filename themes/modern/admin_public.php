<?php
/**
 * @file admin_public.php
 * @brief Contains the admin public template (admin login)
 *
 * @author abidibo abidibo@gmail.com
 * @version 0.99
 * @date 2014
 * @copyright Otto srl [MIT License](http://www.opensource.org/licenses/mit-license.php)
 */
?>
<!DOCTYPE html>
<?php
    // preload contents
    $login = new loginController(); 
    $login_box = $login->adminLogin();
?>
<html lang="<?php echo $registry->language; ?>">
    <head>
        <meta charset="utf-8" />
        <meta name="description" content="<?php echo htmlspecialchars($registry->description, ENT_QUOTES, 'UTF-8'); ?>" />
        <meta name="keywords" content="<?php echo htmlspecialchars($registry->keywords, ENT_QUOTES, 'UTF-8'); ?>" />
        <?php echo $registry->metaHtml(); ?>
        <title><?php echo htmlspecialchars($registry->title, ENT_QUOTES, 'UTF-8'); ?></title>
        <link rel="shortcut icon" href="<?php echo htmlspecialchars($registry->favicon, ENT_QUOTES, 'UTF-8'); ?>" />
        <?php echo $registry->headLinkHtml(); ?>
        <?php echo $registry->cssHtml(); ?>
        <?php echo $registry->jsHtml(); ?>
    </head>
    <body>
        <div class="container">
            <div class="login_container">
                <h1><?= htmlVar($registry->title) ?></h1>
                <h2><?= __('ReservedArea') ?></h2>
                        <div class="login"><?php echo $login_box ?></div>
            </div>
        </div>
        <div><?php echo document::errorMessages(); ?></div>
    </body>
</html>


