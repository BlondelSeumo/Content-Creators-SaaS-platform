(function() {
    // Initialize the service worker
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/serviceworker.js', {
            scope: '.'
        }).then(function (registration) {
            // Registration was successful
            // eslint-disable-next-line no-console
            console.log('Laravel PWA: ServiceWorker registration successful with scope: ', registration.scope);
        }, function (err) {
            // registration failed :(
            // eslint-disable-next-line no-console
            console.log('Laravel PWA: ServiceWorker registration failed: ', err);
        });
    }
});
