<?php
	//Обновление данных ВК в бд о пользователе с `user_id` для ускорения работы сайта
	$user_params = array(
		'user_ids' => $_SESSION['user_data']['user_id'],
        'fields' => 'first_name,last_name,screen_name,photo_big,photo_50',
        'access_token' => $_SESSION['user_data']['access_token']
	);

	$url = 'https://api.vk.com/method/users.get?'.urldecode(http_build_query($user_params));
	
	$ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_SSL_VERIFYPEER  => false,
        CURLOPT_SSL_VERIFYHOST  => false,
        CURLOPT_RETURNTRANSFER  => true,
        CURLOPT_URL             => $url      
    ));

    $resault = curl_exec($ch);
    curl_close($ch);

	$user_data = json_decode($resault, true);

	if(isset($user_data['response'][0]['uid']))
	{
		$user_data = $user_data['response'][0];
		require_once('include/configs/dbCfg.php');
		$DBH = new PDO('mysql:host='.HOST.';dbname='.DB, Login, Pass);
		$DBH->exec('SET NAMES "utf8"');

		$STH = $DBH->prepare('  INSERT INTO `users_info` (`user_id`, `user_name`, `user_screen_name`, `photo_200`, `photo_50`)
                                VALUES                  (:user_id, :user_name, :user_screen_name, :photo_200, :photo_50)
                                ON DUPLICATE KEY
                                UPDATE `user_name`=:user_name, `user_screen_name`=:user_screen_name, `photo_200`=:photo_200, `photo_50`=:photo_50');
        $STH->execute( array(
            'user_id'           => $user_data['uid'],
            'user_name'         => $user_data['first_name'].' '.$user_data['last_name'],
            'user_screen_name'  => $user_data['screen_name'],
            'photo_200'         => $user_data['photo_big'],
            'photo_50'          => $user_data['photo_50']));
	}
	else
	{
		unset($_SESSION['user_data']);
		$_SESSION['err_msg'] = 'Невозможно подключится к сервисам Вконтакте, попробуйте позже';
		header('Location: /');
		exit();
	}