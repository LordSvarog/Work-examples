<?php
include_once "acp/lib/lib.php";

$filter = '';
switch ( $_REQUEST[ ac ] ) {
  case 'used':
    $filter = ' AND (c.used=1)';
    $t_filter = ' -> Активированные';
    break;
  case 'no_used':
    $filter = ' AND (c.used=0)';
    $t_filter = ' -> Неактивированные';
    break;
  default:
    $filter = '';
    $t_filter = ' -> Все';
}

$lines = '';
$t_res = mquery( "
   SELECT cg.*,d.fullname FROM card_grp cg 
   LEFT JOIN diler d ON (cg.id_diler = d.id)   
   WHERE cg.id='^N'", $_REQUEST[ grp ] );

if ( $t_row = $t_res->fetch_assoc() ) {
  $date = str_date_time( $t_row[ date ], '', '#FFFFFF' );
  $title = "$t_filter от $date номинал $t_row[nominal] руб.";
}

$res = mquery( "
   SELECT c.*,u.full_name,u.id as uid, c.id as id_card, ipg.name as gname FROM card c
   LEFT JOIN card_grp cg ON (c.id_grp = cg.id)
   LEFT JOIN diler d ON (cg.id_diler = d.id)
   LEFT JOIN user u ON (c.id_user = u.id)
   LEFT JOIN ip_group ipg ON ipg.id = u.id_grp
   WHERE cg.id='^N' $filter  ORDER BY act_date", $_REQUEST[ grp ] );

$card_cnt = 0;
$used_cnt = 0; $n=0;
while ( $row = $res->fetch_assoc() ) {
  $card_cnt++;
  $date = str_date_time( $row[ act_date ] );
  if ( $row[ used ] ) {
    $used_cnt++;
    $used = 'Да';
  } else $used = '&nbsp';
  $row[ full_name ] = $row[ full_name ] ? $row[ full_name ] : '&nbsp';

  $number=sel_serch_txt($row[number],$_REQUEST[text]);
  $n++;
  $lines .= "<tr id='itm$row[number]'  align='center'>
      <td> $n </td>  
      <td> $row[id] </td>          
      <td> $number </td>
      <td> $used </td>
      <td> " . ( $row[ uid ] ? "№{$row[uid]} {$row[full_name]}" : "" ) . " </td>
      <td> $row[gname] </td>
      <td> $date </td>
    </tr>";
};

$_PAGE[data].= "   
   <br>
   <table class='itbl' border='0' cellpadding='8' cellspacing='1' width='100%'>
   <tr>
     <td class='titlebg' colspan='8' style='border-left: 0px solid white;'>
       <a href='/partner/cards/' id='back-button' tip='Вернуться назад' style='border-bottom: 0px solid white;'>
         <img src='/images/back.png' style='vertical-align: bottom; padding-top: 5px'>
       </a>
       <div style='display: inline;'>Карты оплаты $title</div>
     </td>
   </tr>
   <tr align='center' bgcolor='#666666'>
     <td style='color:white;'><nobr>№ п/п</nobr></td>
     <td style='color:white'>ID карты</td>
     <td style='color:white'>Номер</td>
     <td style='color:white'>Использован</td>
     <td style='color:white'>Пользователь</td>
     <td style='color:white'>Группа</td>
     <td style='color:white'>Время активации</td>
   </tr>
   $lines
   <tr align='center'>
     <th>&nbsp</th>
     <th>&nbsp</th>
     <th>$card_cnt шт.</th>
     <th>$used_cnt шт.</th>
     <th>&nbsp</th>
     <th>&nbsp</th>
     <th>&nbsp</th>
   </tr>
   </table>
";