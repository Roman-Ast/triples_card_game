const moveTax = (msgObject) => {
    $('#tax').css({'position': 'absolute'});
    $('#tax').css({'color': 'red'});
    let top = 3;
    let op = 1;
    let counter = 100;
    const taxUping = setInterval(() => {
        counter -= 1;
        top += 0.5;
        op -= 0.01;
        $('#tax').css('top', top);
        $('#tax').css('opacity', op);
        
        if (counter <= 0) stopTaxUping();
    }, 15);
    const stopTaxUping = () => {
        msgObject.winner.forEach(name => {
            if (name === $('#playerName').text()) {
                const balance = +$('#playerBalance').text() + Math.floor(msgObject.cashBox / msgObject.winner.length);
                $('#playerBalance').text(balance);
                $('#playerBalance').css({'color': '#FFC107'});
            }
        });
        $('#tax').css('opacity', 0);
        $('#cashBoxSum').text(0);
        clearInterval(taxUping);
        $('#tax').detach();
    };
};

const onNextRound = (msgObject) => {
    console.log(msgObject);
    $('#collate').hide();
    $('#cooking').hide();
    $('#myCards').empty();
    $('#betSum').empty();
    $('.playerBetField').empty();
    //$('#cashBoxSum').empty();
    $('#otherPlayers').empty();
    $("#radiness").css({"display":"block"});
    $("#takeCashBox").css({"display":"none"});
    $("#takeCashBoxAfterOpening").css({"display":"none"});
    $("#shareCashBoxAfterOpening").css({"display":"none"});
    $("#round-status").empty();
    $("button").each(function () {
        $(this).removeAttr('disabled'); 
    });

    $('#cashBoxSum').text(msgObject.cashBox);

    $('#cashBox').append(
        `<div id="tax">-${msgObject.taxSum}</div>`
    );
    
    moveTax(msgObject);
};

export default onNextRound;