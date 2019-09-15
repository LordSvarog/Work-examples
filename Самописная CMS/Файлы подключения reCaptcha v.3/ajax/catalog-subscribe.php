<?php
include_once "lib/recaptcha.inc.php";

session_start();
$results=array();

$ID_SUBJECT = 1;
$form_data=
frm_input('E-Mail|from',$results,true, 'med', 'email');
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
				REPLACE INTO `object_subscribe` set 
				`mail`='^S',
				`id_agency`='^N',
				`name`='^S',
				`hash` = '^S',
				info='^S'
				",
					$_REQUEST[from],$GLOBALS['agency']['id'],$_REQUEST[fullname],$tm,json_encode($_REQUEST[flt]));


				Mailer($_REQUEST[from],"Подписка на объекты - Подтверждение","
			
						Спасибо за то, что подписались на новые объекты. Мы будем держать Вас в курсе самых новых объектов. 
			                     <br>");

            Redirect("?ok=1");
        }
    }



    if($_REQUEST[ok]){ $_RETURN_DATA='Подписка успешно оформлена.'; }
    else{

    	global $obj;
    	list($cases, $joins, $params_str) = $obj->fltRules($_REQUEST[flt]);

    $_RETURN_DATA.="
    
    <div class='h3 lined'><span>Подписка на результаты</span></div>
   
   

     <form method='post' name='frm' id='frm'>
     <input type='hidden' name='go' value='1'>
		 <input type='hidden' name='token' id='token'>
		 <input type='hidden' name='action' id='action'>
     
     <div class='frm_ph'>
     ".($err?GenTbl("<div class='error'>$err</div>"):'').'
     <div class="frm_row ainput '.$cls.'">
		'.$params_str.'
	</div>
     '.$form_data.'
     </div>

    <button class=bbtn>Подписаться</button>
        <div class=\'form_personal\'>Нажимая на кнопку, вы даете <a href=\'/soglasie/\' target=\'_blank\'>согласие</a> на обработку своих персональных данных<br>и соглашаетесь с <a href=\'/policy/\' target=\'_blank\'>политикой конфиденциальности</a>.</div>
    </form>
		<script>$captcha</script>
    ';
    }


