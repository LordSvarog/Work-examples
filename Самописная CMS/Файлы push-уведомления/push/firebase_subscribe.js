document.addEventListener("DOMContentLoaded", function(){
  // код после формирования DOM
  firebase.initializeApp({
    messagingSenderId: ''
  });

  // проверка, что браузер поддерживает уведомления
  if ('Notification' in window) {
    var messaging = firebase.messaging();
    subscribe();

    //Позволяет отправить со страницы уведомление и получить его на том же устройстве
    messaging.onMessage(function(payload) {
      console.log('Message received. ', payload);
      new Notification(payload.notification.title, payload.notification);
    });

    //Работа с мобильными устройствами
    messaging.onMessage(function(payload) {
      // регистрируем ServiceWorker
      navigator.serviceWorker.register('js/push/messaging-sw.js');

      // запрашиваем права на показ уведомлений если еще не получили их
      Notification.requestPermission(function(result) {
        if (result === 'granted') {
          navigator.serviceWorker.ready.then(function(registration) {
            // теперь мы можем показывать уведомление
            payload.notification.data = payload.notification; // параметры уведомления
            registration.showNotification(payload.notification.title, payload.notification);
          }).catch(function(error) {
            console.log('ServiceWorker registration failed', error);
          });
        }
      });
    });
  }

  function subscribe() {
    // запрашиваем разрешение на получение уведомлений
    messaging.requestPermission()
      .then(function () {
        // получаем ID устройства
        messaging.getToken()
          .then(function (currentToken) {
            console.log(currentToken);

            if (currentToken) {
              sendTokenToServer(currentToken);
            } else {
              console.warn('Не удалось получить токен.');
              setTokenSentToServer(false);
            }
          })
          .catch(function (err) {
            console.warn('При получении токена произошла ошибка.', err);
            setTokenSentToServer(false);
          });
      })
      .catch(function (err) {
        console.warn('Не удалось получить разрешение на показ уведомлений.', err);
      });
  }

  // отправка ID на сервер
  function sendTokenToServer(currentToken) {
    if (!isTokenSentToServer(currentToken)) {
      console.log('Отправка токена на сервер...');

      $.post('/runtime/push/', {
        token: currentToken
      });

      setTokenSentToServer(currentToken);
    } else {
      console.log('Токен уже отправлен на сервер.');
    }
  }

  // используем localStorage для отметки того,
  // что пользователь уже подписался на уведомления
  function isTokenSentToServer(currentToken) {
    return window.localStorage.getItem('sentFirebaseMessagingToken') == currentToken;
  }

  function setTokenSentToServer(currentToken) {
    window.localStorage.setItem(
      'sentFirebaseMessagingToken',
      currentToken ? currentToken : ''
    );
  }
});