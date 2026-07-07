<?php require_once __DIR__.'/inc_global.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo $language->get(); ?>" dir="ltr">
<head>
<title><?php echo $web->pageTitle; ?></title>
<?php echo $head->render(); ?>
</head>

<body>
<p>Default Website Project Template</p>
<h2>Cascading dropdown menu</h2>
<p>An animated and fluid menu in pure CSS with nested sub menus.</p>
<?php echo $menu2->render(); ?>

<h2>PHP menu</h2>
<div id="layoutMenu3">
    <?php echo $menu3->render(); ?>
</div>
<h2>PHP Menu all open</h2>
<div id="layoutMenu4">
    <?php echo $menu4->render(); ?></div>
<div id="layoutContent">
</body>
</html>