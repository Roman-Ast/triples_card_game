const conn = new WebSocket('ws://localhost:8050');
conn.onopen = (e) => console.log("Соединение установлено...!");

$("#betSum").on('input', function () {
    if ($(this).val() % 5 !== 0) {
         $(this).css({'border': '1px solid red'});
         $("#makeBet").attr('disabled', true);
    } else {
         $(this).css({'border': '#ccc'});
         $("#makeBet").removeAttr('disabled');
    }
});

$("#startPlay").on('click', () => {
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

$("#takeCashBox").on('click', () => {
    const endRound = {
        endRoundWithoutShowingUp: true,
    };
    conn.send(JSON.stringify(endRound));
});


conn.onmessage = (e) => {
    const msgObject = JSON.parse(e.data);

    if (msgObject.dataForRoundStarting) {
        //прячем кнопку "готов" на время раунда
        $("#radiness").css({"display":"none"});

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
                
        if (msgObject.name !== currentFirstWordPlayer) {
            $("#makeBet").attr("disabled", true);
        }
        //заполняем поле "баланс" текущего игрока
        document.querySelector("#playerBalance").innerHTML = balance;

        //заполняем поле "другие игроки" текущего игрока
        allPlayers.forEach(player => {
            if (player.name != msgObject.name) {
                
                const playerContainer = document.createElement("div");
                if (player.name === currentDistributor) {
                    playerContainer.classList.add('distributor');
                }
                const playerDataContainer = document.createElement("div");
                playerDataContainer.innerHTML = `${player.name} ${player.balance}`;
                playerContainer.classList.add("otherPlayer");
                const otherPlayersContainer = document.querySelector("#otherPlayers");
                const imgContainer = document.createElement("div");
                const img = new Image(24, 24);
                img.src = 'https://img.icons8.com/wired/2x/circled-user.png';
                imgContainer.appendChild(img); 
                playerContainer.appendChild(imgContainer);
                playerContainer.appendChild(playerDataContainer);
                otherPlayersContainer.appendChild(playerContainer);
            }
        });

        //отмечаем раздававшего
        if (msgObject.name === currentDistributor) {
            $('#userData').append(`<div>(раздающий)</div>`);
        }

        //заполняем поле "карты" текущего игрока
        cards.forEach(card => {
            const cardContainer = document.createElement("div");
            cardContainer.innerHTML = `${card.name} ${card.suit}`;
            cardContainer.classList.add("card");
            cardsContainer = document.querySelector("#myCards");
            cardsContainer.appendChild(cardContainer);
        });

        //заполняем дефолтными ставками поле "кон"
        const defaultBets = msgObject.defaultBets;
        const cashBox = document.querySelector("#cashBox");

        defaultBets.forEach(item => {
            const betContainer = document.createElement("div");
            betContainer.classList.add("bet");
            const betMakerContainer = document.createElement("div");
            const defaultBetContainer = document.createElement("div");
            betMakerContainer.innerHTML = item.betMaker;
            defaultBetContainer.innerHTML = item.defaultBet;
            betContainer.appendChild(betMakerContainer);
            betContainer.appendChild(defaultBetContainer);
            cashBox.appendChild(betContainer);
        });

    } else if (msgObject.roundStateAfterBetting) {
        
        if (msgObject.playerOpenCardAbility) {
            if ($("#playerName").html() === msgObject.playerOpenCardAbility) {
                //показываем кнопку "вскрыть карты"
                $("#openCards").css({"display": "block"});
            }
        } else if (msgObject.playerTakingConWithoutShowingUp) {
            if ($("#playerName").html() === msgObject.playerTakingConWithoutShowingUp) {
                //показываем кнопку "забрать кон"
                $("#takeCashBox").css({"display": "block"});
            }
        }

        const lastBet = msgObject.lastBet;
        $("#playerBalance").html(msgObject.balanceOfAllPlayers[$("#playerName").html()]);
        console.dir(msgObject);

        if (msgObject.nextStepPlayer !== $("#playerName").html()) {
            $("#save").attr('disabled', true);
            $("#makeBet").attr('disabled', true);
            $("#leaveGame").attr('disabled', true);
        } else {
            $("#save").removeAttr('disabled');
            $("#makeBet").removeAttr('disabled');
            $("#leaveGame").removeAttr('disabled');
            $("#betSum").on('input', function () {
                if ($(this).val() < lastBet || $(this).val() % 5 !== 0) {
                     $(this).css({'border': '1px solid red'});
                     $("#makeBet").attr('disabled', true);
                } else {
                     $(this).css({'border': '#ccc'});
                     $("#makeBet").removeAttr('disabled');
                }
            });
        }
    } else if (msgObject.isRoundEndWithoutShowingUp) {
        alert(`победитель: ${msgObject.playerTakingConWithoutShowingUp}`);
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