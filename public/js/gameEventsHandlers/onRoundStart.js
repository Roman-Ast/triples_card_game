import playersArrangement from '../ playersArrangement.js';

const onRoundStart = (msgObject, checkingOtherPlayersConnection) => {
    clearInterval(checkingOtherPlayersConnection);

    $('#innerFrame').empty();
    $('#betSum').empty();
    //убираем спиннер ожидания
    $('#waitingForStart').hide();

    //очищаем информацию о подключенных игроках
    $('#connectedPlayers').empty();

    //зaполняем селект шагами возможных ставок
    for (let i = msgObject.defaultBet; i < 3000; i += msgObject.stepInBets) {
        $('#betSum').append(`<option value="${i}">${i}</option>`);
    }
    $('#betSum').prop('disabled', false);
    //прячем кнопку "готов" на время раунда
    $("#takeCashBox").css({"display":"none"});
    $("#radiness").css({"display":"none"});
    $("#cooking").css({"display":"none"});
    $("#notCooking").css({"display":"none"});
    $("#bet").css({"display":"flex"});

    console.dir(msgObject);
    
    const cards = msgObject.cards;
    const balance = msgObject.balance;
    const allPlayers = msgObject.allPlayers;
    const currentDistributor = msgObject.currentDistributor;
    const currentFirstWordPlayer = msgObject.currentFirstWordPlayer;

    if (msgObject.name !== currentFirstWordPlayer) {
        $("#makeBet").attr("disabled", true);
        $("#save").attr("disabled", true);
        $("#collate").attr("disabled", true);
        $("#betSum").prop("disabled", true);
    }
    //заполняем поле "баланс" текущего игрока
    const playerBalanceField = $('#playerBalance');
    if (playerBalanceField) $('#playerBalance').text(balance);

    //заполняем комнату игроками
    playersArrangement(
        allPlayers,
        $('#room'),
        msgObject.name,
        currentDistributor,
        currentFirstWordPlayer
    );

    //заполняем поле "касса" суммой всех дефолтных ставок
    ((defaultBets) => {
        const sumOfDefaultBets = defaultBets.reduce((acc, item) => acc + item.defaultBet, 0);
        $("#cashBoxSum").text(sumOfDefaultBets);
    })(msgObject.defaultBets);

    //заполняем поле "карты" текущего игрока
    cards.forEach(card => {
        const cardContainer = document.createElement("div");
        const img = new Image(96.4, 144.6);
        img.src = card.face;
        cardContainer.appendChild(img);

        cardContainer.classList.add("card");
        const cardsContainer = document.querySelector("#myCards");
        
        if (cardsContainer) {
            cardsContainer.appendChild(cardContainer);
        }
        
    });
    
    const isAdmin = $('#isAdmin').text();
    
    if (isAdmin == 1) {
        const allPlayers = msgObject.allPlayers.map(player => player.name);
        
        $('.playerName').each(function () {
            if (allPlayers.includes($(this).text())) {
                msgObject.allPlayers.forEach(item => {
                    if ($(this).text() === item.name) {
                        $(this).next(item.balance);
                    }
                });
            } else {
                $(this).next().next().children().eq(1).attr('disabled', true);
            }
        });
        
    }
}

export default onRoundStart;