<!DOCTYPE HTML>
<html>
<head>
	<title><?=$_PAGE[title]?$_PAGE['title']:$_PAGE[ptitle]?> | Официальный сайт отеля</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="title" content="<?=$_PAGE[title]?$_PAGE['title']:$_PAGE[ptitle]?> | Официальный сайт отеля" />
	<meta name="keywords" content="<?=$_PAGE['keywords']?>">
	<meta name="description" content="<?=$_PAGE['description']?>">

	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta name="resource-type" content="document">
	<meta name="format-detection" content = "telephone=no" >
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">

	<meta name="theme-color" content="#55453a">
	<meta name="apple-mobile-web-app-title" content="<?=$_PRJ[full_title]?>">
	<meta name="application-name" content="<?=$_PRJ[full_title]?>">

	<link REL="SHORTCUT ICON" HREF="/images/favicon.png">

	<link rel="stylesheet" href="/min/app.css?<?=filemtime('./min/app.css')?>">

	<LINK rel="STYLESHEET" href="/images/style.small.css?<?=filemtime('./images/style.small.css')?>"  type="text/css" media="all and (min-device-width: 0px) and (max-device-width:1120px)" >
	<LINK rel="STYLESHEET" href="/images/style.med.css?<?=filemtime('./images/style.med.css')?>"  type="text/css" media="all and (min-device-width: 400px) and (max-device-width: 1120px)" >
	<LINK rel="STYLESHEET" href="/images/style.pad.css?<?=filemtime('./images/style.padd.css')?>"  type="text/css" media="all and (min-device-width: 700px) and (max-device-width: 1120px)" >

	<script src="/js/jquery.js"></script>



	<link href="/js/jquery-ui-1.11.1.custom/jquery-ui.css" rel="stylesheet" type="text/css" />

	<script type="text/javascript" src="/js/jquery-ui-1.11.1.custom/jquery-ui.js"></script>
	<script type="text/javascript" src="/js/jquery-ui-timepicker-addon.js"></script>

	<script src="/js/likely/likely.js"></script>
<link rel="stylesheet" href="/js/likely/likely.css">

	<script type="text/javascript">

		var base_uri = '<?=$_PAGE[base_uri]?>';
		var is_algn = true;

		$.datepicker.setDefaults({
			dateFormat: 'dd.mm.yy'
		});


			$.timepicker.regional['ru'] = {
			timeOnlyTitle: 'Выберите время',
			timeText: 'Время',
			hourText: 'Часы',
			minuteText: 'Минуты',
			secondText: 'Секунды',
			millisecText: 'Миллисекунды',
			timezoneText: 'Часовой пояс',
			currentText: 'Сейчас',
			closeText: 'Закрыть',
			timeFormat: 'HH:mm',
			amNames: ['AM', 'A'],
			pmNames: ['PM', 'P'],
			isRTL: false
		};
		$.timepicker.setDefaults($.timepicker.regional['ru']);
	</script>

	<?
		if ($_PAGE[canonical]) echo $_PAGE[canonical]; else {
			$uri=$_SERVER['REQUEST_URI'];
			$uri=preg_replace('/(\?.*?)$/','',$uri);		
			?>
		<link rel="canonical" href="//<?=$_PRJ[domain]?><?=$uri?>" />

			<?
		}
	?>

	<?=$var->show('counters_head')?>

	<script src="https://www.google.com/recaptcha/api.js?render=6LeQz5IUAAAAAO9B81lpelqVa9tBoyI8mNniezIi"></script>
	
	<script src="/js/jquery.maskedinput.min.js"></script>
</head>
<body class="page_<?=$_URI[0]?>">

<?=$var->show('counters_top')?>

<div id="app">

	<div class="header-ph">
		<div id="header">
			<div class="show_mini mob_buts">
				<img src="/images/m-menu.png" onclick="$('body').toggleClass('menu_visible');" class="buter"><img src="/images/m-close.png" onclick="$('body').toggleClass('menu_visible');" class="buter_close"><a href="tel:88002004912"><img src="/images/m-phone.png" class="phone"></a><a href="/booking/"><img src="/images/m-booking.png" class="booking"></a><a href="//en.hotel-forum.ru/"><img src="/images/m-en.png" class="lang"></a>
			</div>
			<div class='logo-ph'>

				<a href="/"><img src="/images/logo.jpg" class="logo" border="0" alt="Конгресс отель ФОРУМ, город Рязань" title="Конгресс отель ФОРУМ, город Рязань"></a>

				<div class="phone_white" style="display:none;">
					<img src="/images/icon-phonew.png" border="0">8 800 2004912
				</div>
			</div><div class='data'>
				<?=$_PAGE[menu]?>
			</div>
		</div>
	</div>

	<div id="header-under">
		<div class='data'>
			<div class="rating-icons">
				<a href="//en.hotel-forum.ru/"><img src="/images/en.png" class="lang"></a>
				<span tip="Конгресс отель ФОРУМ на booking.com"><img src="/images/icon-booking.png" border="0" ><font class="rating">9.1</font></span>
				<span tip="Конгресс отель ФОРУМ на Trip Advisor"><img src="/images/icon-trip.png" border="0"><font class="rating">4.0</font></span>
			</div><div class="address">
				г. Рязань, пр. Яблочкова, 5е
			</div><div class="phone">
				<img src="/images/icon-phone.png" border="0">8 800 2004912
			</div><div class="consult">
				<a href="#" class="redb callback"><img src="/images/icon-help.png" border="0">ОНЛАЙН КОНСУЛЬТАНТ</a>

				<span style="    position: absolute;
    z-index: 500;
    margin-top: -20px;
    width: 230px;
    text-align: center;
    text-transform: uppercase;
    padding: 0px;" class="greenb"><a href="http://hotel-forum.ru/about/news/41/" style="color:white; text-decoration: none;">Отель прошел реновацию</a></span>

				<span style="    position: absolute;
    z-index: 500;
    margin-top: 20px;
    width: 230px;
    text-align: center;
    text-transform: uppercase;
    padding: 0px;">

