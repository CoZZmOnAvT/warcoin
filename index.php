<?php
    session_start();
    require_once('include/siteScripts/functions.php');

    //Url Parsing
    if ($_SERVER['REQUEST_URI'] == '/')
    {
        $Page = 'index';
        $Module = 'index';
        $URL_Path = "/";
    }
    else
    {
        $URL_Path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $URL_Parts = explode('/', trim($URL_Path, ' /'));
        $Page = array_shift($URL_Parts);        
        $Module = array_shift($URL_Parts);        
     
        if (!empty($Module))
        {
            $Param = array();
            for ($i = 0; $i < count($URL_Parts); $i++)
            {
                $Param[$URL_Parts[$i]] = $URL_Parts[$i];                
            }
        }
    }    

    //Server Files including block
    if($Page == 'admin' && !$Module){
    	exit('Temporary unavalible!');
    }
    else if($Page == 'ajax' && !$Module){
        //Обновление информации об пользователях онлайн
        on_line();

    	require_once('include/siteScripts/'.htmlspecialchars($_POST['action']).'.php');
    }
    else if($Page == 'balance' && empty($Param)){
        require_once('include/siteScripts/payment.php');
    }
    else if($Page == 'vk' && $Module && !$Param){
        if($Module == 'auth'){
            if (isset($_SESSION['user_data']))
                unset($_SESSION['user_data']);
            
            require_once('include/siteScripts/vkAuth.php');            
        }
        else if($Module == 'logout'){
            if($_GET['jsBlocked'])
                $_SESSION['err_msg'] = 'Включите JavaScript для доступа к сайту!';
            unset($_SESSION['user_data']);
            header('Location: /');
            exit();
        }
        else{
            header('Location: /');
            exit();
        }
    }
    else if ($Page == 'lotery' && $Module == 'check' && !$Param) {
        require_once('include/siteScripts/loteryScript.php');
    }
    else{
    	if(!isset($_SESSION['user_data']) || empty($_SESSION['user_data']) || !is_array($_SESSION['user_data'])){
    		require_once('include/common/authPage.php');
            exit();
        }
    	else{
            //Обновление информации об пользователях онлайн
            on_line();
            
            require_once('include/siteScripts/userGetInfo.php');
    		require_once('template.php');
            exit();
        }
    }
