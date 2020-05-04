import playersArrangement from './ playersArrangement.js';
import calc from './calc.js';
console.log(calc(3, 5));

const conn = new WebSocket('ws://192.168.0.107:8050');



    //проверка на соединение других игроков
    /*const checkingOtherPlayersConnection = setInterval(() => {
        $('#connectedPlayers').empty();
        conn.send(JSON.stringify({
            id: $("#playerId").text(),
            name: $("#playerName").text(),
            balance: $("#playerBalance").text(),
            checkConnection: true
        }));
    }, 1000);*/
    const connectedPlayers = [];

    //устанавливаем cтол ровно по центру
    const tableWidth = $('#table').width();
    const roomHeight = $('#room').height();
    const roomWidth = $('#room').width();

    //оповещаем что баланса мало
    if ($('#playerBalance').text() < 200) {
        $('#playerBalance').css({'color':'red'});

        $('#modalBody').empty();
        $('#modalBody').append('<div>Ваш баланс на исходе, закиньте Русику бабла!!!</div>');

        $('#modal').show();
    }

    $('#frameForAllPlayersCardsClose').on('click', function () {
        $('#frameForAllPlayersCards').hide();
        $('#innerFrame').empty();
    });

    $('#mainFrame').on('click', function () {
        $('#frameForAllPlayersCards').hide();
        $('#innerFrame').empty();
    });

    $('#table')
        .css({'height': tableWidth})
        .css({'left': roomWidth / 2 - tableWidth / 2})
        .css({'top': roomHeight / 2 - tableWidth / 2});

    $('#internalRound')
        .css({'height': tableWidth / 2})
        .css({'width': tableWidth / 2})
        .css({'left': tableWidth / 2 - $('#internalRound').width() / 2})
        .css({'top': tableWidth / 2 - $('#internalRound').width() / 2});

    $('#connectToGame').on('click', function () {
        location.reload();  
    });

    $("#startPlay").on('click', () => {
        clearInterval(checkingOtherPlayersConnection);
        $('#waitingForStart').show();
        $('#waitingForStart').css({'display': 'flex'});

        $('.playerContainer').detach();
        const playerData = {
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

    $("#shareCashBoxAfterOpening").on('click', () => {
        
        const shareCashBoxAfterOpening = {
            shareCashBoxAfterOpening: true,
            counter: 1
        };
        
        conn.send(JSON.stringify(shareCashBoxAfterOpening));
        
    });

    conn.onmessage = (e) => {
        //все модальные окна исчезают через 5 секунд
        /*if ($('#modal').css('display') === 'block') {
            const timeOut = setTimeout(() => {
                $('#modal').hide();
            }, 5000);
        }*/

        const msgObject = JSON.parse(e.data);

        //проверяем кто подключился и ждем остальных, если нужно
        if (msgObject.checkConnection) {
            console.dir(msgObject);
            msgObject.connectedPlayers.forEach(playerName => {
                if (playerName && !connectedPlayers.includes(playerName)) {
                    connectedPlayers.push(playerName);
                }
            });

            connectedPlayers.forEach(playerName => {
                const connectedPlayer = document.createElement('div');
                connectedPlayer.classList.add('connectedPlayer');
                
                if (playerName === $('#playerName').text()) {
                    $(connectedPlayer).text(`Вы подключились...`);
                } else {
                    $(connectedPlayer).text(`Игрок ${playerName} подключился...`);
                }
                
                $('#connectedPlayers').append(connectedPlayer);
            });

        } else if (msgObject.connection_error) {
            clearInterval(checkingOtherPlayersConnection);
            $('#connectedPlayers').empty();
            //очищаем modalBody
            $('#modalBody').empty();

            $('#modalBody').append(`<div>${msgObject.msg}</div>`);

            $('#modal').show();
            $('#startPlay').hide();
            $('#connectToGame').show();

        } else if (msgObject.dataForRoundStarting) {
            
            clearInterval(checkingOtherPlayersConnection);
            //убираем спиннер ожидания
            $('#waitingForStart').hide();

            //очищаем информацию о подключенных игроках
            $('#connectedPlayers').empty();

            //зaполняем селект шагами возможных ставок
            for (let i = msgObject.defaultBet; i < 3000; i += msgObject.stepInBets) {
                $('#betSum').append(`<option value="${i}">${i}</option>`);
            }

            //прячем кнопку "готов" на время раунда
            $("#takeCashBox").css({"display":"none"});
            $("#radiness").css({"display":"none"});
            $("#bet").css({"display":"flex"});

            console.dir(msgObject);
            
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
                img.classList.add('cardFace');
                cardContainer.appendChild(img);

                cardContainer.classList.add("card");
                const cardsContainer = document.querySelector("#myCards");
                
                cardsContainer.appendChild(cardContainer);
            });

        } else if (msgObject.roundStateAfterBetting) {
            $(`#betSum`).empty();
            //удаляем из селекта суммы ненужные options, так как меньше уже поставить нельзя
            if (!msgObject.toCollate) {
                for (let i = msgObject.defaultBet + msgObject.stepInBets; i <= 3000; i += msgObject.stepInBets) {
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
                    .css({'border': '15px solid transparent'})
                    .css({'border-bottom': '15px solid red'});
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

        }else if (msgObject.isRoundEndWithoutShowingUp) {
            $('#modalBody').append(`<div>победитель: ${msgObject.playerTakingConWithoutShowingUp}</div>`);
            $('#modal').show();
        } else if (msgObject.nextRound) {
            $('#collate').hide();
            $('#myCards').empty();
            $('#betSum').empty();
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
        } else if (msgObject.balanceError) {
            $('#modalBody').append(`<div>${msgObject.msg}</div>`);
            $('#modal').show();
        } else if (msgObject.reconnect) {
            clearInterval(checkingOtherPlayersConnection);
            $('#modalBody').append(`<div>${msgObject.msg}</div>`);
            $('#modal').show();
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