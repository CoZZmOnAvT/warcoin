<?php
	if(@$_POST['action_type'] == 'siteStats')
	{
		require_once('include/configs/dbCfg.php');
		$DBH = new PDO('mysql:host='.HOST.';dbname='.DB, Login, Pass);
		$DBH->exec('SET NAMES "utf8"');

		//Последние выиграши в рулетке
		$rouletteLastWons = '<li id="lastWons"></li>';
		if(@$_SESSION['user_data'])
		{			
			$STH = $DBH->prepare('SELECT * FROM `roulette_stats` ORDER BY `date` DESC LIMIT 4');
			$STH->execute();
			while($tmp = $STH->fetch(PDO::FETCH_ASSOC))
			{
				$STH_2 = $DBH->prepare('SELECT `user_name` FROM `users_info` WHERE `user_id`=:user_id');
				$STH_2->execute(array('user_id' => $tmp['user_id']));
				$tmp_2 = $STH_2->fetch(PDO::FETCH_ASSOC);
				$tmp_2['user_name'];
				$rouletteLastWons .= '			
					<li><table>
						<tr>
							<td>'.$tmp_2['user_name'].'</td><td rowspan="2"><img height="50px" src="/resource/images/cash.png" alt="Деньги" align="right" /></td>
						</tr>
						<tr>
							<td>'.number_format($tmp['weapon_price'], 0, '', ' ').' кредитов</td>
						</tr>
					</table>
					<a href="/profile/'.$tmp['user_id'].'"></a></li>';
			}
		}		

		//Общий выиграш на сайте
		$creditsTotalWon = 0;
		$STH = $DBH->prepare('SELECT SUM(`weapon_price`) AS `sum` FROM `roulette_stats`');
		$STH->execute();
		$tmp = $STH->fetch(PDO::FETCH_ASSOC);
		$creditsTotalWon += $tmp['sum'];
		$STH = $DBH->prepare('SELECT SUM(`bank`) AS `sum` FROM `lotery_log` WHERE `status` = "done"');
		$STH->execute();
		$tmp = $STH->fetch(PDO::FETCH_ASSOC);
		$creditsTotalWon += $tmp['sum'];

		//Пользователей зарегестрировано на сайте
		$STH = $DBH->prepare('SELECT Count(*) AS `count` FROM `users_registered`');
		$STH->execute();
		$usersTotalRegistered = $STH->fetch(PDO::FETCH_ASSOC);

		$lastNumber = ($usersTotalRegistered['count'] + 29781) % 10;
		if($lastNumber == 1)
			$usersTotalRegisteredCase = 'ь';
		else if($lastNumber > 1 && $lastNumber < 5)
			$usersTotalRegisteredCase = 'я';
		else if($lastNumber > 4 || $lastNumber == 0)
			$usersTotalRegisteredCase = 'ей';

		$usersOnline = on_line() + 13 + mt_rand(0,2);

		$lastNumber = $usersOnline % 10;
		$usersOnlineCase = '';
		if($lastNumber > 4 || $lastNumber == 0 || ($usersOnline > 9 && $usersOnline < 20))
			$usersOnlineCase = 'ей';
		else if($lastNumber == 1)
			$usersOnlineCase = 'ь';
		else if($lastNumber > 1 && $lastNumber < 5)
			$usersOnlineCase = 'я';
					

		$response = array(
			'rouletteLastWons' 			=> $rouletteLastWons,
			'creditsTotalWon'			=> $creditsTotalWon + 10658796,
			'usersTotalRegistered'		=> $usersTotalRegistered['count'] + 29781,
			'usersTotalRegisteredCase'	=> $usersTotalRegisteredCase,		
			'usersOnline'				=> $usersOnline,
			'usersOnlineCase'			=> $usersOnlineCase
		);

		$DBH = null;
		exit(json_encode($response));
	}
	else if(@$_POST['action_type'] == 'currBalance')
	{
		require_once('include/configs/dbCfg.php');
		$DBH = new PDO('mysql:host='.HOST.';dbname='.DB, Login, Pass);
		$DBH->exec('SET NAMES "utf8"');

		$STH = $DBH->prepare('SELECT `balance_kredits`, `balance_bonus` FROM `users_balance` WHERE `user_id` = :user_id');
		$STH->execute(array('user_id' => $_SESSION['user_data']['user_id']));
		$data = $STH->fetch(PDO::FETCH_ASSOC);

		exit(json_encode($data));
	}
