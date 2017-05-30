<?php
	require_once('include/configs/dbCfg.php');
	$DBH = new PDO('mysql:host='.HOST.';dbname='.DB, Login, Pass);
	$DBH->exec('SET NAMES "utf8"');

	//Запрос данных из базы данных по указаному при авторизации `user_id`
	$STH = $DBH->prepare('SELECT 	`users_registered`.`access_token`,
									`users_registered`.`email`,
									`users_registered`.`referal`,
									`users_balance`.`balance_kredits`,
									`users_balance`.`balance_bonus`,
									`users_stats`.`god_mode`,
									`users_stats`.`games_count`,
									`users_stats`.`tickets_count`,
									`users_stats`.`test_spin`,
									DATE_FORMAT(`users_stats`.`reg_date`, "%d-%m-%Y") AS reg_date
		FROM `users_registered`
		INNER JOIN `users_balance` ON `users_registered`.`user_id` = `users_balance`.`user_id`
		INNER JOIN `users_stats`   ON `users_balance`.`user_id`    = `users_stats`.`user_id`
		WHERE `users_registered`.`user_id` = :user_id');

	$STH->execute( array('user_id' => $_SESSION['user_data']['user_id']) );

	$users_locked_data = $STH->fetch(PDO::FETCH_ASSOC);

	if(!$users_locked_data)
	{
		unset($_SESSION['user_data']);
		$_SESSION['err_msg'] = 'Невозможно получить данные об аккаунте, попробуйте позже';
		header('Location: /');
		exit();
	}

	$_SESSION['user_data'] = array_merge($_SESSION['user_data'], $users_locked_data);

	//Подсчёт всех рефералов пользователя `user_id`
	$STH = $DBH->prepare('SELECT COUNT(*) AS `user_refs` FROM `users_registered` WHERE `referal` = :ref_ID');
	$STH->execute( array('ref_ID' => $_SESSION['user_data']['user_id']) );

	$ref_count = $STH->fetch(PDO::FETCH_ASSOC);
	$_SESSION['user_data'] = array_merge($_SESSION['user_data'], $ref_count);

	//Определение баланса пользователя
	if($_SESSION['user_data']['god_mode'])
	{
		$_SESSION['user_data']['balance_bonus'] = 'UNLIMITED';
		$_SESSION['user_data']['balance_kredits'] = 'UNLIMITED';
	}
	else
	{
		$_SESSION['user_data']['balance_bonus'] = number_format($_SESSION['user_data']['balance_bonus'], 0, '', ' ');
		$_SESSION['user_data']['balance_kredits'] = number_format($_SESSION['user_data']['balance_kredits'], 0, '', ' ');
	}

	$STH = $DBH->prepare('	SELECT *, `user_screen_name` as screen_name, `users_info`.`photo_200` as photo_big
							FROM `users_info` WHERE `user_id` = :user_id');
	$STH->execute(array('user_id' => $_SESSION['user_data']['user_id']));
	$tmp = $STH->fetch(PDO::FETCH_ASSOC);

	$_SESSION['user_data'] = array_merge($_SESSION['user_data'], $tmp);	

	//Просчёт ранга пользователя
	$_SESSION['user_data']['rang'] = floor(@$_SESSION['user_data']['games_count']/5);
	if($_SESSION['user_data']['rang'] > 59 || @$_SESSION['user_data']['god_mode'])
		$_SESSION['user_data']['rang'] = 59;

	$DBH = null;
