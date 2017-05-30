<?php
	require_once('include/configs/siteCFG.php');
	mb_internal_encoding("UTF-8");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="ru">
	<head>
		<meta charset = "utf-8">
		<meta name="Description" content='"<?php echo siteName;?>" - рулетка с самыми большими шансами на выигрыш! Заходи и попробуй сам!'>
        <meta name="keywords" content="Варфейс, Ворфейс, Warface, рулетка, выигрыш, выигриш, виигриш, победа, ставки, деньги, лотерея, халява, игра, компьютерная игра, тир, кредиты, тир кредитов, кредити">
        <meta name="theme-color" content="#1c1e1d">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title><?php echo siteTitle?></title>
        <link rel="stylesheet" href="/resource/css/main.min.css" />
        <link rel="shortcut icon" href="/resource/images/favicon.ico" type="image/x-icon" />
        <noscript><meta http-equiv="refresh" content="0; url=/vk/logout?jsBlocked=true"></noscript>        
	</head>
	<body>
<?php
	if(isset($_SESSION['err_msg']) || isset($_SESSION['msg']))
		echo '<style>.messageBox{visibility:visible;opacity:1;display:block}</style>';
	else
		echo '<div class="messageBox"></div>';
	if(isset($_SESSION['err_msg']))
	{
		echo '
			<div class="messageBox negative">
				<p>'.$_SESSION['err_msg'].'</p>
			</div>';
		unset($_SESSION['err_msg']);
	}
	if(isset($_SESSION['msg']))
	{
		echo '
			<div class="messageBox">
				<p>'.$_SESSION['msg'].'</p>
			</div>';
		unset($_SESSION['msg']);
	}
?>
		<div class="preloaderWrapper"><div class="preloader"></div><div class="percent">0%</div></div>
		<div id="winnerBlock"></div>
		<div id="headerWrapper">
			<?php require_once('include/common/header.php');?>
		</div>
		<div id="layotWrapper">
		<?php
			if($Page == 'index' && $Module == 'index' && empty($Param)) require_once('include/dynamic/main.php');
			else if($Page == navItem2Address && $Module && empty($Param)) require_once('include/dynamic/'.navItem2Address.'.php');
			else if($Page == navItem3Address && !$Module) require_once('include/dynamic/'.navItem3Address.'.php');
			else if($Page == navItem4Address && !$Module) require_once('include/dynamic/'.navItem4Address.'.php');
			else if($Page == navItem5Address && !$Module) require_once('include/dynamic/'.navItem5Address.'.php');
			else
			{
				header('Location: /');
			    exit();
			}
		?>
		</div>
		<div id="footerWrapper">
			<?php require_once('include/common/footer.php'); ?>
		</div>

		<?php require_once('include/common/balance_form.php'); ?>

        <script type="text/javascript" src = "/resource/js/jquery-3.1.1.min.js"></script>
        <script type="text/javascript" src = "/resource/js/jquery.easing.min.1.3.js"></script>
        <script type="text/javascript" src = "/resource/js/jquery.smoothScroll.min.js"></script>
        <script type="text/javascript" src = "/resource/js/controll.min.js"></script>
        <script type="text/javascript" src = "/resource/js/animation.min.js"></script>        
	</body>
</html>
