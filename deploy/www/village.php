<?php
require_once(CORE.'control/ChatController.php');

use app\Controller\ChatController;

if ($error = init(ChatController::PRIV, ChatController::ALIVE)) {
	display_error($error);
	die();
}

$command = in('command');
$controller = new ChatController();
$method = $_SERVER['REQUEST_METHOD']; 

// Switch between the different controller methods.
switch(true){
	case( $method === 'POST' && $command==='receive'):
		$response = $controller->receive();
	case($command === 'index'):
	default:
		$command = 'index';
		$response = $controller->index();
		break;
}
if($response instanceof RedirectResponse){
	$response->send();
} else {
	display_page($response['template'], $response['title'], $response['parts'], $response['options']);
}


