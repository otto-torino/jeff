<!DOCTYPE html>
<?php
    // preload contents
    $menu = new menuController(); 
    $admin_menu = $menu->adminMenu();
    $page = new pageController();
    $credits = $page->view('credits');
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
    <body class="admin">
        <div class="container-fluid">
            <div class="">
                <div class="col-md-1 admin-left-sidebar">
                    <nav class="menu-adminmenu">
                        <?php echo $admin_menu ?>
                    </nav>
                </div>
                <div class="col-md-11 admin-content">
                    <?php echo $mdl_url_content; ?>
                </div>
            </div>
        </div>
        <div><?php echo document::errorMessages(); ?></div>
    </body>
</html>
