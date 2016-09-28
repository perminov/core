var connection = new SockJS('http://karusel3.local:8888');
connection.onopen = function () {
    connection.send(JSON.stringify({type: 'open', uid: Indi.user.uid}));
}

connection.onerror = function(event) {
    console.log('onerror', arguments);
}

connection.onmessage = function(message) {
    var data = JSON.parse(message.data);
    if (data.type == 'reload') {
        Ext.getStore('i-section-list-action-index-store').reload();
    }
}

connection.onclose = function(event) {
    console.log('onclose', arguments)
}

