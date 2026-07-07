<?php require_once __DIR__.'/inc_global.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo $language->get(); ?>" dir="ltr">
<head>
    <title><?php echo $web->pageTitle; ?></title>
    <?php echo $head->render(); ?>
</head>

<body>
<h1>Default Website Project Template</h1>
<h2>Cascading dropdown menu</h2>
<p>An animated and fluid menu in pure CSS with nested sub menus.</p>
<?php echo $menu1->render(); ?>

<h2>PHP menu</h2>
<?php echo $menu2->render(); ?>

</body>
</html>