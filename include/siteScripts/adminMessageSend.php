<?php
require_once('include/configs/siteCFG.php');

$data = array();
$userMessageText = htmlspecialchars($_POST['message']);

if(strlen($userMessageText) < 10 || strlen($userMessageText) > 200)
	exit(json_encode(array(	'message' => array(	'text' => 'Не корректное сообщение! Длинна сообщения может быть от 10 символов до 200 символов!',
							   		'err' => 1))));

$pattern = "\w{0,5}[хx]([хx\s\!@#\$%\^&*+-\|\/]{0,6})[уy]([уy\s\!@#\$%\^&*+-\|\/]{0,6})[ёiлeеюийя]\w{0,7}|\w{0,6}[пp]([пp\s\!@#\$%\^&*+-\|\/]{0,6})[iие]([iие\s\!@#\$%\^&*+-\|\/]{0,6})[3зс]([3зс\s\!@#\$%\^&*+-\|\/]{0,6})[дd]\w{0,10}|[сcs][уy]([уy\!@#\$%\^&*+-\|\/]{0,6})[4чkк]\w{1,3}|\w{0,4}[bб]([bб\s\!@#\$%\^&*+-\|\/]{0,6})[lл]([lл\s\!@#\$%\^&*+-\|\/]{0,6})[yя]\w{0,10}|\w{0,8}[её][bб][лске@eыиаa][наи@йвл]\w{0,8}|\w{0,4}[еe]([еe\s\!@#\$%\^&*+-\|\/]{0,6})[бb]([бb\s\!@#\$%\^&*+-\|\/]{0,6})[uу]([uу\s\!@#\$%\^&*+-\|\/]{0,6})[н4ч]\w{0,4}|\w{0,4}[еeё]([еeё\s\!@#\$%\^&*+-\|\/]{0,6})[бb]([бb\s\!@#\$%\^&*+-\|\/]{0,6})[нn]([нn\s\!@#\$%\^&*+-\|\/]{0,6})[уy]\w{0,4}|\w{0,4}[еe]([еe\s\!@#\$%\^&*+-\|\/]{0,6})[бb]([бb\s\!@#\$%\^&*+-\|\/]{0,6})[оoаa@]([оoаa@\s\!@#\$%\^&*+-\|\/]{0,6})[тnнt]\w{0,4}|\w{0,10}[ё]([ё\!@#\$%\^&*+-\|\/]{0,6})[б]\w{0,6}|\w{0,4}[pп]([pп\s\!@#\$%\^&*+-\|\/]{0,6})[иeеi]([иeеi\s\!@#\$%\^&*+-\|\/]{0,6})[дd]([дd\s\!@#\$%\^&*+-\|\/]{0,6})[oоаa@еeиi]([oоаa@еeиi\s\!@#\$%\^&*+-\|\/]{0,6})[рr]\w{0,12}";

if($userMessageText != mb_eregi_replace($pattern, '**CENSORED**', $userMessageText))
	exit(json_encode(array(	'message' => array(	'text' => 'Испольование мата запрещено в сообщениях к администрации, за это вас могут заблокировать сайте!',
							   		'err' => 1))));

$userMessageText = wordwrap($userMessageText, 40, "\r\n");

$message = '<p>Сообщение: '.$userMessageText.'</p><p>Страница VK пользователя: https://vk.com/'.$_SESSION['user_data']['screen_name'].'</p><p>VK_ID пользователя: '.$_SESSION['user_data']['user_id'].'</p>';

$headers= "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=utf-8\r\n";
$headers .= "From: ".adminEmail."\r\n";
$headers .= "Reply-To: DoNotReply@warcoin.tk\r\n";

$mailSend = mail(adminEmail, 'Вопрос от пользователя '.$_SESSION['user_data']['user_name'], $message, $headers);

if($mailSend)
	$data = array(	'message' => array('text' => 'Ваше сообщение отправлено!',
							   'err' => 0));
else 
	$data = array(	'message' => array('text' => 'Сообщение не отправлено, попробуйте позже!',
							   'err' => 1));

exit(json_encode($data));