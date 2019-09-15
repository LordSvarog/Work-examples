<?php
include_once "lib/recaptcha.inc.php";

session_start();
$results=array();

$ID_SUBJECT = 1;
$form_data=
   frm_input('Имя#Name|fullname',$results,true)."
 ".frm_input('E-Mail|from',$results,true, 'med', 'email');
################

    $err='';
    if($_REQUEST[go]){
			
			if (!$recaptcha){
				$err= 'Вы не прошли проверку на человечность!';
			}
			
    	if($results['need']){
    		$err='Заполните все поля правильно!';
	    }

        if(!$err){

				$tm=md5(mktime());
				mquery("
				REPLACE INTO `subscribe` set 
				`mail`='^S',
				`id_agency`='^N',
				`name`='^S',
				`hash` = '^S',
				info='^S'
				",
					$_REQUEST[from],$GLOBALS['agency']['id'],$_REQUEST[fullname],$tm,var_export($_SERVER, true));

	        	mquery("UPDATE `subscribe`set hash='',info='^S' where hash='^S'",$_GET[code], var_export($_SERVER, true));



				Mailer($_REQUEST[from],"Подписка - Подтверждение","
			
						Спасибо за то, что подписались на наши новости. Мы будем держать Вас в курсе самых новых объектов. 
						<br>
			                     для подтверждения подписки пройдите по ссылке <br>
			                     <a href='http://{$GLOBALS[agency][host]}/subscribe/?code=$tm'>http://{$GLOBALS[agency][host]}/subscribe/?code=$tm</a>
			                     <br>");

            Redirect("?ok=1");
        }
    }



    if($_REQUEST[ok]){ $_RETURN_DATA='<div class=\'h3 lined\' style="margin:0px; text-align: center; margin-top:10px;"><span>Подписка успешно оформлена</span></div>'; }
    else{


    $_RETURN_DATA.="
    
    <div class='h3 lined'><span>".L("Оформление подписки","Subscribe")."</span></div>
    
   

     <form method='post' name='frm' id='frm'>
     <input type='hidden' name='go' value='1'>
		 <input type='hidden' name='token' id='token'>
		 <input type='hidden' name='action' id='action'>
          
     <div class='frm_ph'>
     ".($err?GenTbl("<div class='error'>$err</div>"):'')."
     ".$contacts_info['text']."<br>
     $form_data
     </div>

    <button class='bbtn'>".L("Отправить","Send")."</button>
        <div class='form_personal'>".L("Нажимая на кнопку, вы даете <a href='/soglasie/' target='_blank'>согласие</a> на обработку своих персональных данных<br>и соглашаетесь с <a href='/policy/' target='_blank'>политикой конфиденциальности</a>.","By clicking on the button you <a href='/soglasie/' target='_blank'>consent</a> to the processing of your personal data<br>and agree with the <a href='/policy/' target='_blank'>privacy policy</a>.")."</div>
    </form>
		<script>$captcha</script>
    ";
    }


