<?php
include_once "lib/recaptcha.inc.php";

session_start();
$results=array();

global $_PAGE;

$ID_SUBJECT = 2;

$maskedinput= '<script type= "text/javascript">
								jQuery(function($){
									$("#phone").mask("+7(999)999-9999");
								});
							</script>';
$time_to_call= array (
	'Сейчас',
	'00-00',
	'01-00',
	'02-00',
	'03-00',
	'04-00',
	'05-00',
	'06-00',
	'07-00',
	'08-00',
	'09-00',
	'10-00',
	'11-00',
	'12-00',
	'13-00',
	'14-00',
	'15-00',
	'16-00',
	'17-00',
	'18-00',
	'19-00',
	'20-00',
	'21-00',
	'22-00',
	'23-00',
);

$form_data=
   frm_input(L('Имя','Name'),$results,true)
   ."<div class='form-inline'>"
   .str_replace ('required', 'required id="phone"', frm_input(L('Телефон','Phone'),$results, true, 'small'))
	 .$maskedinput
   .frm_select (L('Удобное время для звонка','Time for a call'), $time_to_call, $results, true, 'small')
	 //frm_input(L('Удобное время для звонка','Time for a call'),$results, true, 'small')
   ."</div>";
################

$contacts_info = getContactsInfo($ID_SUBJECT);

//print_r($contacts_info['mails']);exit;

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



            Mailer(join(' ',$contacts_info['mails']),"Обратный звонок","Необходимо перезвонить:<br><br>".$contacts_info['text']."<br>".infoTable($results['data']));

            Redirect("?ok=1");
        }
    }


    if($_REQUEST[ok]){


    	$_RETURN_DATA='<div class=\'h3 lined\'><span>'.L('Обратный звонок','Callback').'</span></div>

		'.L('Ваша заявка отправлена! Вам перезвонят.','Your reuest has been submitted! You will get a call back.').'<br><br>
		
			<button class=\'bbtn\' onclick="closeWindow();">'.L('Закрыть окно','Close').'</button>
		';


    }
    else{

    	global $_PAGE;
    	$_PAGE['retitle']=L('ОНЛАЙН КОНСУЛЬТАНТ','REQUEST A CALL');

    $_RETURN_DATA.="
    
     <form method='post' name='frm' id='frm'>
     <input type='hidden' name='go' value='1'>
		 <input type='hidden' name='token' id='token'>
		 <input type='hidden' name='action' id='action'>
          
     <div class='frm_ph'>
     ".($err?GenTbl("<div class='error'>$err</div>"):'')."
     ".($contacts_info['text']?$contacts_info['text']."<br>":"")."
     $form_data
     </div>

    <button class='bbtn'>".L("Мы вам перезвоним","Send")."</button>
        <div class='form_personal'>".L("Нажимая на кнопку, вы даете <a href='/soglasie/' target='_blank'>согласие</a> на обработку своих персональных данных<br>и соглашаетесь с <a href='/policy/' target='_blank'>политикой конфиденциальности</a>.","By clicking on the button you <a href='/soglasie/' target='_blank'>consent</a> to the processing of your personal data<br>and agree with the <a href='/policy/' target='_blank'>privacy policy</a>.")."</div>
    </form>
		<script>$captcha</script>
    ";
    }


