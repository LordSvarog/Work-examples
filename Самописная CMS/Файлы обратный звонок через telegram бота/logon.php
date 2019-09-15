<?

class mod_logon{


	function mod_logon(){
		include_once "./lib/useproid/auth.lib.php";
	}

	function show_but_mob(){
		session_start();
		$urfa_user = unserialize($_SESSION[urfa_user]);

		if($_REQUEST[ip]){ $_SESSION[ip]=$_REQUEST[ip]; }

		if(!$_SESSION[urfa_user]){

			global $_PRJ;
			$refer="http://".$_PRJ[domain].$_SERVER[REQUEST_URI];
			$refer=str_replace("'","&apos;",$refer);

			$err = $_GET[err]?"<div><font color='EE2222'> $_GET[err]</font></div><br>":"";



			$h=$err?420:360;

			return '

			<a href="#logon_div" class="mob_auth_menu popup">Личный кабинет</a>

			<script>
			'.((($_GET[err]) || ($_GET[logon_frm]))?'$(function(){ $("#logon_btn").click(); })':'').'
			</script>';


		}
		else{
			$urfa_user->refresh_info();
      		return '<a href="/profile/" class="mob_auth_menu loggedin"  onclick=\'$("body").toggleClass("withauthmenu"); return false;\'>Личный кабинет <b>'.$urfa_user->login.'</b> <span class="balance">'.round($urfa_user->balanse,0).' <span class="rub">P</span></span></a>';
		}
	}

