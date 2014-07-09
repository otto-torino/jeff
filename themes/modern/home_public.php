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
        <nav class="navbar-wrapper navbar-inverse navbar-fixed-top" role="navigation">
            <div class="container">
                <h1 class="hidden">Main menu</h1>
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="bs-example-navbar-collapse-1">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#"><?= htmlVar($registry->title) ?></a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <?php echo $language_choose; ?>
                    <?php echo $main_menu; ?>
                </div><!-- /.navbar-collapse -->
            </div>
        </nav>
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <?php echo $index_page; ?>
                </div>
                <div class="col-md-4">
                    <?php echo $login_box; ?>
                </div>
            </div>
        </div>
        <footer>
            <div class="container">
                <?php echo $credits; ?>
            </div>
        </footer>
        <div><?php echo document::errorMessages(); ?></div>
    </body>
</html>
