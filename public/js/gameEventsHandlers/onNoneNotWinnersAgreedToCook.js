const noneNotWinnersAgreedToCook = (msgObject) => {
    console.dir(msgObject);

    if (msgObject.winners.includes($('#playerName').text())) {
        $('#cooking').show();
        $('#shareCashBoxAfterOpening').show();
        
    } else {
        $('#cooking').hide();
        $('#notCooking').hide();
    }
 };
 
 export default noneNotWinnersAgreedToCook;