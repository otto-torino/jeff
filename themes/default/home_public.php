<!DOCTYPE html>
<?php
    // preload contents
    $menu = new menuController(); 
    $main_menu = $menu->mainMenu();
    $page = new pageController();
    $credits = $page->view('credits');
    $language = new languageController();
    $language_choose = $language->choose();
    $index = new indexController();
    $index_page = $index->index();
    $login = new loginController();
    $login_box = $login->login();
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
        <div id="top_site">
            <header>
                <div class="header_logo"></div>
                <?php echo $language_choose; ?>
            </header>
        </div>
        <?php echo $main_menu; ?>
        <div class="site_content">
            <div id="content">
                <div class="col1 left">
                    <?php echo $index_page; ?>
                </div>
                <div class="col2 right">
                    <?php echo $login_box; ?>
                </div>
                <div class="clear"></div>
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
