const onConnectionError = (msgObject, checkingOtherPlayersConnection) => {
    clearInterval(checkingOtherPlayersConnection);
    $('#connectedPlayers').empty();
    //очищаем modalBody
    $('#modalBody').empty();

    $('#modalBody').append(`<div>${msgObject.msg}</div>`);

    $('#modal').show();
    $('#startPlay').hide();
    $('#connectToGame').show();
};

export default onConnectionError;