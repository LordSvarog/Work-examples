<?php
//код скрипта отправки запроса на сервер reCAPTCHA и получения token
$captcha= "
	let captcha_action = 'add_comment';
     
	grecaptcha.ready(function() {
		grecaptcha.execute('', {action: captcha_action})
			.then(function(token) {
				if (token) {
					document.getElementById('token').value = token;
					document.getElementById('action').value = captcha_action;
				}
			});
	});
";

//валидация веб-формы сервером reCAPTCHA
if ($_REQUEST[go] && !empty($_POST['token']) && !empty($_POST['action'])) {
	$captcha_token = $_POST['token'];
	$captcha_action = $_POST['action'];
	
	$url = 'https://www.google.com/recaptcha/api/siteverify';
	$params = array(
			'secret' => '',
			'response' => $captcha_token,
			'remoteip' => $_SERVER['REMOTE_ADDR']
	);
	 
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	 
	$response = curl_exec($ch);
	if(!empty($response))
		$decoded_response = json_decode($response);
	 
	if ($decoded_response->success && $decoded_response->action == $captcha_action) {
		// обрабатываем данные формы, которая защищена капчей
		$recaptcha= true;
	}else{
		// направим на вывод ошибки, если пользователь оказался ботом
		$recaptcha= false;
	}
}