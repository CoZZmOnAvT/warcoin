<?php
	require_once('include/configs/vkCfg.php');
	if(isset($_GET['code']))
	{
		$params = array(
			'client_id'     => client_id,
			'client_secret' => client_secret,
	        'code'          => htmlspecialchars(trim($_GET['code'])),
	        'redirect_uri'  => redirect_uri
		);

		$link = 'https://oauth.vk.com/access_token?'.urldecode(http_build_query($params));

		$ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_SSL_VERIFYPEER  => false,
            CURLOPT_SSL_VERIFYHOST  => false,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_URL             => $link       
        ));

        $resault = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($resault, true);

		if(isset($response['access_token']))
		{
			require_once('include/configs/dbCfg.php');
			$DBH = new PDO('mysql:host='.HOST.';dbname='.DB, Login, Pass);
			$DBH->exec('SET NAMES "utf8"');

			$STH = $DBH->prepare('SELECT * FROM `users_registered` WHERE `user_id` = :user_id');
			$STH->execute(array('user_id' => htmlspecialchars($response['user_id'])));

			$user = $STH->fetch(PDO::FETCH_ASSOC);
			if(!$user || $user['access_token'] == 'bot')
			{
				//Регестрация нового пользователя через ВК
				if(!isset($response['email']) || !$response['email'])
					$response['email'] = '';

				$response['referal'] = 0;
				if(@$_SESSION['reg_info']['referal'] != $response['user_id'])
					$response['referal'] = @$_SESSION['reg_info']['referal'];

				if(@$_SESSION['reg_info'])
					unset($_SESSION['reg_info']);

				$STH = $DBH->prepare('SELECT * FROM `users_registered` WHERE `user_id` = :user_id');
				$STH->execute(array('user_id' => intval($response['referal'])));

				$referal = $STH->fetch(PDO::FETCH_ASSOC);
				if(!$referal)
					$response['referal'] = 0;

				//Обновление информации про бота
				if($user['access_token'] == 'bot'){
					$STH = $DBH->prepare('DELETE FROM `users_registered` WHERE `user_id` = :user_id');
					$STH->execute(array('user_id' => htmlspecialchars($response['user_id'])));
				}
				//Создание записи в таблице пользователей	
				$STH = $DBH->prepare('INSERT INTO `users_registered` (`user_id`, `access_token`, `expires_in`, `email`, `referal`) VALUES (:user_id, :access_token, :expires_in, :email, :referal)');
				$STH->execute(array(
					'user_id'      => intval($response['user_id']),
					'access_token' => htmlspecialchars($response['access_token']),
					'expires_in'   => intval($response['expires_in']),
					'email'        => htmlspecialchars($response['email']),
					'referal'      => intval($response['referal'])
				));

				if($user['access_token'] == 'bot'){
					$STH = $DBH->prepare('DELETE FROM `roulette_stats` WHERE `user_id` = :user_id');
					$STH->execute(array('user_id' => htmlspecialchars($response['user_id'])));
				}
				//Создание записи в таблице баланса пользователей
				$STH = $DBH->prepare('DELETE FROM `users_balance` WHERE `user_id` = :user_id');
				$STH->execute(array('user_id' => htmlspecialchars($response['user_id'])));
				$STH = $DBH->prepare('INSERT INTO `users_balance` (`user_id`, `balance_freeze`) VALUES (:user_id, 0)');	
				$STH->execute(array(
					'user_id'      => intval($response['user_id'])
				));
				//Создание записи в таблице статистики пользователей
				$STH = $DBH->prepare('DELETE FROM `users_stats` WHERE `user_id` = :user_id');
				$STH->execute(array('user_id' => htmlspecialchars($response['user_id'])));
				$STH = $DBH->prepare('INSERT INTO `users_stats` (`user_id`, `reg_date`) VALUES (:user_id, :reg_date)');	
				$STH->execute(array(
					'user_id'      => intval($response['user_id']),
					'reg_date'     => date('Y-m-d')
				));

				$_SESSION['user_data']['user_id'] = $response['user_id'];
				$_SESSION['user_data']['access_token'] = $response['access_token'];				

				$DBH = null;
			}
			else
			{
				//Проверка данных пользователя и обновления кода доступа
				if(!isset($response['email']) || !$response['email'])
					$response['email'] = '';
				$STH = $DBH->prepare('UPDATE `users_registered` SET `access_token` = :access_token, `expires_in` = :expires_in, `email` = :email WHERE `user_id` = :user_id');
				$STH->execute(array(
					'user_id'      => intval($response['user_id']),
					'access_token' => htmlspecialchars($response['access_token']),
					'expires_in'   => intval($response['expires_in']),
					'email'        => htmlspecialchars($response['email'])
				));
			}			

			$_SESSION['user_data']['user_id'] = intval($response['user_id']);
			$_SESSION['user_data']['access_token'] = htmlspecialchars($response['access_token']);

			$DBH = null;

			//Конструирование реферальной ссылки
			$longURL = 'http://'.$_SERVER['SERVER_NAME'].'/?ref='.$_SESSION['user_data']['user_id'];
			$_SESSION['user_data']['ref_link'] = URLshortner($longURL);		
		}
		else
			$_SESSION['err_msg'] = 'Ошибка авторизации!';		

		require_once('include/siteScripts/vkSync.php');

		header('Location: /');
    	exit();		
	}
	else
	{
		$url = 'http://oauth.vk.com/authorize';

		$params = array(
	    'client_id'     => client_id,
	    'redirect_uri'  => redirect_uri,
	    'display'       => 'page',
	    'scope'         => 'email,offline,status',
	    'response_type' => 'code'
		);

		$link = $url.'?'.urldecode(http_build_query($params));
		header('Location: '.$link);
	}	
?>