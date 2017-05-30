<?php
	$rootPath = explode("/", __DIR__);
    array_splice($rootPath, count($rootPath) - 2, 2);
    $rootPath = implode("/", $rootPath);

    require_once($rootPath.'/include/configs/rouletteCfg.php');
    require_once($rootPath.'/include/configs/dbCfg.php');

    $DBH = new PDO('mysql:host='.HOST.';dbname='.DB, Login, Pass);
    $DBH->exec('SET NAMES "utf8"');

    $STH = $DBH->prepare('SELECT `user_id` FROM `users_registered` WHERE `access_token` = "bot"');
    $STH->execute();

    $bots = $STH->fetchAll();

    $randomBot = $bots[mt_rand(0, count($bots) - 1)];

    $STH = $DBH->prepare('DELETE FROM `roulette_stats`
    					  WHERE `date` IN
    					  ( SELECT `date` FROM
    					  	( SELECT `date`
    					  	  FROM `roulette_stats`
    					  	  WHERE `user_id` = :user_id
    					  	  ORDER BY `date` ASC
    					  	  LIMIT 1
    					  	) a
    					  )');
    $STH->execute(['user_id' => $randomBot['user_id']]);

    $weaponAllowedToFake = $weaponsPrices[0];
    array_push($weaponAllowedToFake, $weaponsPrices[1][0]);

    $randomWeapon['price'] = $weaponAllowedToFake[mt_rand(0, count($weaponAllowedToFake) - 1)];   

    $sc_data = array();

    if(file_exists($rootPath.'/include/stats/fakeStats.sc'))
		$sc_data = json_decode(file_get_contents($rootPath.'/include/stats/fakeStats.sc'), true);

	if($randomWeapon['price'] >= $weaponsPrices[1][0]){
		$it = 5;
	    if(count($sc_data) < 5)
	        $it = count($sc_data);

	    for($i = 0; $i < $it; $i++)
	    	if($sc_data[$i]['weapon_price'] == $randomWeapon['price']){
	    		$randomWeapon['price'] = $weaponsPrices[0][mt_rand(0, count($weaponsPrices[0]) - 1)];
	    		break;
	    	}
	}

	$randomWeapon['image'] = $weaponsImgs[$randomWeapon['price']];	

    $STH = $DBH->prepare('INSERT INTO `roulette_stats` (`user_id`, `weapon_price`, `weapon_img`, `date`) VALUES (:user_id, :weapon_price, :weapon_img, :date)');

    $fake_stats_data = array(
    	'user_id' 		=> $randomBot['user_id'],
    	'weapon_price'	=> $randomWeapon['price'],
    	'weapon_img'	=> $randomWeapon['image'],
    	'date'			=> date('Y-m-d H:i:s'));

    $STH->execute($fake_stats_data);   		

  	array_unshift($sc_data, $fake_stats_data);

  	$recordsMaxCount = 3999;
  	while(count($sc_data) > $recordsMaxCount)
  		array_pop($sc_data);

    file_put_contents($rootPath.'/include/stats/fakeStats.sc', json_encode($sc_data));
