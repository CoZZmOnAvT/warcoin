<?php
    function URLshortner($url){
        $rootPath = explode("/", __DIR__);
        array_splice($rootPath, count($rootPath) - 2, 2);
        $rootPath = implode("/", $rootPath);
        require_once($rootPath.'/include/configs/siteCFG.php');

        $apiURL = 'https://www.googleapis.com/urlshortener/v1/url?key='.googleApiKey;
            
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_SSL_VERIFYPEER  => false,
            CURLOPT_SSL_VERIFYHOST  => false,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_POST            => true,
            CURLOPT_POSTFIELDS      => json_encode(array('longUrl' => $url)),
            CURLOPT_HTTPHEADER      => array("Content-Type: application/json"),
            CURLOPT_URL             => $apiURL          
        ));

        $resault = curl_exec($ch);
        curl_close($ch);

        $resault = json_decode($resault);
        return $resault->id;
    }

    function Random($min = 1, $max = 10){
        $rootPath = explode("/", __DIR__);
        array_splice($rootPath, count($rootPath) - 2, 2);
        $rootPath = implode("/", $rootPath);
        require_once($rootPath.'/include/configs/siteCFG.php');

        $randomOrgApiUrl = 'https://api.random.org/json-rpc/1/invoke';
        $randomOrgJSON = array(
            'jsonrpc'   => '2.0',
            'method'    => 'generateSignedIntegers',
            'id'        => 23319,
            'params'    => array(
                'apiKey'        => randomOrgApiKey,
                'n'             => 1,
                'min'           => $min,
                'max'           => $max,
                'replacement'   => true,
                'base'          => 10
            )            
        );

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_SSL_VERIFYPEER  => false,
            CURLOPT_SSL_VERIFYHOST  => false,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_POST            => true,
            CURLOPT_POSTFIELDS      => json_encode($randomOrgJSON),
            CURLOPT_HTTPHEADER      => array("Content-Type: application/json"),
            CURLOPT_URL             => $randomOrgApiUrl         
        ));

        $resault = curl_exec($ch);
        curl_close($ch);

        $resault = json_decode($resault);
        return $resault;
    }

    function rouletteSpinDataGenarator($user, $textData, $rang = 0){
        $rootPath = explode("/", __DIR__);
        array_splice($rootPath, count($rootPath) - 2, 2);
        $rootPath = implode("/", $rootPath);
        require_once($rootPath.'/include/configs/siteCFG.php');
        require_once($rootPath.'/include/configs/rouletteCfg.php');

        $resault = array();

        $randomResault = Random(0, rouletteCells);
        $randomNum = intval($randomResault->result->random->data[0]);

        $weaponArray = array();
        $countWeaponsPrices = count($weaponsPrices);

        $weaponsInterestRateModifided = $weaponsInterestRate;
        for($i = 0; $i < ceil(count($weaponsInterestRateModifided[$user][0]) / 2); $i++)
            $weaponsInterestRateModifided[$user][0][$i] = $weaponsInterestRate[$user][0][$i] - ($rang / 100);
        for($i = ceil(count($weaponsInterestRateModifided[$user][0]) / 2); $i < count($weaponsInterestRateModifided[$user][0]); $i++)
            $weaponsInterestRateModifided[$user][0][$i] = $weaponsInterestRate[$user][0][$i] + ($rang / 100);

        for ($i = 0; $i < $countWeaponsPrices; $i++)      
            for($j = 0; $j < count($weaponsPrices[$i]); $j++)           
                for ($c=0; $c < rouletteCells*$weaponsInterestRateModifided[$user][$i][$j]/100; $c++)                 
                    array_push($weaponArray, $weaponsPrices[$i][$j]);        

        shuffle($weaponArray);

        $weapon = array('price'     => $weaponArray[$randomNum],
                        'img_src'   => $weaponsImgs[$weaponArray[$randomNum]]);

        $rouletteCommonContent = '
                <li><img src="/resource/images/weapons/1.png" height="130px" width="230px" align="right"></li>
                <li><img src="/resource/images/weapons/2.png" height="130px" width="230px" align="right"></li>  
                <li><img src="/resource/images/weapons/12.png" height="130px" width="230px" align="right"></li>                 
                <li><img src="/resource/images/weapons/5.png" height="130px" width="230px" align="right"></li>  
                <li><img src="/resource/images/weapons/9.png" height="130px" width="230px" align="right"></li>  
                <li><img src="/resource/images/weapons/7.png" height="130px" width="230px" align="right"></li>  
                <li><img src="/resource/images/weapons/11.png" height="130px" width="230px" align="right"></li> 
                <li><img src="/resource/images/weapons/10.png" height="130px" width="230px" align="right"></li> 
                <li><img src="/resource/images/weapons/6.png" height="130px" width="230px" align="right"></li>  
                <li><img src="/resource/images/weapons/13.png" height="130px" width="230px" align="right"></li> 
                <li><img src="/resource/images/weapons/8.png" height="130px" width="230px" align="right"></li>  
                <li><img src="/resource/images/weapons/3.png" height="130px" width="230px" align="right"></li>
                <li><img src="/resource/images/weapons/4.png" height="130px" width="230px" align="right"></li>';

        $randomOrgVerify = "
            <form class='randomOrgApi' action='https://api.random.org/verify' method='post' target='_blank'>
                <input type='hidden' name='format' value='json' />
                <input type='hidden' name='random' value='".htmlspecialchars(json_encode($randomResault->result->random))."' />
                <input type='hidden' name='signature' value='".htmlspecialchars($randomResault->result->signature)."' />
                <input type='submit' value='Проверить результат от Random.org' />
            </form>";

        $PrizeBlock = '
            <div id="RoulettePrize">
                <header>
                    <p>'.$textData['header'].'</p>
                    <p>'.$textData['subHeader'].'</p>
                </header>
                <div id="weaponBlock">
                    <p><img src="'.$weapon['img_src'].'" width="250px" height="150px" /></p>
                    <p><span>'.number_format($weapon['price'], 0, '', ' ').' КРЕДИТОВ</span></p>
                </div>'.
                $randomOrgVerify
                .'<div class="agreeButton" onclick="documentScroll.enableScroll();$(\'#winnerBlock\').css({\'display\':\'none\'});$(\'#winnerBlock\').text(\' \');"><p>'.$textData['btnText'].'</p></div>
            </div>';

        $rouletteContent = array();
        $weaponsImgsKeys = array_keys($weaponsImgs);

        for ($i = 0; $i < 44; $i++)
            array_push($rouletteContent, '<li><img src="'.$weaponsImgs[$weaponsImgsKeys[mt_rand(0, count($weaponsImgsKeys) - 6)]].'" height="130px" width="230px" align="right" /></li>');
        for ($i = 0; $i < 3; $i++)
            $rouletteContent[mt_rand(40, 43)] = '<li><img src="'.$weaponsImgs[$weaponsImgsKeys[mt_rand(0, count($weaponsImgsKeys) - 2)]].'" height="130px" width="230px" align="right" /></li>';

        array_push($rouletteContent, '<li><img src="'.$weapon['img_src'].'" height="130px" width="230px" align="right" /></li>');
        
        for ($i = 0; $i < 15; $i++)                 
            array_push($rouletteContent, '<li><img src="'.$weaponsImgs[$weaponsImgsKeys[mt_rand(0, count($weaponsImgsKeys) - 6)]].'" height="130px" width="230px" align="right" /></li>');
        for ($i = 0; $i < 3; $i++)
            $rouletteContent[mt_rand(45, 50)] = '<li><img src="'.$weaponsImgs[$weaponsImgsKeys[mt_rand(0, count($weaponsImgsKeys) - 2)]].'" height="130px" width="230px" align="right" /></li>';
        

        $rouletteContent = implode('', $rouletteContent);
        $test_spin = false;
        if($user == 'test' || $user == 'god')
            $test_spin = true;

        $resault = array(
            'weapon'                => array(
                'price'                 => $weapon['price'],
                'img'                   => $weapon['img_src']
            ),
            'PrizeBlock'            => $PrizeBlock,
            'rouletteContent'       => $rouletteContent,
            'rouletteCommonContent' => $rouletteCommonContent,
            'currentSpinPrice'      => rouletteSpinPrice,
            'test_spin'             => $test_spin);

        return $resault;
    }

    function loteryLastWinnerData(){
        $rootPath = explode("/", __DIR__);
        array_splice($rootPath, count($rootPath) - 2, 2);
        $rootPath = implode("/", $rootPath);
        require_once($rootPath.'/include/configs/dbCfg.php');
        $DBH = new PDO('mysql:host='.HOST.';dbname='.DB, Login, Pass);
        $DBH->exec('SET NAMES "utf8"');        

        $STH = $DBH->prepare('SELECT * FROM `lotery_log` WHERE `status` = "done" ORDER BY `date` DESC LIMIT 1');
        $STH->execute();
        $loteryData = $STH->fetch(PDO::FETCH_ASSOC);

        $STH = $DBH->prepare('SELECT * FROM `users_info` WHERE `user_id` = :user_id');
        $STH->execute(array('user_id' => $loteryData['winner_id']));
        $winnerData = $STH->fetch(PDO::FETCH_ASSOC);

        $loteryWinnerBlock = '
            <div id="loteryWinner">
                <header>
                    <p>ПОБЕДИТЕЛЬ</p>
                </header>
                <div id="loteryWinnerInfo">
                    <p><img src="'.$winnerData['photo_200'].'" width="200px" height="200px" /></p>
                    <p><span>'.$winnerData['user_name'].'</span></p>
                    <p>сорвал банк</p>
                    <p><span>'.number_format($loteryData['bank'], 0, '.', ' ').' КРЕДИТОВ</span></p>
                    <a href="/profile/'.$winnerData['user_id'].'" target="_blank"></a>
                </div>
                <div class="agreeButton" onclick="documentScroll.enableScroll();$(\'#winnerBlock\').css({\'display\':\'none\'});$(\'#winnerBlock\').text(\' \');"><p>Принять</p></div>
            </div>';

        $STH = $DBH->prepare('SELECT * FROM `lotery_members` WHERE `lotery_id` = :lotery_id ORDER BY `tikets_bought` DESC');
        $STH->execute(array('lotery_id' => $loteryData['id']));

        $loteryRouletteData = array('id' => array(), 'html' => array());

        while ($allMembers = $STH->fetch(PDO::FETCH_ASSOC))
            for($i = 0; $i< $allMembers['tikets_bought']; $i++)
                array_push($loteryRouletteData['id'], $allMembers['user_id']);

        while(count($loteryRouletteData['id']) > 60)
            array_splice($loteryRouletteData['id'], mt_rand(0, count($loteryRouletteData['id']) - 1), 1);

        while(count($loteryRouletteData['id']) < 60)
            array_push($loteryRouletteData['id'], $loteryRouletteData['id'][mt_rand(0, count($loteryRouletteData['id']) - 1)]);

        shuffle($loteryRouletteData['id']);
        $loteryRouletteData['id'][44] = $winnerData['user_id'];

        for ($i=0; $i < 60; $i++){ 
            $STH = $DBH->prepare('SELECT `photo_200` FROM `users_info` WHERE `user_id` = :user_id');
            $STH->execute(array('user_id' => $loteryRouletteData['id'][$i]));
            $tmp = $STH->fetch(PDO::FETCH_ASSOC);
            $loteryRouletteData['html'][$i] = '<li><img src="'.$tmp['photo_200'].'" height="80px" width="80px"></li>';
        }

        $isWinner = false;
        if ($_SESSION['user_data']['user_id'] === $winnerData['user_id'])
            $isWinner = true;

        $resault = array(
            'isWinner'              => $isWinner,
            'bank'                  => $loteryData['bank'],
            'loteryRouletteData'    => implode('', $loteryRouletteData['html']),
            'loteryWinnerBlock'     => $loteryWinnerBlock);

        return json_encode($resault);
    }

    function loteryDataWinnerChoose($loteryData){
        $rootPath = explode("/", __DIR__);
        array_splice($rootPath, count($rootPath) - 2, 2);
        $rootPath = implode("/", $rootPath);
        require_once($rootPath.'/include/configs/siteCFG.php');

        require_once($rootPath.'/include/configs/dbCfg.php');
        $DBH = new PDO('mysql:host='.HOST.';dbname='.DB, Login, Pass);
        $DBH->exec('SET NAMES "utf8"');        

        $STH = $DBH->prepare('UPDATE `lotery_log` SET `status` = "done" WHERE `status` = "pending"');
        $STH->execute();

        $STH = $DBH->prepare('SELECT * FROM `lotery_members` WHERE `lotery_id` = :lotery_id');
        $STH->execute(array('lotery_id' => $loteryData['id']));

        $members = array();

        while ($tmp = $STH->fetch(PDO::FETCH_ASSOC))
            array_push($members, array($tmp['user_id'], $tmp['tikets_bought']));

        $loteryMembers = array();
        foreach ($members as $value)            
                for ($i=0; $i < $value[1]; $i++)
                    array_push($loteryMembers, $value[0]);      

        shuffle($loteryMembers);

        $randomResault = Random(0, count($loteryMembers) - 1);
        $randomNum = intval($randomResault->result->random->data[0]);

        $loteryWinner = $loteryMembers[$randomNum];        

        $STH = $DBH->prepare('SELECT `user_name`, `user_screen_name`, `photo_200`, `photo_50` FROM `users_info` WHERE `user_id` = :user_id');
        $STH->execute(array('user_id' => $loteryWinner));
        $loteryWinnerInfo = $STH->fetch(PDO::FETCH_ASSOC);

        $STH = $DBH->prepare('SELECT `balance_kredits` FROM `users_balance` WHERE `user_id` = :user_id');
        $STH->execute(array('user_id' => $loteryWinner));
        $winnerBalance = $STH->fetch(PDO::FETCH_ASSOC);
        $winnerBalance = $winnerBalance['balance_kredits'];

        $STH = $DBH->prepare('UPDATE `users_balance` SET `balance_kredits` = :balance_kredits  WHERE `user_id` = :user_id');
        $STH->execute(array('user_id'           => $loteryWinner,
                            'balance_kredits'   => $winnerBalance + $loteryData['bank']));
        $STH = $DBH->prepare('UPDATE `lotery_log` SET `winner_id` = :winner_id WHERE `id` = :lotery_id');
        $STH->execute(array(
            'winner_id' => $loteryWinner,
            'lotery_id' => $loteryData['id']
        ));
        $STH = $DBH->prepare('UPDATE `lotery_members` SET `won` = true WHERE `user_id` = :user_id AND `lotery_id` = :lotery_id');
        $STH->execute(array(
            'user_id' => $loteryWinner,
            'lotery_id' => $loteryData['id']
        ));
    }

    function on_line(){        
        $rootPath = explode("/", __DIR__);
        array_splice($rootPath, count($rootPath) - 2, 2);
        $rootPath = implode("/", $rootPath);
        require_once ($rootPath.'/include/configs/dbCfg.php');
        $DBH = new PDO('mysql:host='.HOST.';dbname='.DB, Login, Pass);
        $DBH->exec('SET NAMES "utf8"');

        // Time from last activity on server (seconds)
        $tfla = 60;

        // Очищаем из таблицы записи где пользователь не исполнял никаких дейсвий на протяжении `$tfla` секунд или текущий IP встречается в таблице
        if(@$_SESSION['user_data'])
        {
            $STH = $DBH->prepare('DELETE FROM `online_users` WHERE `unix` + :tfla < :currTime OR (`user_ip` = :user_ip AND `user_id` = :user_id)');
            $STH->execute(array(
                'tfla'      => $tfla,
                'currTime'  => time(),
                'user_ip'   => htmlspecialchars($_SERVER['REMOTE_ADDR']),
                'user_id'   => $_SESSION['user_data']['user_id']
            ));

            // Вставляем в таблицу текущего пользователя
            $STH = $DBH->prepare('INSERT INTO `online_users` (`user_ip`, `user_id`, `unix`) VALUES (:user_ip, :user_id, :unix)');
            $STH->execute(array(
                'user_ip'   => htmlspecialchars($_SERVER['REMOTE_ADDR']),
                'user_id'   => $_SESSION['user_data']['user_id'],
                'unix'      => time()            
            ));
        }
        else
        {
            $STH = $DBH->prepare('DELETE FROM `online_users` WHERE `unix` + :tfla < :currTime');
            $STH->execute(array(
                'tfla'      => $tfla,
                'currTime'  => time()
            ));
        }

        //Подсчёт активных записей таблицы `online_users`
        $STH = $DBH->prepare('SELECT COUNT(*) AS `count` FROM `online_users`');
        $STH->execute();
        $online_count = $STH->fetch(PDO::FETCH_ASSOC);

        $DBH = null;
        return $online_count['count'];
    }
    function getUserHostAddress(){
        $ip = '';
        if (!empty($_SERVER['HTTP_X_REAL_IP'])){
            $ip=$_SERVER['HTTP_X_REAL_IP'];
        }
        elseif (!empty($_SERVER['HTTP_CLIENT_IP'])){
            $ip=$_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else{
            $ip=$_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }