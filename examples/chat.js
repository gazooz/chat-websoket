const connection = new WebSocket('ws://gazooz-chat.herokuapp.com/chat');

connection.onopen = () => {
    console.log('connected');
};

connection.onclose = () => {
    console.error('disconnected');
};

connection.onerror = error => {
    console.error('failed to connect', error);
};

connection.onmessage = event => {
    console.log('received', event.data);
    let data = JSON.parse(event.data);
    let messageBox = document.createElement('div');

    let name = document.createElement('span');
    name.innerText = 'Client ' + data.clientId + ': ';

    let text = document.createElement('span');
    text.innerText  = data.message;

    messageBox.append(name, text);

    document.querySelector('#chat').append(messageBox);
};

document.querySelector('form').addEventListener('submit', event => {
    event.preventDefault();
    let message = document.querySelector('#message').value;
    sendMessage(message);
    document.querySelector('#message').value = '';
});

function sendMessage(message) {
    connection.send(JSON.stringify({
        action: 'actionMessage',
        message: message
    }));
}
