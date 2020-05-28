const onMakeBet = (msgObject, checkingOtherPlayersConnection, playersArrangement) => {

    const isAdmin = $('#isAdmin').text();
    
    if (isAdmin == 1) {
        msgObject.allPlayers.forEach(item => {
            $('.playerName').each(function () {
                if ($(this).text() === item.name) {
                    $(this).next().text(item.balance);
                }
            });
        });
    }

    $(`#betSum`).empty();
    //удаляем из селекта суммы ненужные options, так как меньше уже поставить нельзя
    if (!msgObject.toCollate) {
        for (let i = msgObject.defaultBet; i <= 3000; i += msgObject.stepInBets) {
            $('#betSum').append(`<option value="${i}">${i}</option>`);
        }
    } else {
        for (let i = msgObject.toCollate + msgObject.stepInBets; i <= 3000; i += msgObject.stepInBets) {
            $('#betSum').append(`<option value="${i}">${i}</option>`);
        }
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
                $(this).css({'font-style': 'italic'});
            }
        });
    });

    //если есть возможность колировать(поддержать), то показываем соответствующую кнопку
    if (msgObject.toCollate) {
        if ($("#playerName").text() === msgObject.nextStepPlayer) {
            $('#collate').show();
            $(collateSum).text(msgObject.toCollate);
        }
    }

    //добавляем все ставки игроков в общую кассу на столе
    const sumOfBets = Object.values(msgObject.bets).reduce((acc, item) => acc + item, 0);
    
    if (!msgObject.isCooking) {
        const sumOfDefaultBets = msgObject.defaultBets.reduce((acc, item) => acc + item.defaultBet, 0);
        $('#cashBoxSum').text(sumOfDefaultBets + sumOfBets);
    } else {
        $('#cashBoxSum').text(msgObject.roundCashBox);
    }
    
   

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
            .css({'border-bottom': '10px solid blue'});
        $('.playerContainer').each(function() {
            if ($(this).attr('ownerName') === msgObject.playerOpenCardAbility) {
                $(this).append(firstWordFlag);
            }
        });

        if ($("#playerName").text() === msgObject.playerOpenCardAbility) {
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
            .css({'border-bottom': '10px solid #FFC107'});
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
        
        //$('#modal').show();
        $("#bet").hide();
        if ($("#playerName").text() === msgObject.playerTakingConWithoutShowingUp) {
            //показываем кнопку "забрать кон"
            $("#takeCashBox").css({"display": "block"});
            $("#bet").hide();
        }
    }

    $("#playerBalance").text(msgObject.balanceOfAllPlayers[$("#playerName").text()]);
    
    
    //блокируем кнопки тем, кто не ходит и разблокируем тому, чей ход
    if (msgObject.nextStepPlayer !== $("#playerName").text()) {
        $("#save").attr('disabled', true);
        $("#makeBet").attr('disabled', true);
        $('#betSum').prop('disabled', true);
        $('#collate').attr('disabled', true);
        
    
    } else {
        $("#save").removeAttr('disabled');
        $("#makeBet").removeAttr('disabled');
        $('#collate').removeAttr('disabled');
        $('#betSum').prop('disabled', false);
        if ( $("#playerBalance").text() < msgObject.toCollate || $("#playerBalance").text() < msgObject.defaultBet) {
            $('#betSum').prop('disabled', true);
            $('#collate').attr('disabled', true);
            $('#makeBet').attr('disabled', true);
            $("#playerBalance").css({'color': 'red'});
        }
    }
    
    
};

export default onMakeBet;