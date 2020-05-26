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

    //Выводим модальное окно с информацией о раунде
    /*const modalBody = document.querySelector("#modalBody");

    const currentRoundField = document.createElement("div");
    currentRoundField.innerHTML = `Текущий раунд: ${msgObject.currentRoundId}`;
    const defaultBetField = document.createElement("div");
    defaultBetField.innerHTML = `Кон: ${msgObject.defaultBet}`;
    const currentDistributorField = document.createElement("div");
    currentDistributorField.innerHTML = `Раздающий: ${msgObject.currentDistributor}`;
    const currentFirstWordPlayerField = document.createElement("div");
    currentFirstWordPlayerField.innerHTML = `Первое слово: ${msgObject.currentFirstWordPlayer}`;

    modalBody.appendChild(currentRoundField)
            .appendChild(defaultBetField)
            .appendChild(currentDistributorField)
            .appendChild(currentFirstWordPlayerField);
            
    $("#modal").show(1000);
    $("#modalClose").on("click", function () {
        $("#modal").hide(1000); 
    });
    
    //смещаем модальное окно на половину вправо и вниз
    const roomHeight = $("#room").height();
    const modalHeight = $("#modal").height();
    const roomWidth = $("#room").width();
    const modalWidth = $("#modal").width();
    const tableHeight = $("#table").height();
    const tableWidth = $("#table").width();

    $("#modal")
        .css({"left": `calc(${roomWidth / 2 - modalWidth / 2}px)`})
        .css({"top": `calc(${roomHeight / 2 - modalHeight / 2}px)`});*/


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
            
}

export default onRoundStart;