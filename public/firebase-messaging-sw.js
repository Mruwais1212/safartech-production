// Give the service worker access to Firebase Messaging.
// Note that you can only use Firebase Messaging here, other Firebase libraries
// are not available in the service worker.

importScripts('https://www.gstatic.com/firebasejs/7.22.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/7.22.0/firebase-messaging.js');
// Initialize the Firebase app in the service worker by passing in the
// messagingSenderId.
const config = {
    apiKey: "AIzaSyBBO_07e9mlM11b5W0ypq0sfRJDCM5AU7E",
    authDomain: "kooora.firebaseapp.com",
    projectId: "kooora",
    storageBucket: "kooora.appspot.com",
    messagingSenderId: "993794872911",
    appId: "1:993794872911:web:3df36653b0ab46df2bfc3a",
    measurementId: "G-9LMJCRV19Z"
};

firebase.initializeApp(config);

let messaging = firebase.messaging();


// // Retrieve an instance of Firebase Messaging so that it can handle background
// // messages.
// const messaging = firebase.messaging();
//
messaging.setBackgroundMessageHandler(function(payload) {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);
    // Customize notification here
    // const notificationTitle = 'Background Message Title';
    // const notificationOptions = {
    //     body: 'Background Message body.',
    //     icon: '/firebase-logo.png'
    // };
    //
    // return self.registration.showNotification(notificationTitle,
    //     notificationOptions);
});

messaging.onBackgroundMessage((payload) => {
    const title = payload.data.title;
    const body = payload.data.message;
    const options = {
        body: payload.data.body,
        data: payload.data,
    };
    // let link = '';
    // const baseNotificationLink = 'https://amgen360care.intermarkfileup.com';
    // if (payload.data.type === 'user') {
    //     link = baseNotificationLink + '/users'
    // }else if (payload.data.type === 'lapTests') {
    //     link = baseNotificationLink + '/journey/lapTests'
    // }else if (payload.data.type === 'product') {
    //     link = baseNotificationLink + '/journey/product'
    // }else if (payload.data.type === 'nurse') {
    //     link = baseNotificationLink + '/journey/nurse'
    // }
    // payload.data.link = link;
    let notificationOptions = {
        body: body,
        data: payload.data
    };
    if (title) {
        self.registration
          .showNotification(title, notificationOptions)
          .then((r) => console.log(r));
      }
});


