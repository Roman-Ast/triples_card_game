const noneNotWinnersAgreedToCook = (msgObject) => {
    console.dir(msgObject);

    $('.playerBetField').each(function () {
        msgObject.playersCookingOrNot.forEach(item => {
            if (item.name === $(this).attr('ownerName') && item.cooking) {
                $(this).text('варю');
            } else if (item.name === $(this).attr('ownerName') && item.cooking === false) {
                $(this).text('пасс');
            }
        });
    })
    
    if (msgObject.winners.includes($('#playerName').text())) {
        $('#cooking').show();
        $('#shareCashBoxAfterOpening').show();
    } else if (!msgObject.winners.includes($('#playerName').text())) {
        $('#cooking').hide();
        $('#notCooking').hide();
    }
 };
 
 export default noneNotWinnersAgreedToCook;