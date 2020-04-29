const conn = new WebSocket('ws://192.168.0.107:8050');
conn.onopen = (e) => {
    console.log("Соединение установлено...!");
    
}

//устанавливаем cтол ровно по центру
const tableWidht = $('#table').width();
const roomHeight = $('#room').height();
const roomWidth = $('#room').width();

$('#table')
    .css({'height': tableWidht})
    .css({'left': roomWidth / 2 - tableWidht / 2})
    .css({'top': roomHeight / 2 - tableWidht / 2});

import playersArrangement from './ playersArrangement.js';


$("#startPlay").on('click', () => {
    $('.playerContainer').detach();
    const playerData = {
        id: document.querySelector("#playerId").innerHTML,
        name: document.querySelector("#playerName").innerHTML,
        balance: document.querySelector("#playerBalance").innerHTML,
        readyToPlay: true
    };
    conn.send(JSON.stringify(playerData));  
});

$("#makeBet").on('click', () => {
    const betData = {
        makingBet: true,
        betMaker: $("#playerName").html(),
        betSum: $("#betSum").val()
    };

    /*$('.playerBetField').each(function () {
       if ($(this).attr('ownerName') == $('#playerName').text()) {
           if (!parseInt($(this).text())) {
                $(this).text(betData.betSum);
           } else {
                $(this).text(parseInt($(this).text()) + betData.betSum);
           }
       };
    });*/

    conn.send(JSON.stringify(betData));
});

$("#save").on('click', () => {
    const betData = {
        makingBet: true,
        betMaker: $("#playerName").html(),
        betSum: 0
    };
    conn.send(JSON.stringify(betData));
});

$("#openCards").on('click', () => {
    const checkUserCardsValue = {
        checkUserCardsValue: true,
    };
    conn.send(JSON.stringify(checkUserCardsValue));
});

$("#takeCashBox").on('click', () => {
    const endRound = {
        endRoundWithoutShowingUp: true,
    };
    conn.send(JSON.stringify(endRound));
});


