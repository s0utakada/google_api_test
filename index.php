<?php

	require_once('config.php');
	require_once('functions.php');

	@session_start();

	if (empty($_SESSION['me'])) {
					header('Location: '.SITE_URL.'login.php');
					exit;
	}


?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<title>Home</title>
</head>
<body>
<h1>Home</h1>
<p>Login User : <?php echo h($_SESSION['me']['google_name']); ?>(<?php echo h($_SESSION['me']['google_email']); ?>)</p>
<p><a href="logout.php">[ログアウト]</a></p>
</body>
</html>
