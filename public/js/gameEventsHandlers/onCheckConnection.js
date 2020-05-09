const connectedPlayers = [];

const onCheckConnection = (msgObject) => {
    console.dir(msgObject);
    msgObject.connectedPlayers.forEach(playerName => {
        if (playerName && !connectedPlayers.includes(playerName)) {
            connectedPlayers.push(playerName);
        }
    });

    connectedPlayers.forEach(playerName => {
        const connectedPlayer = document.createElement('div');
        connectedPlayer.classList.add('connectedPlayer');
        
        if (playerName === $('#playerName').text()) {
            $(connectedPlayer).text(`Вы подключились...`);
        } else {
            $(connectedPlayer).text(`Игрок ${playerName} подключился...`);
        }
        
        $('#connectedPlayers').append(connectedPlayer);
    });
    
    if (connectedPlayers.length < 3) {
        $('#startPlay').attr('disabled', true);
    } else {
        $('#startPlay').removeAttr('disabled');
    }
};

export default onCheckConnection;