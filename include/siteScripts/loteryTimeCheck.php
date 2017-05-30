<?php
	$rootPath = explode("/", __DIR__);
    array_splice($rootPath, count($rootPath) - 2, 2);
    $rootPath = implode("/", $rootPath);
	require_once($rootPath.'/include/configs/dbCfg.php');
    $DBH = new PDO('mysql:host='.HOST.';dbname='.DB, Login, Pass);
    $DBH->exec('SET NAMES "utf8"');

    $STH = $DBH->prepare('SELECT `id`, `date`, `status`, `bank` FROM `lotery_log` WHERE `status` = "pending" ORDER BY `date` DESC LIMIT 1');
    $STH->execute();
    $lotery = $STH->fetch(PDO::FETCH_ASSOC);

    if ($lotery){
    	$currTime = time();
    	$loteryStartTime = strtotime($lotery['date']);
    	$differenceInTime = ($currTime - $loteryStartTime)/60;

    	require_once($rootPath.'/include/siteScripts/functions.php');

    	if($differenceInTime >= 15){
    		loteryDataWinnerChoose($lotery);
    	}
    }
    exit();
