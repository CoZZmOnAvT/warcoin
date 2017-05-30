<?php 
	$loadPage = true;
	require_once('include/siteScripts/loteryScript.php');
?>

<div id="loteryWrapper">
	<div id="loteryBlock">
		<header>
			<p>ЛОТЕРЕЯ</p>
			<p>Купи билет и забери себе банк!</p>
		</header>
		<div id="bizBlock">
			<div id="timer"><p><?php echo $loteryData['time']; ?></p><div id="hint">Время до следуйщего розыграша</div></div>
			<div id="biz">
				<ul>
					<?php echo $loteryData['members']['roulette']; ?>
				</ul>
				<div id="loading"><img src="/resource/images/loading.gif" width="100px" height="100px" /></div>
				<div id="scope"></div>
			</div>
			<div id="bank">БАНК: <span id="val"><?php echo $loteryData['bank']; ?></span><span>К</span></div>
		</div>
		<form id="ticketBuy" method="POST">
			<header>БИЛЕТОВ ДЛЯ ПОКУПКИ</header>
			<p>*В банк лотереи поступают <span><?php echo (100 - loteryCommission).'%'; ?></span> от общей стоимости приобретаемых билетов</p>
			<div id="ticketCountBlock">
				<div id="minus" onclick="$('#ticketCountBlock').children('input[name=ticketCount]').val(function(i, oldval){if($(this).val() > 1) return --oldval; else return 1;});"></div>
				<input type="text" name="ticketCount" value="1" maxlength="2" onchange="if($(this).val() > 50) $(this).val(50);" onkeypress="return event.charCode >= 48 && event.charCode <= 57" required/>
				<div id="plus" onclick="$('#ticketCountBlock').children('input[name=ticketCount]').val(function(i, oldval){if($(this).val() < 50) return ++oldval; else return 50;});"></div>
			</div>			
			<input type="submit" value="КУПИТЬ БИЛЕТ"/>
			<p>СТОИМОСТЬ ОДНОГО БИЛЕТА: <span><?php echo loteryTiketPrice; ?>К</span></p>
			<div id="loading"><img src="/resource/images/loading.gif" width="190px" height="190px" /></div>
		</form>				
	</div>
	<?php echo $loteryData['lastWinner']; ?>	
	<div id="playersListBlock">
		<header>
			<p>УЧАСТНИКИ</p>
			<p>Больше билетов - выше шанс на победу</p>
		</header>
		<table id="playersList">
			<tr>
				<th>№</th>
				<th>ПОЛЬЗОВАТЕЛЬ</th>
				<th>БИЛЕТОВ КУПЛЕНО</th>			
			</tr>
			<?php echo $loteryData['members']['list']; ?>
		</table>
	</div>
</div>