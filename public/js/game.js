const startPlayBtn = document.querySelector("#startPlay");

startPlayBtn.onclick = () => {
    const playerData = {
        name: document.querySelector("#playerName").innerHTML,
        money: document.querySelector("#playerBalance").innerHTML,
        readyToPlay: true
    };
    conn.send(JSON.stringify(playerData));
    
};

const conn = new WebSocket('ws://localhost:8050');
conn.onopen = (e) => console.log("Соединение установлено...!");
conn.onmessage = (e) => {
    const msgObject = JSON.parse(e.data);
    console.dir(msgObject);
    if (msgObject.alreadyRunningGame) {
        alert(msgObject.alreadyRunningGame);
        return;
    }
    const cards = msgObject.cards;

    cards.forEach(card => {
        const cardContainer = document.createElement("div");
        cardContainer.innerHTML = `${card.name} ${card.suit}`;
        cardContainer.classList.add("card");
        cardsContainer = document.querySelector("#myCards");
        cardsContainer.appendChild(cardContainer);
    });

};