const chargeNewBalance = (msgObject) => {
    console.log(msgObject);
    msgObject.allPlayers.forEach(item => {
        if ($('#playerName').text() === item.name) {
            $('#playerBalance').text(item.balance);
        }
    });

    const isAdmin = $('#isAdmin').text();

    if (isAdmin == 1) {
        msgObject.allPlayers.forEach(item => {
            $('.playerName').each(function () {
                if ($(this).text() === item.name) {
                    $(this).next().text(item.balance);
                }
            });
        });
    }
};

export default chargeNewBalance;