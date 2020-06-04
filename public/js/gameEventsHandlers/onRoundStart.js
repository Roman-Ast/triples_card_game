import playersArrangement from '../ playersArrangement.js';

const onRoundStart = (msgObject, checkingOtherPlayersConnection) => {
    
    clearInterval(checkingOtherPlayersConnection);
    $('#playerBalance').css({'color': '#fff'});
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
        img.id = card.id;
        cardContainer.appendChild(img);

        cardContainer.classList.add("card");
        const cardsContainer = document.querySelector("#myCards");
        
        if (cardsContainer) {
            cardsContainer.appendChild(cardContainer);
        }
        
    });
    
    const isAdmin = $('#isAdmin').text();
    
    if (isAdmin == 1) {
        allPlayers.forEach(item => {
            $('.playerName').each(function () {
                if ($(this).text() === item.name) {
                    $(this).next().text(item.balance);
                }
            });
        });
        
        const allPlayersNames = msgObject.allPlayers.map(player => player.name);

        $('.playerName').each(function () {
            if (!allPlayersNames.includes($(this).text())) {
                $(this).next().next().children().eq(1).attr('disabled', true);
            }
        });
    }

    if (msgObject.currentFirstWordPlayer === $('#playerName').text()) {
        const audio = new Audio();
        audio.preload = 'auto';
        audio.src = '/audio/nextStep.mpeg';
        audio.play();
    }
    let timer = 0;
    let interval;
    $('.card').on('touchstart', function (event) {
        event.preventDefault();
        interval = setInterval(() => {
            timer += 1.5;
            if (timer >= 100) {
                $('#showCard').attr('cardId', event.target.id);
                $('#showCard').click();
                clearInterval(interval);
                timer = 0;
            }
        }, 10);
        $(this).css({'transform': 'scale(1.1)'});
    });
    
    $('.card').on('touchend', function (event) {
        $(this).css({'transform': 'scale(1)'});
        clearInterval(interval);
        timer = 0;
    });
}

export default onRoundStart;