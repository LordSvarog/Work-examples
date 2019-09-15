$(function(){
	$(window).resize();
	window.scrollBy(0, 1);

	//Отслеживание отправки лайв-сообщения
	$('#send_live_btn').click(function(){
		sendLiveMessage();
		return false;
	});
});

//Работа с лайв-сообщениями

//Глобальная переменная для управления работой кнопки
let postMsg = postNewMessage;

//Сохраняет новое сообщение
function postNewMessage(msg, data) {
	let ref= $('#live_list').prepend("<div class='live_item loading'>" + msg + "</div>");

	$.post("/post/live_save/", data, function (res) {
		if (res == 1){
			$(ref).removeClass('loading');
		}
	});
}

//Сохраняет отредактированное сообщение
function editMessage(msg, data, id) {
	$.post("/post/live_edit/" + id + "/", data, function (res) {
		if (res == 1){
			$('#live_list').prepend("<div class='loaded'>Изменено успешно!</div>");
		}
	});
	$('#send_live_btn').val('Добавить сообщение');
	postMsg = postNewMessage;
}

//Получаем объект MediumEditor для доступа к его методам
function getEditor(){
	return MediumEditor.getEditorFromElement($('#data-medium-editor')[0]);
}

//Отправка лайв-сообщений
function sendLiveMessage(){
	let msg= $('#data-medium-editor').html();

	$('#data').html(msg);
	postMsg(msg, $('#post-form').serialize());
	getEditor().resetContent();
}

//Показ лайв-сообщений из БД и функционал запуска скриптов редактирования/удаления
function showLiveMessages() {
	$.get("/post/live_show/" + $('#id').val() + "/", function (res) {
		if (!res){
			return;
		}
		$('#live_list').html(res);

		const editor = getEditor();

		//Ловим нажатие на кнопку редактирования
		$('.btn_edit').click(function(event){
			const id = $(this).parent('.buttons').data('id');
			const msg = $('#msg_' + id).html();

			editor.setContent(msg);
			$('#send_live_btn').val('Сохранить изменение');
			$('html, body').animate({
				scrollTop: $("#post-form").offset().top
			}, 250);

			postMsg = function(msg, data){
				editMessage(msg, data, id);
			};
		});

		//Ловим нажатие на кнопку удаления
		$('.btn_delete').click(function(event){
			const id = $(this).parent('.buttons').data('id');

			$.get("/post/live_delete/" + id + "/", function (res) {
				if (res == 1){
					$('#' + id).html('Готово!');
				}
			//console.log(id);
			});
		});
	});
}
