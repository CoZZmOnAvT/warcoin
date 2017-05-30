<?php
	require_once('include/configs/siteCFG.php');
	require_once('include/configs/dbCfg.php');
	$DBH = new PDO('mysql:host='.HOST.';dbname='.DB, Login, Pass);
	$DBH->exec('SET NAMES "utf8"');

	$userPayInfo = array();
	try
	{
		$STH = $DBH->prepare('	SELECT 	`users_balance`.`balance_kredits`,
										`users_balance`.`balance_bonus`,
										`users_balance`.`balance_freeze`,
										`users_stats`.`god_mode`,
										`users_stats`.`test_spin`,
										`users_stats`.`youtube`,
										`users_stats`.`games_count`
								FROM `users_balance`
								INNER JOIN `users_stats` on `users_balance`.`user_id` = `users_stats`.`user_id`
								WHERE `users_balance`.`user_id`=:user_id');
		$STH->execute(array('user_id' => intval($_SESSION['user_data']['user_id'])));
		$userPayInfo = $STH->fetch(PDO::FETCH_ASSOC);
	}
	catch(PDOException $e)
	{
		$responseData = array(
		'message' => array(	'text' 	=> 'У сайта временные неполадки, попробуй позже',
							'err' 	=> 1));
		exit(json_encode($responseData));
	}
	
	if(!$userPayInfo)
	{
		$responseData = array(
			'message' => array(	'text' => 'Рулетка неисправна, попробуйте позже.',
								'err' => 1));
		exit(json_encode($responseData));
	}	

	$responseData = array();

	if($userPayInfo['god_mode'])
	{			
		$responseData = rouletteSpinDataGenarator('god', array('header' => 'ВАШ ВЫИГРАШ', 'subHeader' => 'С каждой игрой шансы сильно растут!', 'btnText' => 'ЗАБРАТЬ ОРУЖИЕ'));		
	}
	else
	{
		if(@$_POST['test_spin'])
		{
			if($userPayInfo['test_spin'])
			{
				$responseData = rouletteSpinDataGenarator('test', array('header' => 'ВЫ МОГЛИ ВЫИГРАТЬ', 'subHeader' => 'Сыграйте по-настоящему и приз станет вашим!', 'btnText' => 'ПРИНЯТЬ'));
				try
				{
					$STH = $DBH->prepare('UPDATE `users_stats` SET `test_spin`=0 WHERE `user_id`=:user_id');
					$STH->execute(array('user_id' => intval($_SESSION['user_data']['user_id'])));
				}
				catch(PDOException $e)
				{
					$responseData = array(
					'message' => array(	'text' 	=> 'У сайта временные неполадки, попробуй позже',
										'err' 	=> 1));
					exit(json_encode($responseData));
				}				
			}
			else
			{
				$responseData = array(
					'message' => array(	'text' 	=> 'Вы уже использовали свою тестовую попытку!',
										'err' 	=> 1));
			}			
		}
		else
		{
			if($userPayInfo['balance_kredits'] >= rouletteSpinPrice || $userPayInfo['balance_bonus'] >= rouletteSpinPrice)
			{
				$moneyInfo = array();
				if($userPayInfo['balance_bonus'] >= rouletteSpinPrice)
				{
					$moneyInfo = array(
						'bonusPayment' => 1
					);
					$userPayInfo['balance_bonus'] -= rouletteSpinPrice;
				}
				else
					$userPayInfo['balance_kredits'] -= rouletteSpinPrice;

				try
				{
					$STH = $DBH->prepare('UPDATE `users_balance` SET `balance_kredits` = :balance_kredits, `balance_bonus` = :balance_bonus WHERE `user_id` = :user_id');
					$STH->execute(array(
						'balance_kredits' 	=> intval($userPayInfo['balance_kredits']),
						'balance_bonus' 	=> intval($userPayInfo['balance_bonus']),
						'user_id' 			=> intval($_SESSION['user_data']['user_id'])
					));
				}
				catch(PDOException $e)
				{
					$responseData = array(
					'message' => array(	'text' 	=> 'У сайта временные неполадки, попробуй позже',
										'err' 	=> 1));
					exit(json_encode($responseData));
				}

				$rang = floor($userPayInfo['games_count'] / 5);
				if($rang > 59)
					$rang = 59;
				else if($rang < 0)
					$rang = 0;

				if($userPayInfo['youtube'])
					$responseData = rouletteSpinDataGenarator('youtube', array('header' => 'ВАШ ВЫИГРАШ', 'subHeader' => 'С каждой игрой шансы сильно растут!', 'btnText' => 'ЗАБРАТЬ ОРУЖИЕ'), $rang);
				else
					$responseData = rouletteSpinDataGenarator('common', array('header' => 'ВАШ ВЫИГРАШ', 'subHeader' => 'С каждой игрой шансы сильно растут!', 'btnText' => 'ЗАБРАТЬ ОРУЖИЕ'), $rang);

				try
				{
					$STH = $DBH->prepare('UPDATE `users_balance` SET `balance_kredits` = :balance_kredits WHERE `user_id` = :user_id');
					$STH->execute(array(
						'balance_kredits' 	=> intval($userPayInfo['balance_kredits'] + $responseData['weapon']['price']),
						'user_id' 			=> intval($_SESSION['user_data']['user_id'])
					));
					$STH = $DBH->prepare('UPDATE `users_stats` SET `games_count` = :games_count WHERE `user_id` = :user_id');
					$STH->execute(array(
						'games_count' 		=> intval($userPayInfo['games_count'] + 1),
						'user_id' 			=> intval($_SESSION['user_data']['user_id'])
					));
					$STH = $DBH->prepare('INSERT INTO `roulette_stats` (`user_id`, `weapon_price`, `weapon_img`, `date`) VALUES (:user_id, :weapon_price, :weapon_img, :date)');
					$STH->execute(array(
						'user_id' 			=> intval($_SESSION['user_data']['user_id']),
						'weapon_price'		=> intval($responseData['weapon']['price']),
						'weapon_img'		=> htmlspecialchars($responseData['weapon']['img']),
						'date'				=> date('Y-m-d H:i:s')
					));
				}
				catch(PDOException $e)
				{
					$responseData = array(
					'message' => array(	'text' 	=> 'У сайта временные неполадки, попробуй позже',
										'err' 	=> 1));
					exit(json_encode($responseData));
				}
				$responseData = array_merge($responseData, $moneyInfo);
				if(intval($userPayInfo['games_count'] + 1) % 5 == 0)
					$responseData = array_merge($responseData, array('promoted' => $userPayInfo['games_count'] + 1));
			}
			else
			{
				$responseData = array(
					'message' => array(	'text' 	=> 'Недостаточно средств на вашем балансе!',
										'err' 	=> 1));
			}			
		}
	}

	$DBH = null;	
	exit(json_encode($responseData));
