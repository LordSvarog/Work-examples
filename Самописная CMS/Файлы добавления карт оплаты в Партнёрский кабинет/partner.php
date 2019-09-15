<?

ini_set('include_path', get_include_path() . PATH_SEPARATOR . './lib/xls');
require_once "./lib/xls/Spreadsheet/Excel/Writer.php"; 

if($_REQUEST[fast] && $_USR[logon]){
	$res = mquery("SELECT * FROM diler WHERE `login` = '^S'",
		$_REQUEST[fast]);
	$row = $res->fetch_array();


	if($row[id]){

		session_destroy();
		session_start();

		$_SESSION[partner_iden]=$row[id];
		$_SESSION[partner_md5]=md5($row[login]);

		Redirect("/partner/");
		exit();
	}


}

$LNK=$_URI[0];

session_start();

$_PAGE[ptitle]='Партнерский кабинет';

//$_SESSION[partner_iden]=1;

$IS_PARTNER=0; $PARTNER=array();
if($_SESSION[partner_iden]){
    $res = mquery("SELECT * FROM diler WHERE `id`='^S'",
                $_SESSION[partner_iden]);
    $row = $res->fetch_array();
    if($row[id] && md5($row[login]) == $_SESSION[partner_md5]){
        $IS_PARTNER=1;
        $PARTNER=$row;
    }
}

//print_r($_SESSION);

#################

if($_URI[1] == 'quit'){
    $_SESSION[partner_iden]='';
    $_SESSION[partner_md5]='';
                
    Redirect("/$LNK/");
    exit();
}
elseif ($_URI[1]=='chpass') {
        if ($_URI[2]=='save') {
        	if($_POST[password] && !isLogin($_POST[password])){$err.="В пароле можно использовать только символы латинского алфавита и цифры (0-9 A-Z a-z).<br>";}
        
        	if($_POST[password] && $_POST[password]!=$_POST[password2]) {$err.="пароли не совпадают.<br>";}
            if(!$err){

            	if ($_POST[password]){
                    mquery("UPDATE diler SET pass='^S'
                        WHERE id='^N'",
                        $_POST[password],$PARTNER[id]);
                }
                
                Redirect('/$LNK/');
            }
        }

        
        $_PAGE[data] .= ($err?"<br><div class='err' style='color:red;'><font class='title'>Ошибка</font><br>$err</div><br>":'')."
        <form name='prf_fm' action='/$LNK/chpass/save/' method='POST' ENCTYPE='multiptitle/form-data'>
            <h1 class=hh5>Смена пароля партнера</h1><br>
            <table cellpadding='8' cellspacing='1' width='100%' bgcolor='#EFEFEF'>
            <tr>
            <td bgcolor=white> пароль: </td>
            <td> <input name='password' type='text' value='' style='width:200;'></td>
            </tr>
            <tr>
            <td bgcolor=white> подтверждение пароля: </td>
            <td> <input name='password2' type='text' value='' style='width:200;'></td>
            </tr>
            </table>
            
            <br>
            
            <input type=submit value='Сохранить'> &nbsp;
            <input type=button value='Отмена' onclick='GoUrl(\"/$LNK/\");'> 
        </form>    
        ";      
}
elseif ($_URI[1]=='chpass_user') {
	$_PAGE[template]='clear';
        if ($_URI[2]=='save') {
        	if($_POST[password] && !isLogin($_POST[password])){$err.="В пароле можно использовать только символы латинского алфавита и цифры (0-9 A-Z a-z).<br>";}
        
        	if($_POST[password] && $_POST[password]!=$_POST[password2]) {$err.="пароли не совпадают.<br>";}
            if(!$err){

            	if ($_POST[password]){
                    mquery("UPDATE user SET password='^S'
                        WHERE id='^N'",
                        $_POST[password],$_POST[uid]);
                        echo "Пароль изменен!";
                }
                else{
                echo "Пароль не изменен! Указан пустой пароль.";
                }
                
            }
        }
        
           $res = mquery("SELECT * FROM user WHERE `id`='^N'",
                $_GET[uid]);
    $row = $res->fetch_array();

        
        $_PAGE[data] .= ($err?"<br><div class='err' style='color:red;'><font class='title'>Ошибка</font><br>$err</div><br>":'')."
        <form name='prf_fm' action='/$LNK/chpass_user/save/' method='POST' ENCTYPE='multiptitle/form-data'>
        <input type='hidden' name='uid' value='$row[id]'>
            <h1 class=hh5>Смена пароля пользователя</h1><b>$row[login]</b> $row[full_name]<br><br>
            <table cellpadding='8' cellspacing='1' width='100%' bgcolor='#EFEFEF'>
            <tr>
            <td bgcolor=white> пароль: </td>
            <td> <input name='password' type='text' value='' style='width:200;'></td>
            </tr>
            <tr>
            <td bgcolor=white> подтверждение пароля: </td>
            <td> <input name='password2' type='text' value='' style='width:200;'></td>
            </tr>
            </table>
            
            <br>
            
            <input type=submit value='Сохранить'>
        </form>    
        ";      
}