<!-- start reputation form 2.0 -->
<div id="tl-reputation-widget"></div>
<script type="text/javascript">
  (function (w) {
	  var q = [
		  ['setContext', 'TL-INT-hotel-forum', 'ru'],
		  ['embed', 'reputation-widget', {container: 'tl-reputation-widget'}]
	  ];
	  var t = w.travelline = (w.travelline || {}), ti = t.integration = (t.integration || {});
	  ti.__cq = ti.__cq ? ti.__cq.concat(q) : q;
	  if (!ti.__loader) {
		  ti.__loader = true;
		  var d = w.document, p = d.location.protocol, s = d.createElement('script');
		  s.type = 'text/javascript';
		  s.async = true;
		  s.src = (p == 'https:' ? p : 'http:') + '//ibe.tlintegration.com/integration/loader.js';
		  (d.getElementsByTagName('head')[0] || d.getElementsByTagName('body')[0]).appendChild(s);
	  }
  })(window);
</script>
<!-- end reputation form 2.0 -->

				</span>
			</div>
		</div>
	</div>

	<?  if ($_PAGE[pagemode] == 'room') { ?>
		<? include_once("templates$GLOBALS[lp]/_room.php"); ?>
	<?  }else if ($_PAGE[pagemode] == 'service') { ?>
		<? include_once("templates$GLOBALS[lp]/_service.php"); ?>
	<? }else if ($_PAGE[into]) { ?>
		<? include_once("templates$GLOBALS[lp]/_page.php"); ?>

	<? } else { ?>
		<? include_once("templates$GLOBALS[lp]/_index.php"); ?>
	<? } ?>



	<div id="footer">

		<div class="pre_data">
			<div class="subscribe">
				<h4>Хотите быть в курсе новостей, акций и специальных предложений?</h4> Подпишись на рассылку
				<form class="holder green-form" id="subscribe-form">
					<input type="hidden" name="go" value="1">
					<div class="form-col"><input type="text" class="login" name="fullname" placeholder="Ваше имя"></div><div class="form-col"><input type="text" class="login" name="from" placeholder="Электронная почта"></div><br><br><div class="form-col"><button class="bbtn">Подписаться</button></div>
				</form>
			</div>
		</div>

		<div class="data">



			<div class="logo-info">

				<div><a href="/"><img src="/images/logolittle.jpg" border="0" alt="Конгресс отель ФОРУМ, город Рязань" title="Конгресс отель ФОРУМ, город Рязань"></a></div>

				<?=$var->show('copyright')?>


			</div><div class="payments">

				<img src="/images/icon-visa.png" border="0">

			</div><div class="social">

				<div>БУДЕМ ДРУЖИТЬ</div>
				<a href="https://vk.com/hotelforum" class="soc" target="_blank"><img src="/images/icon-v.png" border="0"></a> <a href="https://www.facebook.com/CongressHotelForum" class="soc" target="_blank"><img src="/images/icon-f.png" border="0"></a>  <a href="https://www.instagram.com/hotelforum_rzn/" class="soc" target="_blank"><img src="/images/icon-i.png" border="0"></a>  <a href="https://www.youtube.com/channel/UCtH5Vy-pXIqqDdxLKtTWjAA" class="soc" target="_blank"><img src="/images/icon-y.png" border="0"></a>

			</div>
		</div>
	</div>

</div>

<script type="text/javascript" src="/min/app.js?<?=filemtime('./min/app.js')?>"></script>
<script src="//api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
<script>ymaps.ready(init_maps);</script>

<?=$var->show('counters_bottom')?>

<!-- BEGIN JIVOSITE CODE {literal} -->
<script type='text/javascript'>
(function(){ var widget_id = 'zT4dIlRmsy';var d=document;var w=window;function l(){var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true;s.src = '//code.jivosite.com/script/widget/'+widget_id; var ss = document.getElementsByTagName('script')[0]; ss.parentNode.insertBefore(s, ss);}if(d.readyState=='complete'){l();}else{if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})();
</script>
<!-- {/literal} END JIVOSITE CODE -->

</body>
</html>
