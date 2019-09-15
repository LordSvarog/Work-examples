<?php
// Client token id
$tokens= [];
$res= mquery ("SELECT `token` FROM `push`");
while ($token= $res-> fetch_assoc()){
  $tokens[]=$token['token'];
}

const URL= 'https://fcm.googleapis.com/fcm/send';
// Server key
const API_KEY= '';
const TTL= 604800;

//Делим весь массив токенов на блоки по 1000
$all_tokens= array_chunk($tokens,1000);

//Отправляем уведомления за раз 1000 пользователей
foreach($all_tokens as $tokens_chunk){
  $request_body = [
    "registration_ids" => $tokens_chunk,
    "time_to_live" => TTL,
    "notification" => [
      'title' => 'Прогноз погоды',
      'body' => 'Прогноз на 8 марта',
      'icon' => 'https://www.foreca.ru/img/foreca_logo.png',
      'click_action' => 'https://www.foreca.ru/Russia/Kovrov?details=20190308',
    ],
  ];
  $fields = json_encode($request_body);

  $request_headers = [
    'Content-Type: application/json',
    'Authorization: key=' . API_KEY,
  ];

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, URL);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
  curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  $response = curl_exec($ch);
  curl_close($ch);

  echo $response;
}
exit;