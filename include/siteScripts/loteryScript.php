<?php
	require_once('include/configs/siteCFG.php');

	if(@$_POST['ajax_lotery_start'] == true){
		if(!isset($_SESSION['user_data']) || !is_array($_SESSION['user_data']))
			exit('ACCESS DENIED!');

		require_once('include/configs/dbCfg.php');
	    $DBH = new PDO('mysql:host='.HOST.';dbname='.DB, Login, Pass);
	    $DBH->exec('SET NAMES "utf8"');

	    $STH = $DBH->prepare('SELECT `id`, `date`, `status`, `bank` FROM `lotery_log` WHERE `status` = "pending" ORDER BY `date` DESC LIMIT 1');
	    $STH->execute();
	    $lotery = $STH->fetch(PDO::FETCH_ASSOC);
	    if ($lotery){
	    	$currTime = time();
	    	$loteryStartTime = strtotime($lotery['date']);
	    	$differenceInTime = ($currTime - $loteryStartTime)/60;

	    	if($differenceInTime >= 15){
	    		loteryDataWinnerChoose($lotery);
	    		exit(loteryLastWinnerData());
	    	} else {
	    		exit("NOT YET!");
	    	}
	    }
	    else{
	    	exit(loteryLastWinnerData());
	    }		
	}
	else if(@$_POST['buy_ticket'] == true){
		sleep(mt_rand(1, 3));

		if(!isset($_SESSION['user_data']) || !is_array($_SESSION['user_data']))
			exit('ACCESS DENIED!');

		if($_POST['ticketCount'] < 1 || $_POST['ticketCount'] > 50)
			exit(json_encode(array('message' => array(	'text' 	=> 'Не корректное количество билетов!',
														'err'	=> 1))));
		require_once('include/configs/siteCFG.php');

		require_once('include/configs/dbCfg.php');
	    $DBH = new PDO('mysql:host='.HOST.';dbname='.DB, Login, Pass);
	    $DBH->exec('SET NAMES "utf8"');

	    $STH = $DBH->prepare('	SELECT 	`users_balance`.`balance_kredits`,
	    								`users_balance`.`balance_freeze`,
	    								`users_stats`.`god_mode`
	    						FROM `users_balance`
	    						INNER JOIN `users_stats` ON `users_balance`.`user_id` = `users_stats`.`user_id`
	    						WHERE `users_balance`.`user_id`=:user_id');
	    $STH->execute(array('user_id' => intval($_SESSION['user_data']['user_id'])));
	    $user_balance = $STH->fetch(PDO::FETCH_ASSOC);

	    if($user_balance['balance_kredits'] < $_POST['ticketCount'] * loteryTiketPrice)
	    	exit(json_encode(array('message' => array(	'text' 	=> 'Не достаточно кредитов! Вы можете приобрести: '.(floor($user_balance['balance_kredits'] / loteryTiketPrice) .' билетов'),
														'err'	=> 1))));
	    if($user_balance['balance_freeze'] || $user_balance['god_mode'])
			exit(json_encode(array('message' => array(	'text' 	=> 'Вам запрещено участие в лотерее!',
														'err'	=> 1))));

		$STH = $DBH->prepare('UPDATE `users_balance` SET `balance_kredits` = :balance_kredits WHERE `users_balance`.`user_id`=:user_id');
		$STH->execute(array('balance_kredits'	=> $user_balance['balance_kredits'] - (intval($_POST['ticketCount']) * loteryTiketPrice),
							'user_id' 			=> intval($_SESSION['user_data']['user_id'])));

		$STH = $DBH->prepare('SELECT * FROM `lotery_log` WHERE `status` = "freeze" OR `status` = "pending" ORDER BY `date` DESC LIMIT 1');
		$STH->execute();
		$lotery = $STH->fetch(PDO::FETCH_ASSOC);
		if($lotery){
			$currTime = time();
	    	$loteryStartTime = strtotime($lotery['date']);
	    	$differenceInTime = ($currTime - $loteryStartTime);

	    	if($differenceInTime >= 890)/*За 10 секудн до начала лотереи*/{
	    		exit(json_encode(array('message' => array(	'text' 	=> 'Слишком поздно для покупки билетов, дождитесь следуйщей лотереи!',
															'err'	=> 1))));
	    	}

			$STH = $DBH->prepare('SELECT COUNT(*) as all_members FROM `lotery_members` WHERE `lotery_id` = :lotery_id');
			$STH->execute(array('lotery_id' => $lotery['id']));
			$all_members = $STH->fetch(PDO::FETCH_ASSOC);
			$all_members = $all_members['all_members'];

			if($all_members >= 100)
				exit(json_encode(array('message' => array(	'text' 	=> 'Лотерея переполнена участниками, дождитесь следуйщей',
															'err'	=> 1))));			

			$STH = $DBH->prepare('UPDATE `lotery_log` SET `bank` = :bank WHERE `id` = :lotery_id');
			$STH->execute(array('bank'		=> $lotery['bank'] + floor(intval($_POST['ticketCount']) * loteryTiketPrice * (1 - loteryCommission / 100)),
								'lotery_id' => $lotery['id']));

			$STH = $DBH->prepare('SELECT * FROM `lotery_members` WHERE `lotery_id` = :lotery_id');
            $STH->execute(array('lotery_id' => $lotery['id']));
            $members = $STH->fetch(PDO::FETCH_ASSOC);
			
			$memberFound = false;
			$memberTickets = 0;
			do{
				if($members['user_id'] == intval($_SESSION['user_data']['user_id'])){
					$memberFound = true;
					$memberTickets = $members['tikets_bought'];
					break;
				}
			}
			while($members = $STH->fetch(PDO::FETCH_ASSOC));

			if($memberFound){
				$STH = $DBH->prepare('UPDATE `lotery_members` SET `tikets_bought` = :tikets_bought WHERE `lotery_id` = :lotery_id AND `user_id` = :user_id');
				$STH->execute(array('tikets_bought'	=> $memberTickets + intval($_POST['ticketCount']),
									'lotery_id' 	=> $lotery['id'],
									'user_id' 		=> intval($_SESSION['user_data']['user_id'])));
			} else {
				$STH = $DBH->prepare('INSERT INTO `lotery_members` (`user_id`, `lotery_id`, `tikets_bought`, `date`) VALUES (:user_id, :lotery_id, :tikets_bought, :date)');
				$STH->execute(array('user_id' 		=> intval($_SESSION['user_data']['user_id']),
									'lotery_id' 	=> $lotery['id'],
									'tikets_bought'	=> intval($_POST['ticketCount']),
									'date'			=> date("Y-m-d H:i:s")));
			}

			$STH = $DBH->prepare('SELECT COUNT(*) AS all_members FROM `lotery_members` WHERE `lotery_id` = :lotery_id');
			$STH->execute(array('lotery_id' => $lotery['id']));
			$all_members = $STH->fetch(PDO::FETCH_ASSOC);
			$all_members = $all_members['all_members'];

			if($all_members > 1 && $lotery['status'] == "freeze"){				
				$STH = $DBH->prepare('UPDATE `lotery_log` SET `status` = "pending", `date` = :date WHERE `id` = :lotery_id');
				$STH->execute(array('lotery_id' => $lotery['id'],
									'date'		=> date('Y-m-d H:i:s')));
			} else if($all_members < 2) {
				$STH = $DBH->prepare('UPDATE `lotery_log` SET `status` = "freeze" WHERE `id` = :lotery_id');
				$STH->execute(array('lotery_id' => $lotery['id']));
			}

			exit(json_encode(1));
		} else {
			$STH = $DBH->prepare('INSERT INTO `lotery_log` (`bank`, `status`, `date`) VALUES (:bank, :status, :date)');
			$STH->execute(array('bank'		=> floor(intval($_POST['ticketCount']) * loteryTiketPrice * (1 - loteryCommission / 100)),
								'status' 	=> "freeze",
								'date'		=> date('Y-m-d H:i:s')));

			$STH = $DBH->prepare('SELECT * FROM `lotery_log` WHERE `status` = "freeze" ORDER BY `date` DESC LIMIT 1');
			$STH->execute();
			$NEWlotery =  $STH->fetch(PDO::FETCH_ASSOC);
			$STH = $DBH->prepare('INSERT INTO `lotery_members` (`user_id`, `lotery_id`, `tikets_bought`, `date`) VALUES (:user_id, :lotery_id, :tikets_bought, :date)');
			$STH->execute(array('user_id' 		=> intval($_SESSION['user_data']['user_id']),
								'lotery_id' 	=> $NEWlotery['id'],
								'tikets_bought'	=> intval($_POST['ticketCount']),
								'date'			=> date("Y-m-d H:i:s")));

			exit(json_encode(1));
		}		
	}
	else if(@$_POST['update_lotery_data'] == true){
		if(!isset($_SESSION['user_data']) || !is_array($_SESSION['user_data']))
			exit('ACCESS DENIED!');

		require_once('include/configs/dbCfg.php');
	    $DBH = new PDO('mysql:host='.HOST.';dbname='.DB, Login, Pass);
	    $DBH->exec('SET NAMES "utf8"');

	    $response = array();

	    $STH = $DBH->prepare('SELECT `balance_kredits` FROM `users_balance` WHERE `user_id` = :user_id');
	    $STH->execute(array('user_id' => $_SESSION['user_data']['user_id']));
	    $tmp = $STH->fetch(PDO::FETCH_ASSOC);

	    $response['user_balance'] = $tmp['balance_kredits'];

	    $STH = $DBH->prepare('SELECT * FROM `lotery_log` WHERE `status` = "freeze" OR `status` = "pending"');
	    $STH->execute();
	    $tmp = $STH->fetch(PDO::FETCH_ASSOC);

	    $response['restart_timer'] = false;

	    $response['lotery_roulette'] = '';
	    $response['lotery_users_list'] = '	<tr>
												<th>№</th>
												<th>ПОЛЬЗОВАТЕЛЬ</th>
												<th>БИЛЕТОВ КУПЛЕНО</th>			
											</tr>';
		$response['bank'] = 0;

	    if ( $tmp ) {
	    	$response['bank'] = $tmp['bank'];

	    	if($tmp['status'] == "pending"){
	    		$currTime = time();
				$loteryStartTime = strtotime($tmp['date']) + 15 * 60;
				$differenceInTime = ($loteryStartTime - $currTime);

				if($differenceInTime < 0)
					$differenceInTime = 0;

				$minutes = intval($differenceInTime/60);
				if($minutes < 10)
					$minutes = '0'.$minutes;

				$seconds = intval($differenceInTime%60);
				if($seconds < 10)
					$seconds = '0'.$seconds;

				$time = $minutes.':'.$seconds;
	    		$response['restart_timer'] = $time;
	    	}

	    	$STH = $DBH->prepare('  SELECT  `lotery_members`.`tikets_bought`,
		                                    `users_info`.`user_name`,
		                                    `users_info`.`photo_200`,
		                                    `users_info`.`photo_50`
		                            FROM `lotery_members`
		                            INNER JOIN `users_info` ON `lotery_members`.`user_id` = `users_info`.`user_id`
		                            WHERE `lotery_members`.`lotery_id` = :lotery_id
		                            ORDER BY `lotery_members`.`date` DESC');
		    $STH->execute(array('lotery_id' => $tmp['id']));
		    $it = 0;
		    while($tmp = $STH->fetch(PDO::FETCH_ASSOC)){
		    	$it++;
		    	$response['lotery_roulette'] .= '<li><img src="'.$tmp['photo_200'].'" height="80px" width="80px" /></li>';
		    	$response['lotery_users_list'] .= '<tr>
														<td>'.$it.'</td>
														<td>
															<div>
																<img src="'.$tmp['photo_50'].'">
																<span>'.$tmp['user_name'].'</span>
															</div>				
														</td>
														<td>'.$tmp['tikets_bought'].'</td>
													</tr>';
		    }
	    } else {
	    	$response['lotery_users_list'] .= '<tr><td colspan="4">Участников на данный момент нет!</td></tr>';
	    }

	    $STH = $DBH->prepare('SELECT * FROM `lotery_log` WHERE `status` = "done" ORDER BY `date` DESC LIMIT 1');
	    $STH->execute();
	    $lotery = $STH->fetch(PDO::FETCH_ASSOC);

	    if($lotery){
	    	$STH = $DBH->prepare('SELECT * FROM `users_info` WHERE `user_id` = :user_id');
		    $STH->execute(array('user_id' => $lotery['winner_id']));
		    $userInfo = $STH->fetch(PDO::FETCH_ASSOC);

	    	$response['last_winner_block'] ='<header>ПОСЛЕДНИЙ ПОБЕДИТЕЛЬ</header>
											<img src="'.$userInfo['photo_200'].'" height="200px" />
											<p>'.$userInfo['user_name'].'</p>
											<p>БАНК: <span>'.number_format($lotery['bank'], 0, '.', ' ').'К</span></p>
											<a href="/profile/'.$userInfo['user_id'].'" target="_blank"></a>';
		} else {
			$response['last_winner_block'] = '';
		}

	    exit(json_encode($response));
	}
	else if(@$loadPage){
		if(!isset($_SESSION['user_data']) || !is_array($_SESSION['user_data']))
			exit('ACCESS DENIED!');

		require_once('include/configs/siteCFG.php');

		$loteryData = array(
			'time' => 'NaN',
			'members' => array(),
			'bank' => 0,
			'lastWinner' => '');

		require_once('include/configs/dbCfg.php');
	    $DBH = new PDO('mysql:host='.HOST.';dbname='.DB, Login, Pass);
	    $DBH->exec('SET NAMES "utf8"');

		$STH = $DBH->prepare('SELECT * FROM `lotery_log` WHERE `status` = "pending" OR `status` = "freeze" ORDER BY `date` DESC LIMIT 1');
	    $STH->execute();
	    $lotery = $STH->fetch(PDO::FETCH_ASSOC);
	    
	    if($lotery)
	    {
	    	$loteryData['bank'] = $lotery['bank'];
	    	if($lotery['status'] != 'freeze'){    		
	    		$currTime = time();
				$loteryStartTime = strtotime($lotery['date']) + 15 * 60;
				$differenceInTime = ($loteryStartTime - $currTime);

				if($differenceInTime < 0)
					$differenceInTime = 0;

				$minutes = intval($differenceInTime/60);
				if($minutes < 10)
					$minutes = '0'.$minutes;

				$seconds = intval($differenceInTime%60);
				if($seconds < 10)
					$seconds = '0'.$seconds;

				$loteryData['time'] = $minutes.':'.$seconds;			
	    	}    	

	    	$STH_s = $DBH->prepare('SELECT * FROM `lotery_members` WHERE `lotery_id` = :lotery_id ORDER BY `date` DESC');
		    $STH_s->execute(array('lotery_id' => $lotery['id']));
		    $members = $STH_s->fetch(PDO::FETCH_ASSOC);   
		    if(!$members){
		    	$loteryData['members']['list'] = '<tr><td colspan="4">Участников на данный момент нет!</td></tr>';
		    	$loteryData['members']['roulette'] = '';
		    } else {
		    	$loteryData['members']['roulette'] = '';
		    	$loteryData['members']['list'] = '';
		    	$it = 0;
		    	do{
		    		$it++;
		    		$STH = $DBH->prepare('SELECT * FROM `users_info` WHERE `user_id` = :user_id');
				    $STH->execute(array('user_id' => $members['user_id']));
				    $userInfo = $STH->fetch(PDO::FETCH_ASSOC);

				    $STH = $DBH->prepare('SELECT `tikets_bought` FROM `lotery_members` WHERE `user_id` = :user_id AND `lotery_id` = :lotery_id');
				    $STH->execute(array(
				    	'user_id' 	=> $members['user_id'],
				    	'lotery_id'	=> $lotery['id']
				    	));
				    $userInfo['tikets_bought'] = $STH->fetch(PDO::FETCH_ASSOC);
				    $userInfo['tikets_bought'] = $userInfo['tikets_bought']['tikets_bought'];

				    $loteryData['members']['roulette'] .= '<li><img src="'.$userInfo['photo_200'].'" height="80px" width="80px" /></li>';
				    $loteryData['members']['list'] .= '	<tr>
															<td>'.$it.'</td>
															<td>
																<div>
																	<img src="'.$userInfo['photo_50'].'">
																	<span>'.$userInfo['user_name'].'</span>
																</div>				
															</td>
															<td>'.$userInfo['tikets_bought'].'</td>
														</tr>';
					
		    	}
		    	while($members = $STH_s->fetch(PDO::FETCH_ASSOC));
		    }
	    }
	    else
	    {
	    	$loteryData['members']['list'] = '<tr><td colspan="4">Участников на данный момент нет!</td></tr>';
		    $loteryData['members']['roulette'] = '';
		    $STH = $DBH->prepare('SELECT * FROM `lotery_log` WHERE `status` = "pending" OR `status` = "freeze" ORDER BY `date` DESC LIMIT 1');
		    $STH->execute();
		    $lotery = $STH->fetch(PDO::FETCH_ASSOC);
	    }

	    $STH = $DBH->prepare('SELECT * FROM `lotery_log` WHERE `status` = "done" ORDER BY `date` DESC LIMIT 1');
	    $STH->execute();
	    $lotery = $STH->fetch(PDO::FETCH_ASSOC);
	    if($lotery){
	    	$STH = $DBH->prepare('SELECT * FROM `users_info` WHERE `user_id` = :user_id');
		    $STH->execute(array('user_id' => $lotery['winner_id']));
		    $userInfo = $STH->fetch(PDO::FETCH_ASSOC);

	    	$loteryData['lastWinner'] ='<div id="lastWinnerBlock">
											<header>ПОСЛЕДНИЙ ПОБЕДИТЕЛЬ</header>
											<img src="'.$userInfo['photo_200'].'" height="200px" />
											<p>'.$userInfo['user_name'].'</p>
											<p>БАНК: <span>'.number_format($lotery['bank'], 0, '.', ' ').'К</span></p>
											<a href="/profile/'.$userInfo['user_id'].'" target="_blank"></a>
										</div>';
		}
	} else {
		exit('ACCESS DENIED!');
	}
