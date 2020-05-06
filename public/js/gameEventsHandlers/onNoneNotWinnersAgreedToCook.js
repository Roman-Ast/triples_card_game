const noneNotWinnersAgreedToCook = (msgObject) => {
    console.dir(msgObject);

    if (msgObject.winners.includes($('#playerName').text())) {
        $('#cooking').show();
        $('#shareCashBoxAfterOpening').show();
        
    } else {
        $('button').each(function () {
            $(this).hide();
        });
    }
 };
 
 export default noneNotWinnersAgreedToCook;