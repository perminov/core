var connection = new SockJS('http://prazdnik.life:8888');
connection.onopen = function () {
    connection.send(JSON.stringify({type: 'open', uid: Indi.user.uid}));
}

connection.onerror = function(event) {
    console.log('onerror', arguments);
}

connection.onmessage = function(message) {
    var data = JSON.parse(message.data), store;
    if (data.type == 'reload') {
        if (store = Ext.getStore('i-section-list-action-index-store')) store.reload();
    }
}

connection.onclose = function(event) {
    console.log('onclose', arguments)
}

