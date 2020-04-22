const conn = new WebSocket('ws://localhost:8050');
conn.onopen = (e) => console.log("Соединение установлено...!");

$("#startPlay").on('click', () => {
    const playerData = {
        id: document.querySelector("#playerId").innerHTML,
        name: document.querySelector("#playerName").innerHTML,
        balance: document.querySelector("#playerBalance").innerHTML,
        readyToPlay: true
    };
    conn.send(JSON.stringify(playerData));
    
});


conn.onmessage = (e) => {
    //прячем кнопку "готов" на время раунда
    $("#radiness").css({"display":"none"});

    const msgObject = JSON.parse(e.data);
    console.dir(msgObject);
    if (msgObject.alreadyRunningGame) {
        alert(msgObject.alreadyRunningGame);
        return;
    }
    const cards = msgObject.cards;
    const balance = msgObject.balance;
    const allPlayers = msgObject.allPlayers;

    //заполняем поле "баланс" текущего игрока
    document.querySelector("#playerBalance").innerHTML = balance;

    //заполняем поле "другие игроки" текущего игрока
    allPlayers.forEach(player => {
        if (player.name != msgObject.name) {
            const playerContainer = document.createElement("div");
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
    
    const num = defaultBets.length; // Число картинок
    const wrap = $("#cashBox").height(); // Размер "холста" для расположения картинок
    const radius = wrap / 3; // Радиус нашего круга
    console.log(num + " " + wrap + " " + radius);
    
    for (i = 0;i < num; i++){
        let f = 2 / num * i * Math.PI;  // Рассчитываем угол каждой картинки в радианах
        let left = wrap + radius * Math.sin(f) + 'px';
        let top = wrap + radius * Math.cos(f) + 'px';
        $('.bet').eq(i).css({'top':top,'left':left}); // Устанавливаем значения каждой картинке
    }
    

};