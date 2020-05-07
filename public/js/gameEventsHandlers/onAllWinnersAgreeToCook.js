import playersArrangement from '../ playersArrangement.js';

const allWinnersAgreeToCook = (msgObject) => {
    console.dir(msgObject);
    $('.playerContainer').detach();
    $('#betSum').empty();
    $('#myCards').empty();
    
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
    }

    if (msgObject.name === msgObject.currentFirstWordPlayer) {
        $("#makeBet").removeAttr("disabled");
        $("#save").removeAttr("disabled");
        $("#collate").removeAttr("disabled");
        $("#betSum").prop("disabled", false);
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
    
    const cookingPlayersNames = msgObject.cookingPlayers.map(item => item.name);
    //если игрок не варит убираем у него рубашку
    $('.playerContainer').each(function () {
        if (!cookingPlayersNames.includes($(this).attr('ownerName'))) {
            $(this).children().first().empty();
        }
    });
    
};

export default allWinnersAgreeToCook;