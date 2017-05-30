		<div class="balance_form_wrapper balance_form_add_wrapper">
			<div id="popup" onclick="$('.balance_form_wrapper').css({'display':'none'});$('.balance_form_wrapper .balance_form').css({'opacity':'0'});"></div>
			<div class="balance_form balance_add_form">
				<header>Пополнение баланса</header>
				<div class="balance_inputs">
					<p class="balance_val"><input type="text" placeholder="0.00" id="amount" name="amount" onchange="$(this).val(number_format($(this).val(), 2, '.', ' ')); if(parseInt($(this).val().replace(/ /g, '')) > 20000) $(this).val('20 000.00'); else if(parseInt($(this).val().replace(/ /g, '')) < 20) $(this).val('20.00');" onkeypress="return event.charCode >= 48 && event.charCode <= 57"/><label for="amount">руб.</label></p>
					<p><input type="submit" value="ПОПОЛНИТЬ" href="add" onclick="payment(this);" /></p>
				</div>
				<div class="warn_imprt"><span>Внимание!</span> На ваш баланс будет зачислено <span>2X кредитов</span> от сумы пополнения баланса.</div>
				<div class="warn"><p>*Минимальная сумма пополнения баланса составляет - 20 руб.</p></div>
				<div class="warn"><p>*Максимальная сумма пополнения баланса составляет - 20 000 руб.</p></div>		
			</div>
		</div>
<?php
	require_once('include/configs/dbCfg.php');
    $DBH = new PDO('mysql:host='.HOST.';dbname='.DB, Login, Pass);
    $DBH->exec('SET NAMES "utf8"');

    $STH = $DBH->prepare('SELECT *, DATE_FORMAT(`request_date`, "%d.%m %H:%i") as `request_date` FROM `withdraw_requests` WHERE `user_id` = :user_id');
    $STH->execute(array('user_id' => $_SESSION['user_data']['user_id']));
    $withdraw_request = $STH->fetch(PDO::FETCH_ASSOC);
?>
		<div class="balance_form_wrapper balance_form_withdraw_wrapper">
			<div id="popup" onclick="$('.balance_form_wrapper').css({'display':'none'});$('.balance_form_wrapper .balance_form').css({'opacity':'0'});"></div>
			<div class="balance_form balance_withdraw_form">
				<header>Вывод кредитов</header>
				<div class="warn_imprt"><span>Внимание!</span> Вам нужно указать почту от аккаунта <span>Warface</span> или платежный ID, на который будут перечислены <span>выигрышные кредиты</span>. Кредиты будут на Вашем аккаунте в течении от 1 минуты до 24 часов!</div>
				<div class="balance_inputs">
					<p><input type="text" placeholder="Ваш Email адрес / Платежный ID" name="email"/></p>
					<p class="balance_val"><input type="text" placeholder="0.00" id="amount" name="amount" onchange="$(this).val(number_format($(this).val(), 2, '.', ' ')); if(parseInt($(this).val().replace(/ /g, '')) > 20000) $(this).val('20 000.00'); else if(parseInt($(this).val().replace(/ /g, '')) < 50) $(this).val('50.00');" onkeypress="return event.charCode >= 48 && event.charCode <= 57"/><label for="amount">К</label></p>
					<p>Ваш текущий баланс: <span><?php echo $_SESSION['user_data']['balance_kredits']; ?> К</span></p>
					<p><input type="submit" value="ВЫВЕСТИ КРЕДИТЫ" href="withdraw" onclick="payment(this);" /></p>
				</div>				
				<div class="warn"><p>*Минимальная сумма вывода кредитов составляет - 50 КРЕДИТОВ.</p></div>
				<div class="warn"><p>*Максимальная сумма вывода кредитов составляет - 20 000 КРЕДИТОВ.</p></div>
				<table id="withdraw_request">
					<thead>
						<td colspan="4">Запрос на вывод кредитов</td>
					</thead>
					<tr>
						<th>EMAIL / ID</th>
						<th>КРЕДИТЫ</th>
						<th>ДАТА</th>
						<th>СТАТУС</th>
					</tr>
					<tr>
<?php
	if (isset($withdraw_request['user_id'])) {
		echo '			<td>'.$withdraw_request['withdraw_email'].'</td>
						<td>'.$withdraw_request['amount'].'</td>
						<td>'.$withdraw_request['request_date'].'</td>';
		switch ($withdraw_request['status']){
			case 'pending':
				echo '	<td style="color: yellow;">ожидает</td>';
				break;
			case 'error':
				echo '	<td style="color: red;">отказано</td>';
				break;
			case 'done':
				echo '	<td style="color: green;">готово</td>';
				break;
		}
						
	}
	else
		echo '			<td colspan="4">Нету активных запросов на вывод</td>';
?>
					</tr>
				</table>
			</div>
		</div>