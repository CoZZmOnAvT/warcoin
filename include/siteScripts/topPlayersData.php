<?php
	require_once('include/configs/dbCfg.php');
	$DBH = new PDO('mysql:host='.HOST.';dbname='.DB, Login, Pass);
	$DBH->exec('SET NAMES "utf8"');

	$topPlayersData = array();
	$user_pos = 0;
	$STH = $DBH->prepare('	SELECT 	`users_stats`.`user_id`,
									`users_stats`.`games_count`,
									`users_info`.`user_name`,
									`users_info`.`user_screen_name`,
									`users_info`.`photo_200`,
									`users_info`.`photo_50`
							FROM `users_stats`
							INNER JOIN `users_info` ON `users_stats`.`user_id`=`users_info`.`user_id`
							ORDER BY `users_stats`.`games_count` DESC
							LIMIT 100');
	$STH->execute();
	$i = 0;
	while($tmp = $STH->fetch(PDO::FETCH_ASSOC))
	{
		array_push($topPlayersData, $tmp);
		if($tmp['user_id'] == $_SESSION['user_data']['user_id'])
			$user_pos = $i;
		$i++;
	}
