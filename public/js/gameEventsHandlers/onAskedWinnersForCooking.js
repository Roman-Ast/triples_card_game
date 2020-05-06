const askedWinnersForCooking = (msgObject) => {
    msgObject.askedWinnersForCooking.forEach(winner => {
        if ($('#playerName').text() === winner) {
            $('#cooking').show();
            $('#notCooking').show();
            $('#shareCashBox').show();
        }
    });
};

export default askedWinnersForCooking;