conn.onmessage = (e) => {
    //все модальные окна исчезают через 5 секунд
    setTimeout(() => {
        $('#modal').hide();
    }, 5000);

    const msgObject = JSON.parse(e.data);

    if (msgObject.dataForRoundStarting) {
        
        //прячем кнопку "готов" на время раунда
        $("#radiness").css({"display":"none"});
        $("#bet").css({"display":"flex"});

        console.dir(msgObject);
        if (msgObject.alreadyRunningGame) {
            alert(msgObject.alreadyRunningGame);
            return;
        }
        const cards = msgObject.cards;
        const balance = msgObject.balance;
        const allPlayers = msgObject.allPlayers;
        const currentDistributor = msgObject.currentDistributor;
        const currentFirstWordPlayer = msgObject.currentFirstWordPlayer;

        //Выводим модальное окно с информацией о раунде
        const modalBody = document.querySelector("#modalBody");

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
            .css({"top": `calc(${roomHeight / 2 - modalHeight / 2}px)`});


        if (msgObject.name !== currentFirstWordPlayer) {
            $("#makeBet").attr("disabled", true);
            $("#save").attr("disabled", true);
            $("#betSum").prop("disabled", true);
        }
        //заполняем поле "баланс" текущего игрока
        document.querySelector("#playerBalance").innerHTML = balance;

        //заполняем комнату игроками
        playersArrangement(
            allPlayers,
            $('#room'),
            msgObject.name,
            msgObject.currentDistributor,
            msgObject.currentFirstWordPlayer
        );

        //заполняем поле "касса" суммой всех дефолтных ставок
        ((defaultBets) => {
            const sumOfDefaultBets = defaultBets.reduce((acc, item) => acc + item.defaultBet, 0);
            $("#cashBox").html(sumOfDefaultBets);
        })(msgObject.defaultBets);

        //заполняем поле "карты" текущего игрока
        cards.forEach(card => {
            const cardContainer = document.createElement("div");
            cardContainer.innerHTML = `${card.name} ${card.suit}`;
            cardContainer.classList.add("card");
            const cardsContainer = document.querySelector("#myCards");
            cardsContainer.appendChild(cardContainer);
        });

        //зaполняем селект шагами возможных ставок
        for (let i = 50; i < 3000; i += 50) {
            $('#betSum').append(`<option value="${i}">${i}</option>`);
        }
        //если раунд 1 то показываем "поднять" или "пасс"
        if (msgObject.currentRoundId == 1) {
            $('#collate').hide();
        }
        //если сумма raise не выбрана деактивируем кнопку "поднять"
        if (!$('#betSum').val()) {
            $('#makeBet').attr('disabled', true);
        }
        $('#betSum').on('change', function () {
            $('#makeBet').removeAttr('disabled');
        })

    } else if (msgObject.roundStateAfterBetting) {
        console.dir(msgObject);
        //показываем ставки игроков на столе
        $('.playerBetField').each(function (params) {
            for (const name in msgObject.bets) {
                if (name === $(this).attr('ownerName')) {
                    if (msgObject.bets[name] === 0){
                        $(this).text('пасс');
                        $(this).css({'color': 'red'});
                        $(this).css({'font-style': 'italic'});
                    }
                    else $(this).text(msgObject.bets[name]);
                }
            }
            
        });

        //добавляем все ставки игроков в общую кассу на столе
        const sumOfDefaultBets = msgObject.defaultBets.reduce((acc, item) => acc + item.defaultBet, 0);
        const sumOfBets = Object.values(msgObject.bets).reduce((acc, item) => acc + item, 0);
        $('#cashBox').text(sumOfDefaultBets + sumOfBets);

        //если есть возможность открыть карты
        if (msgObject.playerOpenCardAbility) {
            $("#bet").hide();
            if ($("#playerName").html() === msgObject.playerOpenCardAbility) {
                //показываем кнопку "вскрыть карты"
                $("#openCards").css({"display": "block"});
                $("#bet").hide();
            }
        }//если все пасанули
        else if (msgObject.playerTakingConWithoutShowingUp) {
            $('#modalBody').empty();
            $('#modalBody').append(
                `<div>
                    Игрок ${msgObject.playerTakingConWithoutShowingUp} забирает кассу не вскрываясь!
                </div>`
            );
            
            $('#modal').show();
            $("#bet").hide();
            if ($("#playerName").html() === msgObject.playerTakingConWithoutShowingUp) {
                //показываем кнопку "забрать кон"
                $("#takeCashBox").css({"display": "block"});
                $("#bet").hide();
            }
        }

        //удаляем флаг следующего хода у текущего игрока
        $('.firstWordFlag').detach();

        //перемещаем флаг следующего хода
        const firstWordFlag = document.createElement("div");
        firstWordFlag.classList.add('firstWordFlag');
                    
        $(firstWordFlag)
            .css({'border': '10px solid transparent'})
            .css({'border-bottom': '10px solid green'});
        $('.playerContainer').each(function() {
            if ($(this).attr('ownerName') === msgObject.nextStepPlayer) {
                $(this).append(firstWordFlag);
            }
        });

        const lastBet = msgObject.lastBet;
        $("#playerBalance").html(msgObject.balanceOfAllPlayers[$("#playerName").html()]);
        

        if (msgObject.nextStepPlayer !== $("#playerName").html()) {
            $("#save").attr('disabled', true);
            $("#makeBet").attr('disabled', true);
            $('#betSum').prop('disabled', true);
        } else {
            $("#save").removeAttr('disabled');
            $('#betSum').prop('disabled', false);
            //если сумма raise не выбрана деактивируем кнопку "поднять"
            if (!$('#betSum').val()) {
                $('#makeBet').attr('disabled', true);
            }
            $('#betSum').on('change', function () {
                $('#makeBet').removeAttr('disabled');
            })
        }
    } else if (msgObject.dataAfterOpeningCards) {
        console.dir(msgObject);


    }else if (msgObject.isRoundEndWithoutShowingUp) {
        $('#modalBody').append(`<div>победитель: ${msgObject.playerTakingConWithoutShowingUp}</div>`);

    } else if (msgObject.nextRound) {
        //очищаем поле карты у игрока
        $('#myCards').empty();
        $('#cashBox').empty();
        $('#modalBody').empty();
        $('#otherPlayers').empty();
        alert('next round');
        $("#radiness").css({"display":"block"});
        $("#takeCashBox").css({"display":"none"});
        $("button").each(function () {
           $(this).removeAttr('disabled'); 
        });
    }
    

};



/*расположение ставок по кругу
    const num = defaultBets.length; // Число картинок
    const wrap = $("#cashBox").height(); // Размер "холста" для расположения картинок
    const radius = wrap / 3; // Радиус нашего круга
    
    for (i = 0;i < num; i++){
        let f = 2 / num * i * Math.PI;  // Рассчитываем угол каждой картинки в радианах
        let left = wrap + radius * Math.sin(f) + 'px';
        let top = wrap + radius * Math.cos(f) + 'px';
        $('.bet').eq(i).css({'top':top,'left':left}); // Устанавливаем значения каждой картинке
    }*/