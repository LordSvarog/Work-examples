<?php
include_once "lib/recaptcha.inc.php";

session_start();
$results=array();

$ID_SUBJECT = 10;
$form_data=
   frm_input('Имя#Name',$results,true)."
 ".frm_input('Телефон#Phone',$results, true, 'small')."
 ".frm_date('Даты проживания#Dates',$results, true, 'small')."
 ".frm_text('Отзыв#Review',$results,true)."
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


           // print_r($results['data']);exit;

            $r = mquery( "INSERT INTO `guestbook_list` SET
                        `id_agent`='1',
                         `fullname`='^S',
                         `phone`='^S',
                         `data`='^S',
                         `date_deal`='^S'
                         ", $results['data']['Имя'], $results['data']['Телефон'], $results['data']['Отзыв'], $results['data']['Даты проживания'] );

            Redirect("?ok=1");
        }
    }



    if($_REQUEST[ok]){ $_RETURN_DATA=L('Ваше сообщение отправлено.','Your message has been sent'); }
    else{

    	  	global $_PAGE;
    	$_PAGE['retitle']=L('Отзыв гостя','Review');



    $_RETURN_DATA.="
    
    
   

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


