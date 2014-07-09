<!DOCTYPE html>
<?php
    // preload contents
    $menu = new menuController(); 
    $main_menu = $menu->mainMenu();
    $page = new pageController();
    $credits = $page->view('credits');
?>
<html lang="{LANGUAGE}">
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
        <div id="top_site">
            <header>
                <div class="header_logo"></div>
            </header>
        </div>
        <nav class="main_menu">
            <?php echo $main_menu; ?>
        </nav>
        <div class="site_content">
            <div id="content">
                <?php echo $mdl_url_content; ?>
            </div>
        </div>
        <div id="site_bottom">
            <footer>
                <?php echo $credits; ?>
            </footer>
        </div>
        <div><?php echo document::errorMessages(); ?></div>
    </body>
</html>
