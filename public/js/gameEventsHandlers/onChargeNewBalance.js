const chargeNewBalance = (msgObject) => {
    console.log(msgObject);
    msgObject.allUsers.forEach(item => {
        if ($('#playerName').text() === item.name) {
            $('#playerBalance').text(item.balance);
        }
    });
};

export default chargeNewBalance;