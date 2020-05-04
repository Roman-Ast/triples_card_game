const onNextRound = () => {
    $('#collate').hide();
    $('#myCards').empty();
    $('#betSum').empty();
    $('.playerBetField').empty();
    $('#cashBoxSum').empty();
    $('#modalBody').empty();
    $('#otherPlayers').empty();
    $('#modalBody').append(`<div>следующий раунд</div>`);
    $("#radiness").css({"display":"block"});
    $("#takeCashBox").css({"display":"none"});
    $("#takeCashBoxAfterOpening").css({"display":"none"});
    $("#shareCashBoxAfterOpening").css({"display":"none"});
    $("button").each(function () {
        $(this).removeAttr('disabled'); 
    });
};

export default onNextRound;