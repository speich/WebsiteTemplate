<?php require_once 'inc_script.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo $web->getLang(); ?>" dir="ltr">
<head>
<title><?php echo $web->getPageTitle(); ?></title>
<?php require_once 'inc_head.php'; ?>
<meta name="description" content="">
<meta name="robots" content="index, follow">
</head>

<body>
<?php require_once 'inc_bodystart.php'; ?>
<nav><?php echo $mainNav->render(); ?></nav>
<p>Default Website Project Template</p>
<?php require_once 'inc_bodyend.php'; ?>
</body>
</html>