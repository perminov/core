var connection = new WebSocket('ws://' + Indi.ini.ws.socket.split('//')[1]);

connection.onopen = function () {
    console.log('opened');
    connection.send(JSON.stringify({type: 'open', uid: Indi.user.uid}));
}

connection.onerror = function(event) {
    console.log('onerror', arguments);
}

connection.onclose = function(event) {
    console.log('onclose', arguments)
}

// Handler for incoming websockets-message
connection.onmessage = function(message) {

    // If message.data is not a JSON-encoded string - return
    if (!message.data.match(/^[\[\{]/)) return console.log('ws:message.data is not a JSON:', message.data);

    // Parse message
    var data = JSON.parse(message.data), store;

    // If message type is 'notice'
    if (data.type == 'notice') {

        // Show notice message
        if (data.msg) Ext.Msg.side(data.msg);

        // If notice mode is 'menu-qty'
        if (data.mode == 'menu-qty') {

            // Get certain menu-qty dom element. If not found - return
            var qtyEl = Ext.get('menu-qty-' + data.noticeId), qtyVal; if (!qtyEl) return;

            // Get current qty
            qtyVal = parseInt(qtyEl.getHTML());

            // Increase/decrease qty by data.diff
            qtyVal += data.diff;

            // Update dom node
            qtyEl.setHTML(qtyVal);

            // Hide/show qtyEl if qtyVal is zero/non-zero
            qtyEl.setStyle('display', qtyVal ? '' : 'none');
        }
    }

    // If message type is 'reload'
    if (data.type == 'reload' && data.model) {
        Ext.StoreMgr.filter('table', data.model).each(function(store){
            store.reload();
        });
    }

    if (data.type == 'gps') {

        // Check if geolocation is supported by the browser
        if (!navigator.geolocation) return Ext.Msg.alert('Ошибка', 'Геолокация не поддерживается вашим браузером');

        // Once coords are got - log them
        var onGpsSuccess = function(position) {
            Indi.load('/../cron/gps/', {
                into: true,
                params: {
                    eventId: data.eventId,
                    coords: [position.coords.latitude.toFixed(6), position.coords.longitude.toFixed(6)].join(', ')
                }
            });
        }

        // Geolocation error handler
        var onGpsFailure = function(error) {
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    Ext.Msg.alert('Ошибка', 'User denied the request for Geolocation.');
                    break;
                case error.POSITION_UNAVAILABLE:
                    Ext.Msg.alert('Ошибка', 'Location information is unavailable.');
                    break;
                case error.TIMEOUT:
                    Ext.Msg.alert('Ошибка', 'The request to get user location timed out."');
                    break;
                case error.UNKNOWN_ERROR:
                    Ext.Msg.alert('Ошибка', 'An unknown error occurred.');
                    break;
            }
        }

        // Try to get current position
        navigator.geolocation.getCurrentPosition(onGpsSuccess, onGpsFailure);
    }
}
