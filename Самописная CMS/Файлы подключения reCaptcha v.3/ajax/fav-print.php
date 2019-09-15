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

        	$mail='';

global $obj;


			$_REQUEST[view]='table';

	        $mail.=$obj->genList(array(favorites=>1, for_mail=>true));




	      //  echo $mail;
	      //  exit;



				Mailer($_REQUEST[from],"Избранные объекты","
			
						Мы подготовили список Ваших избранных объектов ниже. 
						<br>
						
						<center>$mail</center>
			              " );

	        
	        
	        
            Redirect("?ok=1");

	        exit;
        }
    }



    if($_REQUEST[ok]){ $_RETURN_DATA='Избранные объекты успешно отправлены.'; }
    else{


    $_RETURN_DATA.="
    
    <div class='h3 lined'><span>Отправка избранного на почту</span></div>
    
   

     <form method='post' name='frm' id='frm'>
     <input type='hidden' name='go' value='1'>
		 <input type='hidden' name='token' id='token'>
		 <input type='hidden' name='action' id='action'>
          
     <div class='frm_ph'>
     ".($err?GenTbl("<div class='error'>$err</div>"):'')."
     $form_data
     </div>

    <button class='bbtn'>Отправить</button>
        <div class='form_personal'>Нажимая на кнопку, вы даете <a href='/soglasie/' target='_blank'>согласие</a> на обработку своих персональных данных<br>и соглашаетесь с <a href='/policy/' target='_blank'>политикой конфиденциальности</a>.</div>
    </form>
		<script>$captcha</script>
    ";
    }


