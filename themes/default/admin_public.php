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
    <table class="login_container">
        <tr>
            <td>
                <div class="logo">
                </div>
            </td>
            <td>
                <div class="box" style="padding: 10px;">
                <div class="login"><?php echo $login_box ?></div>
                </div>
            </td>
        </tr>
    </table>
        <div><?php echo document::errorMessages(); ?></div>
    </body>
</html>


