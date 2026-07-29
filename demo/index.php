<?php

use WebsiteTemplate\Html\RadioGroup;
use WebsiteTemplate\Html\SelectField;

require_once __DIR__.'/inc_global.php';
$data = ['1' => 'item 10', 'ok' => 'item ok', '3' => 'item all'];
$rg = (new RadioGroup('fldRadioGroup', $data))->render();
$sel = new SelectField('fldSelectField', $data);
?>
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

<h2>Form elements</h2>
<pre>
<code>echo (new RadioGroup('fldRadioGroup', [10, 'ok', 'all'], ))->render();</code>
</pre>
<?php echo $rg; ?>
<?php
echo $sel->render();
$sel->size = 4;
$sel->defaultText = 'default text';
//$sel->autoOptionTitle = Op
echo $sel->render();
?>
</body>
</html>