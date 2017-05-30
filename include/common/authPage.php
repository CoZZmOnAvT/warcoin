<?php
	require_once('include/configs/siteCFG.php');
	mb_internal_encoding("UTF-8"); 	

	if(isset($_GET['ref']))
	{
		unset($_SESSION['reg_info']);
		$_SESSION['reg_info']['referal'] = intval($_GET['ref']);		
	}
	if($Page != 'index' && $Module != 'index')
	{
		header('Location: /');
		exit();
	}
?>
<!DOCTYPE HTML>
<html>
	<head>
		<meta charset = "utf-8">
		<meta name="Description" content='"<?php echo siteName;?>" - рулетка с самыми большими шансами на выигрыш! Заходи и попробуй сам!'>
        <meta name="keywords" content="Варфейс, Ворфейс, Warface, рулетка, выигрыш, выигриш, виигриш, победа, ставки, деньги, лотерея, халява, игра, компьютерная игра, тир, кредиты, тир кредитов, кредити">
        <meta name="theme-color" content="#1c1e1d">
        <title><?php echo siteTitle?></title>
        <link rel="shortcut icon" href="/resource/images/favicon.ico" type="image/x-icon" />        
        <link rel="stylesheet" href="/resource/css/auth.min.css" />
        <script type="text/javascript" src = "/resource/js/jquery-3.1.1.min.js"></script>
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
		
		<script type="text/javascript" src = "/resource/js/authPage.min.js"></script>
		<div class="preloaderWrapper"><div class="preloader"></div><div class="percent">0%</div></div>
		<header>
			<p><a href="/"><?php echo "<span>".mb_strtoupper(substr(siteName, 0, 3))."</span>".mb_strtoupper(substr(siteName, 3)); ?></a></p>
			<p>ДОБЫВАТЬ КРЕДИТЫ НИКОГДА НЕ БЫЛО ТАК ЛЕГКО</p>
		</header>
		<div id="layot">
			<div id="authBlock">
				<header>
					<p>ПРИСОЕДИНЯЙСЯ</p>
					<p>Крутое оружие и море кредитов ждут тебя!</p>
				</header>
				<div id="authButtonBlock">
					<p>Ввойти через Вконтакте</p>
					<div id="authButton"><a href="/vk/auth">ПРИСОЕДИНИТСЯ</a></div>
				</div>				
			</div>
		</div>
		<div id="footer">
			<table id="siteStats">
				<tr>
					<td id="online">
						<p>0</p>
						<p><span>пользовател<span id="case">ей</span> онлайн</span></p>
					</td>
					<td id="registered">
						<p>0</p>
						<p>пользовател<span id="case" class="nonSpan">ей</span> зарегистрировано</p>
					</td>
					<td id="creditsWon">
						<p>0</p>
						<p><span>кредитов выиграно</span></p>
					</td>
				</tr>
			</table>
			<p id="siteName"><?php echo "<span>".mb_strtoupper(substr(siteName, 0, 3))."</span>".mb_strtoupper(substr(siteName, 3)); ?></p>
			<p>ВСЕ ПРАВА ЗАЩИЩЕНЫ © <?php echo date('Y')?><br>Created by CoZZ</p>
		</div>
		<script type="text/javascript" src = "/resource/js/animation.min.js"></script>
	</body>
</html>