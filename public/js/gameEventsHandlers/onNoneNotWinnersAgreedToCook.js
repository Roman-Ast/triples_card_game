const noneNotWinnersAgreedToCook = (msgObject) => {
    console.dir(msgObject);

    $('.playerBetField').each(function () {
        if (!msgObject.cookingPlayers.includes($(this).attr('ownerName'))) {
            $(this).text('пасс');
        } else {
            $(this).text('варю');
        }
     });
    $('.playerBetField').each(function () {
        if (!msgObject.winners.includes($(this).attr('ownerName'))) {
            $(this).text('пасс');
            $(this).css({'color': 'red'});
            $(this).css({'font-style': 'italic'});
        }
    });
    if (msgObject.winners.includes($('#playerName').text())) {
        $('#cooking').show();
        $('#shareCashBoxAfterOpening').show();

        
    } else {
        $('#cooking').hide();
        $('#notCooking').hide();
    }
 };
 
 export default noneNotWinnersAgreedToCook;