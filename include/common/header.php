<header>
	<ul>		
		<li class="navItem <?php if($Page == 'index') echo "checked"; ?>"><a href="<?php echo navItem1Address; ?>"></a><?php echo mb_strtoupper(navItem1); ?></li>
		<li class="navItem <?php if($Page == navItem2Address) echo "checked"; ?>"><a href="<?php echo "/".navItem2Address."/".$_SESSION['user_data']['user_id']; ?>"></a><?php echo mb_strtoupper(navItem2); ?></li>
		<li class="navItem <?php if($Page == navItem3Address) echo "checked"; ?>"><a href="<?php echo "/".navItem3Address; ?>"></a><?php echo mb_strtoupper(navItem3); ?></li>
		<li class="navItem <?php if($Page == navItem4Address) echo "checked"; ?>"><a href="<?php echo "/".navItem4Address; ?>"></a><?php echo mb_strtoupper(navItem4); ?></li>
		<li class="navItem <?php if($Page == navItem5Address) echo "checked"; ?>"><a href="<?php echo "/".navItem5Address; ?>"></a><?php echo mb_strtoupper(navItem5); ?></li>
	</ul>
	<div id="siteName"><?php echo "<a href = '".navItem1Address."'><span>".mb_strtoupper(substr(siteName, 0, 3))."</span>".mb_strtoupper(substr(siteName, 3))."</a>"; ?>
		<table>
			<tr><td rowspan="4"><div id="rang" style="background-position: -<?php echo $_SESSION['user_data']['rang']*70; ?>px 0;"><div id="hint">Здесь отображается ваш текущий ранг на сайте, <span style="color:#b56029;font-size:18px;">выше ранг - выше вероятность выиграть более дорогое оружие.</span> Ранг повышается за каждые <span style="color:#b56029;font-size:18px;">5 игр</span>.</div></div><td><?php echo $_SESSION['user_data']['user_name']; ?></td></tr>
			<tr><td>Баланс: <span class="balance"><?php echo $_SESSION['user_data']['balance_kredits'].' К'; ?></span></td></tr>
			<tr><td><span tab="add" onclick="payment(this)" class="buttons">Пополнить баланс<img src="/resource/images/money_add.png" width="17px" align="right" alt="выйти"></span></td></tr>
			<tr><td><a href="/vk/logout" class="buttons">Выйти из аккаунта<img src="/resource/images/logout.png" width="17px" align="right" alt="выйти"></a></td></tr>
		</table>
	</div>
</header>