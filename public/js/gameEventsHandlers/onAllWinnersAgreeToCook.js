import playersArrangement from '../ playersArrangement.js';

const allWinnersAgreeToCook = (msgObject) => {
    console.dir(msgObject);
    $('.playerContainer').detach();
    $('#betSum').empty();
    $('#myCards').empty();
    $('.playerBetField').empty();

    $('#round-status').text('свара');
    
    
    if (!msgObject.winners.includes($('#playerName').text())) {
        $('#cooking').hide();
        $('#notCooking').hide();
        $('#shareCashBoxAfterOpening').hide();
    } else {
        $('#bet').show();
        $('#bet').css({'display': 'flex'});
        $('#makeBet').show();
        $('#save').show();
        $('#shareCashBoxAfterOpening').hide();
        $('#cooking').hide();
        $('#collate').hide();
    }

    if (msgObject.name === msgObject.currentFirstWordPlayer) {
        $("#makeBet").removeAttr("disabled");
        $("#save").removeAttr("disabled");
        $("#collate").removeAttr("disabled");
        $("#betSum").prop("disabled", false);
    } else {
        $("#makeBet").attr("disabled", true);
        $("#save").attr("disabled", true);
        $("#collate").attr("disabled", true);
        $("#betSum").prop("disabled", true);
    }

    for (let i = msgObject.defaultBet; i < 3000; i += msgObject.stepInBets) {
        $('#betSum').append(`<option value="${i}">${i}</option>`);
    }
    
    playersArrangement(
        msgObject.allPlayers,
        $('#room'),
        msgObject.name,
        msgObject.currentDistributor,
        msgObject.currentFirstWordPlayer
    );

    msgObject.cards.forEach(card => {
        
        const cardContainer = document.createElement("div");
        const img = new Image(96.4, 144.6);
        img.src = card.face;
        img.classList.add('cardFace');
        cardContainer.appendChild(img);

        cardContainer.classList.add("card");
        const cardsContainer = document.querySelector("#myCards");
        
        cardsContainer.appendChild(cardContainer);
    });
    
    //если игрок не варит убираем у него рубашку
    $('.playerContainer').each(function () {
        if (!msgObject.cookingPlayers.includes($(this).attr('ownerName'))) {
            $(this).children().first().empty();
        }
    });
    //если игрок не варит ставим ему "пасс"
    $('.playerBetField').each(function () {
        if (!msgObject.cookingPlayers.includes($(this).attr('ownerName'))) {
            $(this).text('пасс');
            $(this).css({'color': 'red'});
            $(this).css({'font-style': 'italic'});
        }
    });
    
};

export default allWinnersAgreeToCook;