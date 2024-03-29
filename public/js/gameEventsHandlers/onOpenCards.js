const onOpenCards = (msgObject, checkingOtherPlayersConnection, playersArrangement) => {

    const isAdmin = $('#isAdmin').text();
    
    if (isAdmin == 1) {
        msgObject.allPlayers.forEach(item => {
            $('.playerName').each(function () {
                if ($(this).text() === item.name) {
                    console.log(item.name);
                    $(this).next().text(item.balance);
                }
            });
        });
    }

    $('#cashBoxSum').text(msgObject.totalCashBox);
    $('#modalBody').empty();
    $('#modal').hide();
    console.dir(msgObject);
    $('#openCards').hide();

    if (msgObject.winnerAfterOpening.length === 1) {

        if ($('#playerName').text() === msgObject.winnerAfterOpening[0]) {
            $("#takeCashBoxAfterOpening").show({"display": "block"});
        }
        
    } else if (msgObject.allPlayers.length === msgObject.winnerAfterOpening.length) {
        $("#shareCashBoxAfterOpening").show();
        $('#cooking').show();
    } else if (msgObject.winnerAfterOpening.length > 1) {
        if (msgObject.winnerAfterOpening.includes($('#playerName').text())) {
            $('#bet').hide();
        } else {
            if ($('#playerBalance').text() > $('#cashBoxSum').text() / 2) {
                $('#cooking').show();
                $('#notCooking').show();
            } else {
                $('#notCooking').show();
            }
            
        }
    }

    const openCardsForAll = (allCards, container) => {
        allCards.forEach(item => {
            const cardsOfPlayer = document.createElement('div');
            cardsOfPlayer.classList.add('cardsOfPlayer');
    
            const playerName = document.createElement('div');
            $(playerName).text(item.name);
    
            const cardsContainer = document.createElement('div');
            cardsContainer.classList.add('cardsContainer');
    
            item.cards.forEach(face => {
                const cardContainer = document.createElement('div');
                cardContainer.style.width = '50px';
                cardContainer.style.height = '60px';
                cardContainer.style.backgroundImage = `url(${face})`;
                cardContainer.style.backgroundSize = 'contain';
                cardContainer.style.backgroundRepeat = 'no-repeat';
                cardsContainer.appendChild(cardContainer);
            });
            msgObject.winnerAfterOpening.forEach(winner => {
                if (winner === item.name) {
                    cardsOfPlayer.style.border = '1px dashed red';
                }
            });
            cardsOfPlayer.style.marginBottom = '5px';
            cardsOfPlayer.appendChild(playerName);
            cardsOfPlayer.appendChild(cardsContainer);
    
            container.append(cardsOfPlayer);
        });
    };
    $('#notCooking').removeAttr('disabled');
    $('#cooking').removeAttr('disabled');
    openCardsForAll(msgObject.allCards, $('#innerFrame'));
    $('#frameForAllPlayersCards').show();
};

export default onOpenCards;