<?php
include_once "lib/recaptcha.inc.php";

session_start();
$results=array();

$ID_SUBJECT = 9;
$form_data=
   frm_input('Имя#Name',$results,true)."
   "."<div class='form-inline'>"."
 ".frm_input('Телефон#Phone',$results, true, 'small')."
 ".frm_input('Дата трансфера#Date',$results, true, 'small')."
   "."</div>"."

  ".frm_input('Откуда#Location',$results, true)."".frm_input('Куда#Destination',$results, true)."

 ".frm_text('Комментарий#Notes',$results)."
";

################

$contacts_info = getContactsInfo();

    $err='';
    if($_REQUEST[go]){
			
			if (!$recaptcha){
				$err= 'Вы не прошли проверку на человечность!';
			}

    	if($results['need']){
    		$err='Заполните все поля правильно!';
	    }

        if(!$err){

        	$res=mquery("SELECT `email` FROM `feedback_subjects` WHERE `id`='^N'",$ID_SUBJECT);
            $row=$res->fetch_array();

	        if($row['email']){ $contacts_info['mails'][]=$row['email']; }

            mquery("INSERT INTO `feedback_list` SET id_agency='{$GLOBALS['agency']['id']}',`date`=NOW(),`id_subject`='$ID_SUBJECT',`data`='^S'",json_encode($results['data']));

            session_destroy();

            Mailer(join(' ',$contacts_info['mails']),"Обратная связь","Отправлено сообщение обратной связи:<br><br>".$contacts_info['text']."<br>".infoTable($results['data']));

            Redirect("?ok=1");
        }
    }



    if($_REQUEST[ok]){ $_RETURN_DATA='Ваше сообщение отправлено.'; }
    else{


    	global $_PAGE;
    	$_PAGE['retitle']='Заказ трансфера';

    $_RETURN_DATA.="
    
    
    ".L("<b>Заказ трансфера возможен при условии гарантированной оплаты номера.</b><br>
	Уважаемые гости, просим Вас оформлять заявку на бронирование трансфера не менее чем за 1 час до подачи автомобиля.<br>
	В течение 30 минут менеджер свяжется с Вами. В случае, если Вам не позвонили, проверьте правильно ли Вы указали номер телефона.","<b>Transfer order is possible provided the payment is guaranteed.</b><br>
Dear guests, we ask you to make an order for a transfer reservation at least 1 hour before the car is served.<br>
Within 30 minutes the manager will contact you. In case you have not received a phone call, check if you have entered the correct phone number.")."
	<br><br>
   

     <form method='post' name='frm' id='frm'>
     <input type='hidden' name='go' value='1'>
		 <input type='hidden' name='token' id='token'>
		 <input type='hidden' name='action' id='action'>
          
     <div class='frm_ph'>
     ".($err?GenTbl("<div class='error'>$err</div>"):'')."
     ".($contacts_info['text']?"{$contacts_info['text']}<br>":"")."
     $form_data
     </div>

    <button class='bbtn'>".L("Отправить","Send")."</button>
    <div class='form_personal'>".L("Нажимая на кнопку, вы даете <a href='/soglasie/' target='_blank'>согласие</a> на обработку своих персональных данных<br>и соглашаетесь с <a href='/policy/' target='_blank'>политикой конфиденциальности</a>.","By clicking on the button you <a href='/soglasie/' target='_blank'>consent</a> to the processing of your personal data<br>and agree with the <a href='/policy/' target='_blank'>privacy policy</a>.")."</div>
    </form>
		<script>$captcha</script>
    ";
    }


