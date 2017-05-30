<?php
	$profile_data = array('owner' => false);

	$user_id = intval(preg_replace("/[^0-9]/", '', $Module));
	if(!$user_id)
	{
		header('Location: /');
		exit();
	}
	else if(intval($user_id) == $_SESSION['user_data']['user_id'])
	{
		$profile_data = $_SESSION['user_data'];
		$profile_data['owner'] = true;

		//Просчёт игр до следуйщего ранга пользователя
		$profile_data['next_rang'] = 5 - ($profile_data['games_count'] % 5);
		if($profile_data['next_rang'] == 1)
			$profile_data['next_rang'] = '<span>'.$profile_data['next_rang'].'</span> ИГРУ';
		else if($profile_data['next_rang'] < 5)
			$profile_data['next_rang'] = '<span>'.$profile_data['next_rang'].'</span> ИГРЫ';
		else
			$profile_data['next_rang'] = '<span>'.$profile_data['next_rang'].'</span> ИГР';

		//Подгрузка последних выиграшей
		require_once('include/configs/dbCfg.php');
		$DBH = new PDO('mysql:host='.HOST.';dbname='.DB, Login, Pass);
		$DBH->exec('SET NAMES "utf8"');

		$STH = $DBH->prepare('SELECT `weapon_price`, `weapon_img`, `date` FROM `roulette_stats` WHERE `user_id` = :user_id ORDER BY `date` DESC LIMIT 50');
		$STH->execute( array( 'user_id' => intval($user_id) ) );

		$tmp = $STH->fetch(PDO::FETCH_ASSOC);
		$profile_data['latestWinnings'] = '';
		if(!$tmp){
			$profile_data['latestWinnings'] = '<p>Пользователь пока не наполнил свой склад...</p>';
		}
		else{
			do{
				$profile_data['latestWinnings'] .= '
					<li>
						<img src="'.$tmp['weapon_img'].'">
						<p>'.number_format($tmp['weapon_price'], 0, '', ' ').' КРЕДИТОВ</p>
					</li>';
			}
			while($tmp = $STH->fetch(PDO::FETCH_ASSOC));
		}

		$STH = $DBH->prepare('SELECT `referal` FROM `users_registered` WHERE `user_id` = :user_id');
		$STH->execute( array( 'user_id' => intval($user_id) ) );
		$tmp = $STH->fetch(PDO::FETCH_ASSOC);
		$referal_data = array();
		if($tmp['referal'])
		{
			$referal_data['id']		= intval($tmp['referal']);
			$STH = $DBH->prepare('	SELECT 	`users_info`.`user_name`, `users_info`.`photo_50`, `users_stats`.`games_count`
								  	FROM 	`users_info`
								  	INNER JOIN `users_stats` ON `users_info`.`user_id` = `users_stats`.`user_id`
								  	WHERE `users_info`.`user_id` = :user_id');
			$STH->execute( array( 'user_id' => $referal_data['id'] ) );
			$tmp = $STH->fetch(PDO::FETCH_ASSOC);

			$referal_data['name'] 	= $tmp['user_name'];
			$referal_data['games'] 	= $tmp['games_count'];
			$referal_data['photo'] 	= $tmp['photo_50'];
		}
	}	
	else
	{
		require_once('include/configs/dbCfg.php');
		$DBH = new PDO('mysql:host='.HOST.';dbname='.DB, Login, Pass);
		$DBH->exec('SET NAMES "utf8"');

		$STH = $DBH->prepare('SELECT 	`users_stats`.`god_mode`,
										`users_stats`.`games_count`,
										DATE_FORMAT(`users_stats`.`reg_date`, "%d-%m-%Y") AS reg_date
			FROM `users_registered`
			INNER JOIN `users_balance` ON `users_registered`.`user_id` = `users_balance`.`user_id`
			INNER JOIN `users_stats`   ON `users_balance`.`user_id`    = `users_stats`.`user_id`
			WHERE `users_registered`.`user_id` = :user_id');

		$STH->execute( array( 'user_id' => intval($user_id) ) );

		$sqlData = $STH->fetch(PDO::FETCH_ASSOC);

		if(!$sqlData)
		{
			$_SESSION['err_msg'] = 'Профиль с таким id не найден!';
			header('Location: /');
			exit();
		}
		else
		{
			$profile_data = array_merge($profile_data, $sqlData);

			//Подсчёт всех рефералов пользователя `user_id`
			$STH = $DBH->prepare('SELECT COUNT(*) AS `user_refs` FROM `users_registered` WHERE `referal` = :ref_ID');
			$STH->execute( array( 'ref_ID' => intval($user_id) ) );

			$ref_count = $STH->fetch(PDO::FETCH_ASSOC);
			$profile_data = array_merge($profile_data, $ref_count);


			$STH = $DBH->prepare('	SELECT *, `user_screen_name` as screen_name, `users_info`.`photo_200` as photo_big
							FROM `users_info` WHERE `user_id` = :user_id');
			$STH->execute(array('user_id' => intval($user_id)));
			$tmp = $STH->fetch(PDO::FETCH_ASSOC);
			if($tmp)
			{
				$profile_data = array_merge($profile_data, $tmp);
			}
			else
			{
				$params = explode(',', $user_params['fields']);
				foreach ($params as $value)		
					$profile_data[$value] = 0;

				$profile_data['first_name'] = 'Пользователь покинул проэкт';
				$profile_data['last_name'] = '';
				$profile_data['photo_big'] = 'http://vk.com/images/camera_200.png';
				$profile_data['screen_name'] = 'id'.intval($user_id);
			}

			//Просчёт ранга пользователя
			$profile_data['rang'] = floor($profile_data['games_count']/5);
			if($profile_data['rang'] > 59 || $profile_data['god_mode'])
				$profile_data['rang'] = 59;

			//Просчёт игр до следуйщего ранга пользователя
			$profile_data['next_rang'] = 5 - ($profile_data['games_count'] % 5);
			if($profile_data['next_rang'] == 1)
				$profile_data['next_rang'] = '<span>'.$profile_data['next_rang'].'</span> ИГРУ';
			else if($profile_data['next_rang'] < 5)
				$profile_data['next_rang'] = '<span>'.$profile_data['next_rang'].'</span> ИГРЫ';
			else
				$profile_data['next_rang'] = '<span>'.$profile_data['next_rang'].'</span> ИГР';

			//Подгрузка последних выиграшей
			$STH = $DBH->prepare('SELECT `weapon_price`, `weapon_img`, `date` FROM `roulette_stats` WHERE `user_id` = :user_id ORDER BY `date` DESC LIMIT 50');
			$STH->execute( array( 'user_id' => intval($user_id) ) );

			$tmp = $STH->fetch(PDO::FETCH_ASSOC);
			$profile_data['latestWinnings'] = '';
			if(!$tmp){
				$profile_data['latestWinnings'] = '<p>Пользователь пока не наполнил свой склад...</p>';
			}
			else{
				do{
					$profile_data['latestWinnings'] .= '
						<li>
							<img src="'.$tmp['weapon_img'].'">
							<p>'.number_format($tmp['weapon_price'], 0, '', ' ').' КРЕДИТОВ</p>
						</li>';
				}
				while($tmp = $STH->fetch(PDO::FETCH_ASSOC));
			}
		}
		$DBH = null;
	}

?>