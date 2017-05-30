<?php require_once('include/siteScripts/profileData.php'); ?>
<div id="profileBlockWrapper">	
	<div id="infoBlock">
		<header>
			<p>ПРОФИЛЬ</p>
			<p>Информация о пользователе</p>
		</header>
		<table>
			<tr>
				<td id="photo" rowspan="2">
					<div style="background-image: url('<?php echo $profile_data['photo_big'] ?>');"></div>
					<div id="mask"></div>
				</td>
				<td id="mainInfo" colspan="2">
					<ul>
						<li>
							<p><?php echo $profile_data['user_name']; ?></p>
							<p>Сыграл игр: <span><?php echo $profile_data['games_count']; ?></span></p>
							<p><a target="_blank" href="https://vk.com/<?php echo $profile_data['screen_name']; ?>">Страница VK</a></p>
						</li>
						<li>
							<p>Прибыл: <span><?php echo $profile_data['reg_date']; ?></span></p>
							<p>В его отряде: <span><?php echo $profile_data['user_refs']; ?></span></p>
						</li>
					</ul>
					
				</td>				
			</tr>
			<tr>				
				<td id="rang" colspan="2">
					<p>РАНГ</p>
					<ul>
						<li id="currRang">
							<div style="background-position: -<?php echo $profile_data['rang']*70; ?>px 0;"></div>							
<?php if($profile_data['rang'] != 59)
					echo '	<div id="hint">Здесь отображается <span>текущий</span> ранг</div>'; ?>
						</li>
<?php
	if($profile_data['rang'] != 59)
	echo '				<li>
							<img src="/resource/images/trippleArrow.png">
							<p>ЧЕРЕЗ '.$profile_data['next_rang'].'</p>
						</li>
						<li id="nextRang">
							<div style="background-position: -'.($profile_data['rang']*70+70).'px 0;"></div>
							<div id="hint">Здесь отображается <span>будущий</span> ранг</div>
						</li>
					</ul>';
	else
		echo '		</ul>
					<p>ДОСТИГ <span>МАКСИМАЛЬНОГО</span> РАНГА</p>';
?>
					
				</td>
			</tr>
<?php
	if($profile_data['owner'])
	{
		echo '<tr>
				<td id="balance">
					<p>БАЛАНС</p>
					<p>Кредиты: <span>'.$profile_data['balance_kredits'].' К</span></p>
					<p>Бонусы: <span>'.$profile_data['balance_bonus'].' Б</span></p>
				</td>
				<td class="cashOperations" tab="add" onclick="payment(this);">
					<img src="/resource/images/arrow.png">
					<p>ПОПОЛНИТЬ</p>
				</td>
				<td class="cashOperations" tab="withdraw" onclick="payment(this);">
					<img src="/resource/images/arrow.png">
					<p>ВЫВЕСТИ</p>
				</td>
			</tr>
			<tr id="referalBlock">
				<td>';
		if(!empty($referal_data))
			echo '	<div id="inviterBlock">
						<header>ВАС ПРИГЛАСИЛ:</header>
						<ul>
							<li><img src="'.$referal_data["photo"].'" alt="'.$referal_data['name'].'" width="50px" height="50px" /></li>
							<li>
								<p>'.$referal_data['name'].'</p>
								<p><span>'.$referal_data['games'].' ИГР</span></p>
							</li>
							<a href="/profile/'.$referal_data['id'].'"></a>
						</ul>							
					</div>';
		echo '	</td>
				<td colspan="2" rowspan="2" align="right">
					<div id="hrefBlock">
						<header>ВАША РЕФЕРАЛЬНАЯ ССЫЛКА:</header>
						<ul class="copyBlock">
							<li><input type="text" class="copyField" value="'.$profile_data['ref_link'].'" readonly /></li>
							<li><input type="button" class="copyBtn" value="КОПИРОВАТЬ" onclick="copyToBuffer(this)"/></li>
						</ul>							
					</div>
					<ul>
						<li><img src="/resource/images/cash.png" /></li>
						<li>Попроси друга зарегистрироваться по этой ссылке и получи '.referalDepositPercent.'% от каждого его пополнения счёта!</li>
					</ul>					
				</td>
			</tr>
			<tr><td></td><td colspan="2"></td></tr>';
	}		
?>			
		</table>
	</div>
	<div id="gunsBlock">
		<header>
			<p>СКЛАД</p>
			<p>Последние выигрыши</p>
		</header>
		<ul>
			<?php echo $profile_data['latestWinnings']; ?>
		</ul>
	</div>
</div>