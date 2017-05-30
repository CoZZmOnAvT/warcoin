<?php
	if(@$_POST['method'] === 'add')	{
		if(!@$_POST['amount'])
			exit(json_encode(array('message' => array(	'text'	=> 'Укажите сумму для пополнения!',
														'err'	=> 1))));
		$donate_amount = abs(intval($_POST['amount']));
		if($donate_amount > 20000 || $donate_amount < 20)
			exit(json_encode(array('message' => array(	'text'	=> 'Некоректная сумма кредитов!',
														'err'	=> 1))));

		require_once('include/configs/dbCfg.php');
		$DBH = new PDO('mysql:host='.HOST.';dbname='.DB, Login, Pass);
		$DBH->exec('SET NAMES "utf8"');

		$STH = $DBH->prepare('  INSERT INTO `balance_add_log` (`user_id`, `amount`, `status`, `date`)
                                VALUES (:user_id, :amount, "pending", :date)');
		$STH->execute(array('user_id' 			=> $_SESSION['user_data']['user_id'],
							'amount'			=> $donate_amount,
							'date'				=> date('Y-m-d H:i:s')));

		$STH = $DBH->prepare('SELECT `id`, `date` FROM `balance_add_log` WHERE `user_id` = :user_id ORDER BY `date` DESC LIMIT 1');
		$STH->execute(array('user_id' 			=> $_SESSION['user_data']['user_id']));
		$order_id = $STH->fetch(PDO::FETCH_ASSOC);
		$order_id = $order_id['id'];

		require_once('include/configs/free_kassaCfg.php');

		$baseURL = 'https://www.free-kassa.ru/merchant/cash.php?';
		$params = array(
			'm'	=> fk_id,
			'oa'=> number_format($donate_amount, 2, '.', ''),
			'o'	=> $order_id,
			's'	=> md5(fk_id.':'.number_format($donate_amount, 2, '.', '').':'.fk_secret1.':'.$order_id),
			'em'=> @$_SESSION['user_data']['email'],
			'lang'=>'ru'
		); 
		exit(json_encode(array('redirectURL' => $baseURL.urldecode(http_build_query($params)))));
	}
	else if(@$_POST['method'] === 'withdraw'){
		require_once('include/configs/free_kassaCfg.php');
		require_once('include/configs/dbCfg.php');
		$DBH = new PDO('mysql:host='.HOST.';dbname='.DB, Login, Pass);
		$DBH->exec('SET NAMES "utf8"');

		if(!@$_POST['email'] || !@$_POST['amount'])		
			exit(json_encode(array('message' => array(	'text'	=> 'Укажите Email/ID и сумму для вывода!',
														'err'	=> 1))));		

		$STH = $DBH->prepare('SELECT `balance_kredits` FROM `users_balance` WHERE `user_id`=:user_id');
		$STH->execute(array('user_id' => $_SESSION['user_data']['user_id']));
		$balance = $STH->fetch(PDO::FETCH_ASSOC);

		$withdraw_amount = abs(intval($_POST['amount']));
		if($withdraw_amount > 20000 || $withdraw_amount < 50)
			exit(json_encode(array('message' => array(	'text'	=> 'Некоректная сумма кредитов!',
														'err'	=> 1))));

		$email = '';
		if(intval($_POST['email']) > 0 && strlen(trim($_POST['email'])) == 9){
			$email = intval($_POST['email']);	
		}				
		else if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
			$email = trim($_POST['email']);	
		}				
		else
			exit(json_encode(array('message' => array(	'text'	=> 'Некоректный Email или Платёжный ID!',
														'err'	=> 1))));

		$STH = $DBH->prepare('	SELECT `users_balance`.`balance_freeze`, `users_stats`.`god_mode`
								FROM `users_balance`
								INNER JOIN `users_stats` ON `users_balance`.`user_id`=`users_stats`.`user_id`
								WHERE `users_stats`.`user_id`=:user_id');
		$STH->execute(array('user_id' => $_SESSION['user_data']['user_id']));
		$balance_info = $STH->fetch(PDO::FETCH_ASSOC);

		if($balance_info['balance_freeze'] > 0 || $balance_info['god_mode'] > 0)
			exit(json_encode(array('message' => array(	'text'	=> 'Вам запрещён вывод средств с сайта!',
														'err'	=> 1))));

		$STH = $DBH->prepare('SELECT * FROM `withdraw_requests` WHERE `user_id`=:user_id AND `status`="pending"');
		$STH->execute(array('user_id' => $_SESSION['user_data']['user_id']));
		if($STH->fetch(PDO::FETCH_ASSOC))
			exit(json_encode(array('message' => array(	'text'	=> 'У вас уже есть активный запрос на вывод кредитов!',
														'err'	=> 1))));		

		if($balance['balance_kredits'] >= $withdraw_amount){
			$STH = $DBH->prepare('  INSERT INTO `withdraw_requests` (`user_id`, `amount`, `withdraw_email`, `status`, `request_date`)
                                    VALUES                  (:user_id, :amount, :withdraw_email, "pending", :request_date)
                                    ON DUPLICATE KEY
                                    UPDATE `amount`=:amount, `withdraw_email`=:withdraw_email, `status`="pending", `request_date`=:request_date');
			$STH->execute(array('user_id' 			=> $_SESSION['user_data']['user_id'],
								'amount'			=> $withdraw_amount,
								'withdraw_email'	=> $email,
								'request_date'		=> date('Y-m-d H:i:s')));

			$STH = $DBH->prepare('UPDATE `users_balance` SET `balance_kredits` = :balance_kredits WHERE `user_id`=:user_id');
			$STH->execute(array('user_id' 	=> $_SESSION['user_data']['user_id'],
								'balance_kredits'	=> $balance['balance_kredits'] - $withdraw_amount));

			require_once('include/configs/siteCFG.php');

			$message = '<p>Ожидается выплата средств в размере: '.floor($withdraw_amount/2).' Рублей!</p><p>Платёжный Email/ID: '.$email.'</p><p>Страница VK пользователя: https://vk.com/'.$_SESSION['user_data']['screen_name'].'</p><p>User_ID: '.$_SESSION['user_data']['user_id'].'</p>';

			$headers= "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=utf-8\r\n";
			$headers .= "From: payment@warcoin.tk\r\n";
			$headers .= "Reply-To: DoNotReply@warcoin.tk\r\n";

			$mailSend = mail(adminEmail, 'Запрос на выплату '.$_SESSION['user_data']['user_id'], $message, $headers);

			exit(json_encode(array('redirectURL' => $_SERVER['HTTP_REFERER'])));
		}
		else{
			exit(json_encode(array('message' => array(	'text'	=> 'На балансе недостаточно средств',
														'err'	=> 1))));
		}		
	}
	else{
		require_once('include/configs/free_kassaCfg.php');
		require_once('include/configs/siteCFG.php');
		require_once('include/configs/dbCfg.php');
		$DBH = new PDO('mysql:host='.HOST.';dbname='.DB, Login, Pass);
		$DBH->exec('SET NAMES "utf8"');

		if(!in_array(getUserHostAddress(), array('136.243.38.147', '136.243.38.149', '136.243.38.150', '136.243.38.151', '136.243.38.189', '88.198.88.98'))){
			exit('hacking attempt!');
		}

		$STH = $DBH->prepare('SELECT `id`, `user_id`, `amount`, `status`, `date` FROM `balance_add_log` WHERE `id` = :merchant_order_id AND `status` = "pending"');
		$STH->execute(array('merchant_order_id' => intval($_POST['MERCHANT_ORDER_ID'])));
		$data = $STH->fetch(PDO::FETCH_ASSOC);
		if(!$data){
			exit('wrong data!');
		}

		$amount = $data['amount'];

		$site_side_sign = md5(fk_id.':'.$data['amount'].':'.fk_secret2.':'.$_POST['MERCHANT_ORDER_ID']);
		$sign = md5(fk_id.':'.$_POST['AMOUNT'].':'.fk_secret2.':'.$_POST['MERCHANT_ORDER_ID']);

		if($sign != $_POST['SIGN'] || $site_side_sign != $sign){
			exit('wrong sign');
		}

		$STH = $DBH->prepare('UPDATE `balance_add_log` SET `status`="done" WHERE `id` = :merchant_order_id');
		$STH->execute(array('merchant_order_id' => intval($_POST['MERCHANT_ORDER_ID'])));

		$STH = $DBH->prepare('DELETE FROM `balance_add_log` WHERE `user_id` = :user_id AND `status`="pending"');
		$STH->execute(array('user_id' => $data['user_id']));

		$STH = $DBH->prepare('SELECT `balance_kredits` FROM `users_balance` WHERE `user_id` = :user_id');
		$STH->execute(array('user_id' => $data['user_id']));
		$balance = $STH->fetch(PDO::FETCH_ASSOC);
		$balance = $balance['balance_kredits'];

		$STH = $DBH->prepare('SELECT `referal` FROM `users_registered` WHERE `user_id` = :user_id');
		$STH->execute(array('user_id' => $data['user_id']));
		$ref = $STH->fetch(PDO::FETCH_ASSOC);
		
		if(isset($ref['referal']) && $ref['referal'])
		{
			$ref = $ref['referal'];

			$STH = $DBH->prepare('SELECT `balance_bonus` FROM `users_balance` WHERE `user_id` = :ref_id');
			$STH->execute(array('ref_id' => $ref));
			$balance_bonus = $STH->fetch(PDO::FETCH_ASSOC);
			$balance_bonus = $balance_bonus['balance_bonus'];

			$STH = $DBH->prepare('UPDATE `users_balance` SET `balance_bonus` = :amount  WHERE `user_id` = :ref_id');
			$STH->execute(array(
				'amount'	=> $balance_bonus + intval($data['amount']*2*(referalDepositPercent/100)),
				'ref_id' 	=> $ref));
		}
		

		$STH = $DBH->prepare('UPDATE `users_balance` SET `balance_kredits` = :amount  WHERE `user_id` = :user_id');
		$STH->execute(array(
			'amount'	=> $balance + intval($data['amount']*2),
			'user_id' 	=> $data['user_id']));



		exit("YES");
	}
