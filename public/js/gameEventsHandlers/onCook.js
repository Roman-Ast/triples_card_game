import playersArrangement from '../ playersArrangement.js';

const onCook = (msgObject) => {
    $('#betSum').empty();

    //зaполняем селект шагами возможных ставок
    for (let i = msgObject.defaultBet; i < 3000; i += msgObject.stepInBets) {
        $('#betSum').append(`<option value="${i}">${i}</option>`);
    }
    $('#betSum').prop('disabled', false);
    //прячем кнопку "готов" на время раунда
    $('button').each(function () {
        $(this).css({'display': 'none'});
    });

    $('#save').css({'display': 'flex'});
    $('#raise').css({'display': 'flex'});

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
    document.querySelector("#playerBalance").innerHTML = balance;

    //заполняем комнату игроками
    playersArrangement(
        allPlayers,
        $('#room'),
        msgObject.name,
        currentDistributor,
        currentFirstWordPlayer
    );

    $("#cashBoxSum").text(msgObject.totalCashBox);

    //заполняем поле "карты" текущего игрока
    $('#myCards').empty();
    cards.forEach(card => {
        
        const cardContainer = document.createElement("div");
        const img = new Image(96.4, 144.6);
        img.src = card.face;
        img.classList.add('cardFace');
        cardContainer.appendChild(img);

        cardContainer.classList.add("card");
        const cardsContainer = document.querySelector("#myCards");
        
        cardsContainer.appendChild(cardContainer);
    });
            
}

export default onCook;