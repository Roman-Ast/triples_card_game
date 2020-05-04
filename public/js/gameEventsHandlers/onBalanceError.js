const onBalanceError = (msgObject, checkingOtherPlayersConnection) => {
    $('#modalBody').append(`<div>${msgObject.msg}</div>`);
    $('#modal').show();
};

export default onBalanceError;