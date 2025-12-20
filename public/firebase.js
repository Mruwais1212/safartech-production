$(function () {

    const firebaseConfig = {
        apiKey: "AIzaSyBBO_07e9mlM11b5W0ypq0sfRJDCM5AU7E",
        authDomain: "kooora.firebaseapp.com",
        projectId: "kooora",
        storageBucket: "kooora.appspot.com",
        messagingSenderId: "993794872911",
        appId: "1:993794872911:web:3df36653b0ab46df2bfc3a",
        measurementId: "G-9LMJCRV19Z"
    };

    // Initialize Firebase
    firebase.initializeApp(firebaseConfig);
    const messaging = firebase.messaging();
    messaging.requestPermission()
        .then(function () {
            messaging.getToken()
                .then(function (currentToken) {
                    if (currentToken) {
                        var baseUrl = window.location.origin;
                        $.ajax({
                            url: baseUrl + '/update-token',
                            method: 'get',
                            data: 'device_token=' + currentToken,
                            processData: false,
                            contentType: false,
                            success: function (response) {
                            }
                        });
                    }
                })
                .catch(function (err) {
                    console.log(err);
                });
        });


    window.addEventListener('load', function () {

// Check that service workers are supported, if so, progressively
// enhance and add push messaging support, otherwise continue without it.
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/firebase-messaging-sw.js')
                .then((registration) => {
                }).catch(err => {
                console.log(err)
            })
        } else {
            console.warn('Service workers aren\'t supported in this browser.');
        }
    });

    if ("Notification" in window) {
        // console.log('notification');
    }

    messaging.onMessage(function (payload) {
        if (Notification.permission === "granted") {
            console.log('granted');
        } else {
            console.log('not granted');
        }
        console.log("Message received. ", payload);
        const title = payload.notification.title;
        const body = payload.notification.body;

        let link = null;

        displayNotification(title, body, link);
    });

});

function displayNotification(title, body, link) {
    var baseUrl = window.location.origin;

    if (Notification.permission !== "granted")
        Notification.requestPermission();
    else {
        var notification = new Notification(title, {
            icon: baseUrl + '/images/logo.png',
            body: body,
            data: {
                link: link
            }
        });
        notification.onclick = (e) => {
            if(e.target.data.link) {
                window.location.href = e.target.data.link;
            }
        };
    }
}