else{
#####

if($IS_PARTNER){


if(!$_URI[1]){ Redirect("/$LNK/stat/"); exit(); }



		$cr=mquery("SELECT COUNT(*) as cnt
    			FROM `user` u  
    			JOIN `ip_group` g ON g.id = u.id_grp
    			WHERE u.id_grp IN (SELECT id FROM ip_group WHERE id_diler = $PARTNER[id])");
    	$crow = $cr->fetch_array();

//	echo $cr->q;


if($_POST[gomess]){

	mquery("REPLACE `const_text` SET `id` = 'partner_message_$PARTNER[id]', `val` = '^S'",$_POST["message"]);

	Redirect("/partner/stat/?tst=1");
}


$r=mquery("SELECT `val` FROM `const_text` WHERE `id`='partner_message_$PARTNER[id]'");
$sl=$r->fetch_array();


$mess=clr2textarea($sl[val]);

    $_PAGE[data]="<br>
    
    ".($_GET[tst]||1?"
    
    <form method=post>
    <h2>Объявление абонентам</h2>
    <input type='hidden' name='gomess' value='1'>
    <textarea name='message' style='height:120px; width:100%; resize:none;'>$mess</textarea>
    <input type=submit value='Сохранить'>
    </form>
    <br><br>
    
    ":"")."
    
    
    ".'<div style="background: #EFEFEF; padding:10px; border-bottom: 1px solid #444444; position:relative;z-index:2;" class="rad10">			
			<table width = "100%">
				<tr>
				<td width="80px" class="hh5">Партнер: </td>
				<td> <font class="hh5">'.$PARTNER[fullname].'</font> </td>
				</tr>
				<tr>
				<td style="padding-top: 7px;">
				<input type=button value="Выход" onclick="GoUrl(\'/'.$LNK.'/quit/\')"></td>
				<td style="padding-top: 7px;"><a href="/'.$LNK.'/chpass/">сменить пароль</a>
				</td>	
				</tr>	
			</table>
	
			</div>'."
	<div style='position: relative; height:20px; width:100%; z-index: 1; '>
	<div class='rad10' style='position:absolute; top:-15px; bottom: 0px; right:0px; left:0px; padding:17px; padding-top:25px; height:20px; z-index: 10000; color:white; background: #222'>
	<a href='/$LNK/stat/' class='whiteb".($_URI[1] == 'stat'?"_sel":"")."'>Статистика</a> &nbsp; 
	<a href='/$LNK/users/' class='whiteb".($_URI[1] == 'users'?"_sel":"")."'>Абоненты ($crow[cnt])</a> &nbsp; 
	<a href='/$LNK/cards/' class='whiteb".($_URI[1] == 'cards'?"_sel":"")."'>Карты оплаты</a>
	</div>		
	</div>
			
	";



  if ($_URI[1]=='stat') {

      $mnu="<input type='radio' id='grps0' name='group' value='0' ".(!$_REQUEST[group]?"checked":"")."/><label for='grps0'>-Все-</label>";

      if(!$_REQUEST[group]){
          $_REQUEST[group]=0;
      }

      $res = mquery("SELECT * FROM ip_group WHERE id_diler='^N'", $PARTNER[id]);
      $n=0;
    while($row = $res->fetch_array()) {
      $n++;
                  #if(!$_REQUEST[group]){
                  #    $_REQUEST[group]=$row[id];
                  #}
      $mnu.="<input type='radio' id='grps$n' name='group' value='$row[id]' ".($row[id] == $_REQUEST[group]?'checked':'')."/><label for='grps$n'>$row[name]</label>";
    }


    $_REQUEST[date_from]=$_REQUEST[date_from]?$_REQUEST[date_from]:strftime("%d.%m.%Y",time()-60*60*24*30);
    $_REQUEST[date_to]=$_REQUEST[date_to]?$_REQUEST[date_to]:strftime("%d.%m.%Y");

    $_PAGE[data].="
    
    <style>
    .tab_norm{
      padding: 4px;
    }
    .tab_sel{
      padding: 4px;
      background-color: #DEDEDE;
    }
    </style>
    
    <script>
    $(function(){
      $('.dtpicker').datepicker($.datepicker.regional['ru']);
      $('#grps').buttonset();
    });
    </script>
    
    <br>"."<div style='background: #EFEFEF; padding:10px; border-top: 1px solid #fff; position:relative;z-index:2;' class='rad10'>	
    <form method=post id='stat_frm'>Группа: 
      
      <input type='hidden' name='go' value='1'>
    <span id='grps'>
    $mnu
    </span>
    
    
    <br><br>
    
    
    
    <b>Вывести статистику платежей</b><br><br>от <input type='text' style='width: 90px' name='date_from' value='$_REQUEST[date_from]' class='dtpicker'>
     до <input type='text' style='width: 90px' name='date_to' value='$_REQUEST[date_to]' class='dtpicker'>
    <br><br>
    <input type='submit' value='Применить'>
    
    <div style='float:right'><a href='#' onclick=\"$('#stat_frm').attr('action','/$LNK/stat/xls/').submit().attr('action','/$LNK/stat/');return false;\"><img src='/img/excel.png' align=absMiddle style='margin-right: 6px;'>Скачать в виде Excel</a></div>
    
    </form>
    
    </div>"."";

    if($_REQUEST[go]){
      $arr=preg_split("/\./",$_POST[date_from]);
        $DateFrom=strtotime("$arr[2]-$arr[1]-$arr[0]");

        $arr=preg_split("/\./",$_POST[date_to]);
        $DateTo=strtotime("$arr[2]-$arr[1]-$arr[0]");

        $Group_Arr=array();

        if($_REQUEST[group]){
          $Group_Arr[]=$_REQUEST[group];
        }
        else{
          $res = mquery("SELECT * FROM ip_group WHERE id_diler='^N'", $PARTNER[id]);
          while($row = $res->fetch_array()) {
            $Group_Arr[]=$row[id];
          }
        }

        $Group=join(',',$Group_Arr);

  $r=mquery("SELECT p.date_pay AS `date`,p.amount,p.type,'pays' AS `tp`,u.`full_name`,u.`id` AS `user_id`
            FROM `pays` p 
            JOIN `pays_bills` pb ON pb.`id` = p.`id_bill`
            JOIN `user` u ON u.`id` = pb.`id_user`
            WHERE p.`date_pay`>='^N' and p.`date_pay`<='^N' and u.`id_grp` IN ($Group) and p.`state` = 'payed' 
            
            UNION
            
            SELECT p.date,p.amount,p.id_type,'mobi' AS `tp`,u.`full_name`,u.`id` AS `user_id`
            FROM `payment` p
            JOIN `user` u ON u.`id` = p.`id_user_to`
            WHERE  p.`date`>='^N' and p.`date`<='^N' and u.`id_grp` IN ($Group) and p.id_type=7
            
            ORDER BY `date`
        ",$DateFrom,$DateTo,$DateFrom,$DateTo);


        global $_DFN;

        $for_xls=array();

        $list='';$summ=0;
        while($row = $r->fetch_array()) {
          $date=strftime('%d.%m.%y, %H:%M',$row[date]);

          if($row[tp] == 'mobi'){
            $type='MobiДеньги';
          }
          else{
            $type="{$_DFN[pays][type][$row[type]]}";
          }

          $list.="<tr>
          <td>$type
          <td>$date
          <td align=right>$row[amount] руб.
          <td>$row[user_id]
          <td>$row[full_name]
          </tr>";

          $for_xls[]=array($type,$date,"$row[amount] руб.",$row[user_id],$row[full_name]);

          $summ+=$row[amount];
        }


              $list.="<tr bgcolor='#DADADA'>
          <td>Сумма
          <td>
          <td align=right>$summ руб.
          <td>
          <td>
          </tr>";

        $nds_summ=$summ;
        $nds=0;

        if(!$PARTNER[nds]){
        //$nds_summ=($summ / 118) * (100-18);
        //$nds=round(($summ / 118) * (18),2);
          $nds=0;
        $nds_summ=round($summ-$nds,2);

        }

        if(1){

              $now_proc=0;
              foreach(preg_split("/(\n\r|\r\n|\n|\r)/",$PARTNER[proc_case]) as $l){
                  if(preg_match("/^\[(.*?)\-(.*?)\:(.*?)\]$/",$l,$m)){
                      $from=$m[1];
                      $to=$m[2];
                      $proc=$m[3];

                      if($from*1 <= $summ*1 && $to*1 > $summ*1 && !$now_proc){
                          $now_proc=$proc;
                          //echo "$from --- $to ::: $proc<br>";
                      }

                  }
              }

              if($now_proc){ $PARTNER[proc]=$now_proc; }

        $proc=round(($nds_summ / 100) * $PARTNER[proc],2);



          /*$list.="<tr bgcolor='#EFEFEF'>
          <td>НДС
          <td>
          <td align=right>$nds руб.
          <td><b>-18%</b>
          <td>
          </tr>";
        */
        $list.="<tr bgcolor='#EFEFEF'>
          <td>Вознаграждение
          <td>
          <td align=right>$proc руб.
          <td><b>$PARTNER[proc]%</b>
          <td>
          </tr>";
        }

          ##
          if(!$list){
              $list="<tr><td colspan='5'>Не найдено ни одного платежа!</tr>";
          }
          ##

        $_PAGE[data].="<br><table class='itbl' border='0' cellpadding='8' cellspacing='1' width='100%'>
        <tr bgcolor='#666666'>
          <td style='color:white' width='100px'>Тип
          <td style='color:white' width='130px'>Дата
          <td align=right style='color:white' width='120px'>Сумма
          <td style='color:white' width='35px'>ACC
          <td style='color:white'>Пользователь
        </tr>
          
        $list
        </table>";

      if($_URI[2] == 'xls'){
          $xls = new Spreadsheet_Excel_Writer();
          $xls->send("partner_report_$PARTNER[login](".strftime("%d-%m-%y").").xls");
          $sheet = $xls->addWorksheet('Отчет');

          $format_tit = $xls->addFormat();
          $format_tit->setBgColor('black');
          $format_tit->setColor('white');


          $format_info = $xls->addFormat();
          $format_info->setBold();


          $format_itogo = $xls->addFormat();
          $format_itogo->setBgColor('gray');
          $format_itogo->setColor('white');

          $format_itogo_1 = $xls->addFormat();
          $format_itogo_1->setBgColor('silver');
          $format_itogo_1->setColor('white');

          $i=0;
          $sheet->write($i,0,"Отчет партнера");
          $sheet->write($i,2,$PARTNER[fullname],$format_info);

          $i=1;
          $sheet->write($i,0,"Диапазон от $_POST[date_from] до $_POST[date_to]");

          $i=3;
          $sheet->write($i,0,"Тип",$format_tit);
          $sheet->write($i,1,"Дата",$format_tit);
          $sheet->write($i,2,"Сумма",$format_tit);
          $sheet->write($i,3,"АСС",$format_tit);
          $sheet->write($i,4,"Пользователь",$format_tit);

          $sheet->setColumn(1,1,14);
          $sheet->setColumn(2,2,14);
          $sheet->setColumn(4,4,36);

          foreach($for_xls as $v){
            $i++;
            $y=0;
            foreach($v as $f){

              $sheet->write($i,$y,$f);
              $y++;
            }
          }

          $i++;
          # itogo
          $sheet->write($i,0,"Сумма",$format_itogo);
          $sheet->write($i,1," ",$format_itogo);
          $sheet->write($i,2,"$summ руб.",$format_itogo);
          $sheet->write($i,3," ",$format_itogo);
          $sheet->write($i,4," ",$format_itogo);


            $nds_summ=$summ;
            $nds=0;

            if(!$PARTNER[nds]){
              //$nds=round(($summ / 118) * (18),2);

              //$nds_summ=round($summ-$nds,2);
            }


              $now_proc=0;
              foreach(preg_split("/(\n\r|\r\n|\n|\r)/",$PARTNER[proc_case]) as $l){
                  if(preg_match("/^\[(.*?)\-(.*?)\:(.*?)\]$/",$l,$m)){
                      $from=$m[1];
                      $to=$m[2];
                      $proc=$m[3];

                      if($from*1 <= $summ*1 && $to*1 > $summ*1 && !$now_proc){
                          $now_proc=$proc;
                          //echo "$from --- $to ::: $proc<br>";
                      }

                  }
              }

              if($now_proc){ $PARTNER[proc]=$now_proc; }

            if(1){
            $proc=round(($nds_summ / 100) * $PARTNER[proc],2);

          #$i++;
          # itogo
          #$sheet->write($i,0,"НДС",$format_itogo_1);
          #$sheet->write($i,1," ",$format_itogo_1);
          #$sheet->write($i,2,"$nds руб.",$format_itogo_1);
          #$sheet->write($i,3,"-18%",$format_itogo_1);
          #$sheet->write($i,4," ",$format_itogo_1);

          $i++;
          # itogo
          $sheet->write($i,0,"Вознаграждение",$format_itogo_1);
          $sheet->write($i,1," ",$format_itogo_1);
          $sheet->write($i,2,"$proc руб.",$format_itogo_1);
          $sheet->write($i,3,"$PARTNER[proc]%",$format_itogo_1);
          $sheet->write($i,4," ",$format_itogo_1);


              /*$list.="<tr bgcolor='#EFEFEF'>
              <td>НДС
              <td>
              <td align=right>$nds руб.
              <td><b>-18%</b>
              <td>
              </tr>";
            */
            $list.="<tr bgcolor='#EFEFEF'>
              <td>Вознаграждение
              <td>
              <td align=right>$proc руб.
              <td><b>$now_proc%</b>
              <td>
              </tr>";
            }


          $xls->close();
            exit();
      }

       }

  }
  else if ($_URI[1]=='users') {

    $mnu='';

    $res = mquery("SELECT * FROM ip_group WHERE id_diler='^N'", $PARTNER[id]);
    $n=0;
    while($row = $res->fetch_array()) {
      $n++;
                  //if(!$_REQUEST[group]){
                 //     $_REQUEST[group]=$row[id];
                 // }
      $mnu.="<input type='radio' id='grps$n' name='group' value='$row[id]' ".($row[id] == $_REQUEST[group]?'checked':'')."/ onchange='this.form.submit();'><label for='grps$n'>$row[name]</label>";
    }

	  $mnu="<input type='radio' id='grps0' name='group' value='0' ".(!$_REQUEST[group]?"checked":"")." onchange='this.form.submit();' /><label for='grps0'>Все</label>$mnu";

    $_PAGE[data].="
    
    <style>
    .tab_norm{
      padding: 4px;
    }
    .tab_sel{
      padding: 4px;
      background-color: #DEDEDE;
    }
    </style>
    
    <script>
    $(function(){
      $('.dtpicker').datepicker($.datepicker.regional['ru']);
      $('#grps').buttonset();
    });
    </script>
    
    <br>"."<div style='background: #EFEFEF; padding:10px; border-top: 1px solid #fff; position:relative;z-index:2;' class='rad10'>	
    <form method=post>Группа: 
      
    <span id='grps'>
    $mnu
    </span>
  
    
    </form>
    
    </div>"."";

    //if($_REQUEST[group]){

    $Group=$_REQUEST[group];

		$r=mquery("SELECT u.*, g.name as gname
    			FROM `user` u  
    			JOIN `ip_group` g ON g.id = u.id_grp
    			WHERE ".($Group?"u.id_grp='^N'":"^N = 0 and u.id_grp IN (SELECT id FROM ip_group WHERE id_diler = $PARTNER[id])")."

    			ORDER BY u.`id_grp`
    	",$Group);
    	//echo $r->q;


    	global $_DFN;

    	$list='';$summ=0;
    	while($row = $r->fetch_array()) {

    		$list.="<tr>
    		<td>$row[gname]
    		<td>$row[id]
    		<td><b>$row[login]</b> $row[full_name]
    		<td align=right>$row[balanse]
    		<td align=center><a href='/$LNK/chpass_user/?uid=$row[id]' class='fb_iframe' w='350' h='220'>сменить пароль</a>
    		</tr>";
    	}

        ##
        if(!$list){
            $list="<tr><td colspan='5'>Не найдено ни одного абонента!</tr>";
        }
        ##

    	$_PAGE[data].="<br><table class='itbl' border='0' cellpadding='8' cellspacing='1' width='100%'>
    	<tr bgcolor='#666666'>
    		<td style='color:white' width='100px'>Группа
    		<td style='color:white' width='50px'>ID
    		<td style='color:white'>Имя
    		<td style='color:white' width='60px'>Баланс
    		<td align=right style='color:white' width='130px'>Операции
    	</tr>
    		
    	$list
    	</table>";
	 //  }
	}
  else if ($_URI[1]== 'cards') {
    if ($_REQUEST[ac] && $_REQUEST[grp]){
      include_once "cards.php";

    }else{
      $lines='';
      $res=mquery("
      SELECT cg.*, cg.id as id_grp,
      (SELECT COUNT(*) FROM card c WHERE (c.id_grp=cg.id))  as cnt_all,
      (SELECT COUNT(*) FROM card c WHERE (c.id_grp=cg.id) AND (c.used=1))  as cnt_used
      FROM card_grp cg WHERE (cg.id_diler='^N')", $PARTNER[id]);

      //$last_cnt=0; $last_cnt_info=array();
      while($row=$res->fetch_assoc()){
        $date = str_date_time($row[date]);
        $row[nominal] *=1;
        /*$last_cnt++;
        $last_cnt_info[cnt_used]+=$row[cnt_used];
        $last_cnt_info[cnt_all]+=$row[cnt_all];*/
        $lines .= "<tr class='hvr_tr stat_row sr_$row[id_dil]'>
                   <td> $date </td>
                   <td> $row[nominal] руб. </td>
                   <td> $row[cnt_used] из $row[cnt_all] шт. активировано <br>
                        <a href='&ac=all&grp=$row[id_grp]' class='mini-link'>все</a> 
                        <a href='&ac=used&grp=$row[id_grp]' class='mini-link'>активированные</a>
                        <a href='&ac=no_used&grp=$row[id_grp]' class='mini-link'>неактивированные </a>
                   </td>
              </tr>";
      }

      $_PAGE[data].="<br><br><table  class='itbl' border='0' cellpadding='8' cellspacing='1' width='100%'>
          <tr bgcolor='#666666'>
            <td style='color:white' width='150px'>Дата создания
            <td style='color:white' width='100px'>Номинал
            <td style='color:white'>Количество и активация
          </tr>
  
          $lines
          </table>";
    }
  }
}
else{
    
    $err='';
    
    if($_POST[go]){

            $res = mquery("SELECT * FROM diler WHERE `login`='^S' and `pass`='^S'",
                $_POST[login],$_POST[pass]);
            $row = $res->fetch_array();
            
            if($row[id] && $_POST[login] && $_POST[pass]){
                
                $_SESSION[partner_iden]=$row[id];
                $_SESSION[partner_md5]=md5($row[login]);
                
                Redirect("/$LNK/");
                exit();
            }
            else{
                $err='Неправильный логин или пароль!';
            }
    } 
        
    $err=$err?"<br><br><div><b>Ошибка<b><br><font style='color:#EE2222'> $err</font></div><br>":"";
        
    $_PAGE[data]=$err.'
			<form method="post" name="upid_auth">
                        <input type="hidden" name="go" value="1">
				<center>
				<table cellpadding="5" cellspacing="0" width="200px">
				<tr>
				<td> Логин:<br>
					<input style="margin-top: 4px; width: 210px;" type="text" name="login" value="'.$_POST[login].'" class="login" onkeypress="if(event.keyCode==13){this.form.pass.focus();return false;}">
				</td>
				</tr>
				<tr>
				<td><br>Пароль:<br>
					<input style="margin-top: 4px; width: 210px;" type="password" name="pass" class="login" onkeypress="if(event.keyCode==13){this.form.submit();return false;}">
				</td>
				</tr>
				<tr>	<td> <br><br><input type=submit class=gbut value="Войти в кабинет партнера"> </td> </tr>
				</table>
				</center>
				
	</form>';
}

}
?>