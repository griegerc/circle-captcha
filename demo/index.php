<?php

require '../captcha/CaptchaCircle.php';

define('SIZE_X', 300);
define('SIZE_Y', 100);


if (isset($_GET['show'])) {
	$captcha = new CaptchaCircle($_GET['show'], SIZE_X, SIZE_Y);
	$captcha->show();
	exit();
}

if (isset($_GET['validate'])) {

	print '<pre>';
	print_r($_GET);
	print '</pre>';

	$captcha = new CaptchaCircle($_GET['captchaId'], SIZE_X, SIZE_Y);
	$result = $captcha->isValid($_GET['captcha_x'], $_GET['captcha_y']);
	if ($result === true) {
		print '<p style="color:#00BB00">Captcha ok</p>';
	} else {
		print '<p style="color:#FF0000">Captcha missed</p>';
	}
}


$captchaId = CaptchaCircle::generateId();


?>
<html>
	<head>
		<title>Captcha test</title>
	</head>
	<body>
		<p>Click in the open circle:</p>
		<form method="get" action="">
			<input type="hidden" name="validate" value="1"/>
			<input type="hidden" name="captchaId" value="<?php print $captchaId; ?>"/>
			<input type="image" name="captcha" src="?show=<?php print $captchaId; ?>"/>
		</form>

<pre>
<?php
	$captcha = new CaptchaCircle($captchaId, SIZE_X, SIZE_Y);
print_r($captcha);
?>
</pre>
	</body>
</html>