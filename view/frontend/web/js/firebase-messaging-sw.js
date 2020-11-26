/**
 * listener for push notification
 */
self.addEventListener(
    'push',
    function (event) {
	let a= 1;
	console.log(event.data.json().action)
    if(event.data.json()!=undefined){
    	dataa  = event.data.json().notification
        return self.registration.showNotification(
        dataa.title,
        {
            body: dataa.body,
            icon: dataa.icon,
            vibrate: 1,
            data: dataa.click_action
         } )
        }else{
          
        }
        }
	
);

/**
 * adding event listener for registring customer for push notification
 *
 * @param  {string} event) {               var url url to notify
 * @return object
 */
self.addEventListener(
    'notificationclick',
    function (event) {
        var url = event.notification.data;
        if (url) {
            event.notification.close();
            event.waitUntil(
                clients.matchAll(
                    {
                        type: 'window'
                    }
                ).then(
                    function (windowClients) {
                        if (clients.openWindow) {
                            return clients.openWindow(url);
                        }
                    }
                )
            );
        }
    }
);

/**
 * Logging function to log
 *
 * @param {mixed} $log
 */
function Logging($log)
{
    var canLog = 0;
    if (canLog) {
        console.log($log);
    }
};

function ext(url)
{
    return (url = url.substr(1 + url.lastIndexOf("/")).split('?')[0]).split('#')[0].substr(url.lastIndexOf("."));
}