	function show_but(){
		session_start();
		$urfa_user = unserialize($_SESSION[urfa_user]);

		if($_REQUEST[ip]){ $_SESSION[ip]=$_REQUEST[ip]; }

		if(!$_SESSION[urfa_user]){

			global $_PRJ;
			$refer="http://".$_PRJ[domain].$_SERVER[REQUEST_URI];
			$refer=str_replace("'","&apos;",$refer);

			$err = $_GET[err]?"<div><font color='EE2222'> $_GET[err]</font></div><br>":"";



			$h=$err?420:360;

			return '

			<div id="logon_div" class="white-popup zoom-anim-dialog mfp-hide" style="width: 400px;  height:'.$h.'px;">

			<center>
			<div style="padding: 20px; padding-top:50px; text-align:center">

			<form action="//'.$_PRJ[domain].'/login/?sec=0"  method="post" id="upid_auth" name="upid_auth" style="height:'.($h-40).'px;width: 360px;" onsubmit=" $(\'#p1ass_val\').val(MD5($(\'#pass_raw\').val())); $(\'#pa1ss_raw\').val(\'\'); ">
				<input type="hidden" name="__prj_id" value="2">
				<input type="hidden" name="__refer" value="http://'.$_PRJ[domain].'/profile/">
				<center>
				<h2>Вход</h2><br>
				'.$err.'
				<table cellpadding="5" cellspacing="0" width="260px">
				<tr>
				<td> Логин<br>
					<input style="margin-top: 4px; width: 260px;" type="text" name="login" value="" class="login" onkeypress="if(event.keyCode==13){this.form.pass.focus();return false;}">
				</td>
				</tr>
				<tr>
				<td><br>Пароль<br>
					<input style="margin-top: 4px; width: 260px;" type="password" name="pass" id="pass_raw" class="login" onkeypress="if(event.keyCode==13){this.form.submit();return false;}">
					
					<input type="hidden" name="pa2ss" id="pass_val">
				</td>
				</tr>
				<tr>	<td align=center> <br>

				 <a href="#" onclick="$(\'#upid_auth\').submit(); return false;" class=gbut>Войти в кабинет</a>

				 </td> </tr>
				<tr>
					<td>	<br>'. ( (Urfa_user::get_ip_grp( $_SERVER[REMOTE_ADDR], 1)) && 0 ?
						'<a href="/register/">Регистрация</a><br><br>' : '').
					'<center><a href="/reminder/">Забыли пароль?</a></center>
					</td>
				</tr>
				</table>
				</center>
			</form>
			</div>
			</center>
			</div>

<!--a href="/reg/" class="menuright">РЕГИСТРАЦИЯ</a-->
			<a href="#logon_div" id="logon_btn" class="popup gbut">Войти в кабинет</a>

			<script>
			'.((($_GET[err]) || ($_GET[logon_frm]))?'$(function(){ $("#logon_btn").click(); })':'').'
			</script>';


		}
		else{
			$urfa_user->refresh_info();
      		return '<!--font class="menuright">'.$urfa_user->login.'</font--><a href="/profile/" class="gbut">Личный кабинет</a>';
		}
	}

	function show(){
		session_start();
		$urfa_user = unserialize($_SESSION[urfa_user]);

		if($_SESSION[urfa_user]){

			$urfa_user->refresh_info();

			/*$err = ((Urfa_user::get_ip_grp( $_SERVER['REMOTE_ADDR'], 1)) != $urfa_user->id_grp) ?
			'<tr>
				<td class="little" colspan=2>Внимание! Текущий ip-адрес не совпадает с ip-группой пользователя.
				Изменение состояния услуг будет производится для пользовательской ip-группы
				</td>
			</tr>' : '';*/

			$b = $urfa_user->balanse;
			$b = preg_replace("/\.00$/","",$b);

			$bb = $urfa_user->balanse_bonus;
			$bb = preg_replace("/\.00$/","",$bb);

			$refcode= $urfa_user->refcode();
			
			global $_PRJ;

			$cur_profile = $urfa_user->get_r_profile();


			$traff='';
			$cur_res = mquery("SELECT * FROM service
                        WHERE sname='^S'", $urfa_user->r_service);
	        if ($cur_row=$cur_res->fetch_array()) {

	        	$traff="Тариф: <a href='/service/' style='color:white;border-color:white;'>$cur_row[name]</a><br>";

	        	$exp_bytes= round( ($cur_row[limit] - $cur_profile['limit_value'])/(1024*1024) ,2);//round ((($cur_profile['download-used']+$cur_profile['upload-used'])/(1024*1024)),2);

	            $cur_row[limit] /= (1024*1024);
	            $cur_row[limit2] /= (1024*1024);
	            //(Израсходовано:".$exp_bytes." Мб)

	            $limit_bytes= round($cur_profile['limit_value']/(1024*1024),2);

	            //
	          //  print_r($cur_profile);
	           // if($_GET[t]){
	                //print_r($cur_profile);
	                if(preg_match("/valid ((.*?) (.*?)\/(.*?)\/(.*?))$/",$cur_profile['comment'],$mtch)){
	                    //echo "{$mtch[1]}<hr>";
	                    $dt=date_parse_from_format("D M/j/Y", $mtch[1]);
	                    $dt_ut=mktime(0, 0, 0, $dt[month], $dt[day], $dt[year]);
	                }
	           //}

		        if ( $cur_row[ type ] == 'unlim_fap' ) {
			        $limit_days_bytes = round( $urfa_user->ref[ 'now_day_traffic' ] / ( 1024 * 1024 ), 2 );
			        $traff .= "Трафика на макс. скорости: <br> <a href='/service/?stat=1#stat' style='color:white;border-color:white;'>$limit_days_bytes Мб</a>";
		        } else {
			        if ( $cur_row[ limit ] != '0' ) {
				        if ( !$cur_row[ limit2 ] ) {
					        $traff .= "Ограничение трафика: (входящий+исходящий): <a href='/service/?stat=1#stat' style='color:white;border-color:white;'>$cur_row[limit] Мб</a><br>израсходовано " . $exp_bytes . " Мб";
				        } else {
					        $traff .= "Трафик: <b>днем:</b> $cur_row[limit]/$cur_row[limit2] Мб<br> Доступно " . $limit_bytes . " Мб";
				        }
			        }
		        }
	        }



			return '
<div class=rnd style="background:#e21100;margin-bottom:50px;">
			<table width="333" cellpadding="0" cellspacing="0" border="0" style="">
<tr style="cursor:pointer;" onclick="document.location.href=\'/profile/\';">
<td  width="40%" style="line-height: 23px;padding-top:50px;padding-left:50px;padding-bottom:3px;" class="name">ID '.$urfa_user->user_id.'<br>'.$urfa_user->login.'</td>
<td style="padding-top:50px;padding-right:50px; text-align: right;"><nobr class="price">'.$b.' <span class=rub>&#8381;</span></nobr></td>
</tr>

<tr><td colspan=2 style="padding-bottom:10px;padding-left:50px;padding-top:15px;color:white;">'.$urfa_user->full_name.'</td></tr>

<tr><td colspan=2 style="padding-bottom:10px;padding-left:50px;padding-top:15px;color:white;line-height: 28px;">
'.$traff.'
</td>
</tr>

'.($urfa_user->may_bonus()?'<tr><td colspan=2 class="bonus_val" style="padding-bottom:5px;padding-left:50px;padding-top:15px;color:white;">

<a href="#how_get_bonus" class="popup" style="float:right; color:white; font-size:12px; border-color:white !important; margin-right:30px;">Как получить?</a>

<div id="how_get_bonus" class=" white-popup zoom-anim-dialog mfp-hide" style="width: 500px;  height:200px; padding: 50px;">
<h3>Как получить бонусные баллы?</h3>

Приглашайте своих друзей для подключения к Wifitochka и получайте бонусные баллы!<br><br>
Ваша ссылка для друзей: <br>

<input type="text" readonly value="http://'.$_PRJ[domain].'/?ref='.$refcode.'" style="width:100%; padding:5px; font-weight:bold;"><br>

<br>
За каждого приглашенного друга <b>500 бонусных рублей</b>*. <br>
<small>* - баллы начисляются только после активации пакета пользователем.</small>

</div>

Бонусный баланс:<br><nobr class="price" style="-">'.$bb.' <span class=rub>&#8381;</span></td></tr>':'').'
<tr><td colspan=2 style="padding-bottom:30px;padding-left:50px;padding-top:10px;color:white;"><a href="/login/quit/" style="color:white;border-color:white;" class=name>Выход</a></td></tr>

</table>
</div>

			';
		}
	}

	function show_call(){
	  return "
      <div id='call_div' class='white-popup zoom-anim-dialog mfp-hide' style='width: 400px;  height:360px;'>
        <center>
        <div style='padding: 20px; padding-top:30px; text-align:center'>
          <form action='/to_call/' method='post' id='call_to_user' name='call_to_user' style='width: 360px;  height:320px;'>
            <input type='hidden' name='go' value='{$_SERVER[REQUEST_URI]}'>
            <center>
            <h2>Заказ звонка</h2>
            <p style='font-size: 12px;'>Оставьте Ваши контактные данные.<br>
              Мы Вам обязательно перезвоним!</p>
            <table cellpadding='5' cellspacing='0' width='260px'>
              <tr>
                <td for='name' class='for_call'>Имя<br>
                  <input id='name' name='name' type='text' placeholder='Введите Ваше имя' class='call'  required>
                </td>
              </tr>
              <tr>
                <td for='email' class='for_call'>Почтовый ящик<br>
                  <input id='email' name='email' type='email' placeholder='example@domain.com' class='call' required>
                </td>
              </tr>
              <tr>
                <td for='phone' class='for_call'>Номер телефона<br>
                  <input id='phone' name='phone' type='phone' placeholder='+7(900)100-1010' class='call' required>
                </td>
              </tr>
            </table><br>
            <input type='submit' value='Отправить' class='cbut save' id=''>
            </center>
          </form>
        </div>
        </center>
      </div>
      
      <script type= \"text/javascript\">
        jQuery(function($){
          $('#phone').mask('+7(999)999-9999');
        });
      </script>

      <a href='#call_div' class='popup cbut nomini' id='call_btn'>Заказать звонок</a>
    ";
  }

  function show_call_mob(){
    return '

			<a href="#call_div" class="mob_auth_menu popup">Заказать звонок</a>
		';
  }
}



?>