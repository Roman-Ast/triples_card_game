const conn = new WebSocket('ws://192.168.0.107:8050');
conn.onopen = (e) => {
    console.log("Соединение установлено...!");
    
}

//устанавливаем cтол ровно по центру
const tableWidth = $('#table').width();
const roomHeight = $('#room').height();
const roomWidth = $('#room').width();


$('#table')
    .css({'height': tableWidth})
    .css({'left': roomWidth / 2 - tableWidth / 2})
    .css({'top': roomHeight / 2 - tableWidth / 2});

$('#internalRound')
    .css({'height': tableWidth / 2})
    .css({'width': tableWidth / 2})
    .css({'left': tableWidth / 2 - $('#internalRound').width() / 2})
    .css({'top': tableWidth / 2 - $('#internalRound').width() / 2});

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

$("#collate").on('click', function () {
    const betData = {
        makingBet: true,
        betMaker: $("#playerName").text(),
        betSum: parseInt($('#collateSum').text())
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

$("#takeCashBoxAfterOpening").on('click', () => {
    const endRound = {
        endRoundAfterOpeningCards: true,
    };

    conn.send(JSON.stringify(endRound));
});

$('#shareCashBoxAfterOpening').on('click', () => {
    const endRound = {
        endRoundAfterOpeningCards: true,
    };

    conn.send(JSON.stringify(endRound));
});


conn.onmessage = (e) => {
    //все модальные окна исчезают через 5 секунд
    /*if ($('#modal').css('display') === 'block') {
        const timeOut = setTimeout(() => {
            $('#modal').hide();
        }, 5000);
    }*/

    const msgObject = JSON.parse(e.data);

    //зaполняем селект шагами возможных ставок
    for (let i = msgObject.defaultBet; i < 3000; i += msgObject.stepInBets) {
        $('#betSum').append(`<option value="${i}">${i}</option>`);
    }

    $('#betSum').prop('disabled', false);
    //очищаем modalBody
    $('#modalBody').empty();

    if (msgObject.dataForRoundStarting) {
        
        //прячем кнопку "готов" на время раунда
        $("#takeCashBox").css({"display":"none"});
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
            msgObject.currentDistributor,
            msgObject.currentFirstWordPlayer
        );

        //заполняем поле "касса" суммой всех дефолтных ставок
        ((defaultBets) => {
            const sumOfDefaultBets = defaultBets.reduce((acc, item) => acc + item.defaultBet, 0);
            $("#cashBoxSum").text(sumOfDefaultBets);
        })(msgObject.defaultBets);

        //заполняем поле "карты" текущего игрока
        cards.forEach(card => {
            const cardContainer = document.createElement("div");
            
            const img = new Image(70, 120);
            img.src = card.face;
            img.style.width = '100%';
            cardContainer.appendChild(img);

            cardContainer.classList.add("card");
            const cardsContainer = document.querySelector("#myCards");
            cardsContainer.appendChild(cardContainer);
        });

    } else if (msgObject.roundStateAfterBetting) {
        $(`#betSum`).empty();
        //удаляем из селекта суммы ненужные options, так как меньше уже поставить нельзя
        for (let i = msgObject.toCollate + msgObject.stepInBets; i <= 3000; i += msgObject.stepInBets) {
            $('#betSum').append(`<option value="${i}">${i}</option>`);
        }
        
        console.dir(msgObject);
        //показываем ставки игроков на столе
        $('.playerBetField').each(function (params) {
            for (const name in msgObject.bets) {
                if (name === $(this).attr('ownerName')) {
                    $(this).text(msgObject.bets[name]);
                }
            }
        });

        //устанавливаем "пасс" тем, кто хоть раз пасанул
        $('.playerBetField').each(function (params) {
            msgObject.savingPlayers.forEach(name => {
                if (name === $(this).attr('ownerName')) {
                    $(this).text('пасс');
                    $(this).css({'color': 'red'});
                    $(this).css({'text-shadow': '1px 1px 2px #fff'});
                    $(this).css({'font-style': 'italic'});
                }
            });
        });

        //если есть возможность колировать(поддержать), то показываем соответствующую кнопку
        if (msgObject.toCollate) {
            if ($("#playerName").text() === msgObject.nextStepPlayer) {
                $(collateSum).text(msgObject.toCollate);
                $('#collate').show();
            }
        }

        //добавляем все ставки игроков в общую кассу на столе
        const sumOfDefaultBets = msgObject.defaultBets.reduce((acc, item) => acc + item.defaultBet, 0);
        const sumOfBets = Object.values(msgObject.bets).reduce((acc, item) => acc + item, 0);
        $('#cashBoxSum').text(sumOfDefaultBets + sumOfBets);

        //если есть возможность открыть карты
        if (msgObject.playerOpenCardAbility) {
            //удаляем флаг следующего хода у текущего игрока
            $('#bet').hide();
            $('.firstWordFlag').detach();

            //перемещаем флаг на игрока, который вскрывает карты
            const firstWordFlag = document.createElement("div");
            firstWordFlag.classList.add('firstWordFlag');
            $(firstWordFlag)
                .css({'border': '10px solid transparent'})
                .css({'border-bottom': '10px solid #007BFF'});
            $('.playerContainer').each(function() {
                if ($(this).attr('ownerName') === msgObject.playerOpenCardAbility) {
                    $(this).append(firstWordFlag);
                }
            });

            if ($("#playerName").html() === msgObject.playerOpenCardAbility) {
                //показываем кнопку "вскрыть карты"
                $("#openCards").css({"display": "block"}); 
            }
        } else {
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
        }
        
        //если все пасанули
        if (msgObject.playerTakingConWithoutShowingUp) {
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

        $("#playerBalance").html(msgObject.balanceOfAllPlayers[$("#playerName").html()]);
        
        //блокируем кнопки тем, кто не ходит и разблокируем тому, чей ход
        if (msgObject.nextStepPlayer !== $("#playerName").html()) {
            $("#save").attr('disabled', true);
            $("#makeBet").attr('disabled', true);
            $('#betSum').prop('disabled', true);
            $('#collate').attr('disabled', true);
        } else {
            $("#save").removeAttr('disabled');
            $("#makeBet").removeAttr('disabled');
            $('#collate').removeAttr('disabled');
            $('#betSum').prop('disabled', false);
        }
    } else if (msgObject.dataAfterOpeningCards) {
        //показываем все карты всем игрокам, чтобы убедиться в победителе

        $('#modalBody').empty();
        console.dir(msgObject);
        if (msgObject.winnerAfterOpening.length === 1) {
            $("#openCards").css({"display": "none"});
            $("#takeCashBoxAfterOpening").show({"display": "block"});

            $('#modalBody').append(
                `<div>Победитель: ${msgObject.winnerAfterOpening[0]}</div>`
            );

            $('#modalBody').append(
                `<div>Касса: ${msgObject.totalCashBox}</div>`
            );

            $('#modal').show();
        } else {
            $("#openCards").css({"display": "none"});
            $('#shareCashBoxAfterOpening').show();

            $('#modalBody').append(`Победители:`);

            msgObject.winnerAfterOpening.forEach(winner => {
                $('#modalBody').append(winner);
            });
        
        }
    }else if (msgObject.isRoundEndWithoutShowingUp) {
        $('#modalBody').append(`<div>победитель: ${msgObject.playerTakingConWithoutShowingUp}</div>`);
        $('#modal').show();
    } else if (msgObject.nextRound) {
        //очищаем поле карты у игрока
        $('#myCards').empty();
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