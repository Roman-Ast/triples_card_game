const onOpenCards = (msgObject) => {
    $('#modalBody').empty();
    $('#modal').hide();
    console.dir(msgObject);
    $('#openCards').hide();

    if (msgObject.winnerAfterOpening.length === 1) {
        $('#modalBody').append(`<div>Победитель: ${msgObject.winnerAfterOpening[0]}</div>`);
        $('#modalBody').append(`<div>Касса: ${msgObject.totalCashBox}</div>`);
        $('#modal').show();
        $("#openCards").css({"display": "none"});

        if ($('#playerName').text() === msgObject.winnerAfterOpening[0]) {
            $("#takeCashBoxAfterOpening").show({"display": "block"});
        }
        
    } else {
        $('#modalBody').append(`Победители:`);
        $('#modal').show();

        msgObject.winnerAfterOpening.forEach(function (winner) {
            if ($('#playerName').text() === winner) {
                $('#shareCashBoxAfterOpening').show();
            }
            $('#modalBody').append(`<div>${winner}</div>`);
        });
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

    openCardsForAll(msgObject.allCards, $('#innerFrame'));
    $('#frameForAllPlayersCards').show();
};

export default onOpenCards;