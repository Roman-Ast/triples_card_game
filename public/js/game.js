import onRoundStart from './gameEventsHandlers/onRoundStart.js';
import onMakeBet from './gameEventsHandlers/onMakeBet.js';
import onOpenCards from './gameEventsHandlers/onOpenCards.js';
import onConnectionError from './gameEventsHandlers/onConnectionError.js';
import onCheckConnection from './gameEventsHandlers/onCheckConnection.js';
import onNextRound from './gameEventsHandlers/onNextRound.js';
import playersArrangement from './ playersArrangement.js';
import onBalanceError from './gameEventsHandlers/onBalanceError.js';
import reconnect from './gameEventsHandlers/onReconnect.js';

const socketUnit = {
    init() {
        this.connectBtn = $('#connect');
        this.startRoundBtn = $('#startPlay');
        this.makeBetBtn = $("#makeBet");
        this.saveBtn = $("#save");
        this.collateBtn = $("#collate");
        this.openCardsBtn = $("#openCards");
        this.takeCashBoxBtn = $('#takeCashBox');
        this.takeCashBoxAfterOpeningBtn = $('#takeCashBoxAfterOpening');
        this.shareCashBoxAfterOpeningBtn = $('#shareCashBoxAfterOpening');
        this.playersArrangement = playersArrangement;

        this.initCss();
        this.bindEvents();
    },
    initCss() {
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
    },

    bindEvents() {
        this.connectBtn.bind('click', () => this.openSocket());
        this.startRoundBtn.bind('click', () => this.send(
            {readyToPlay: true},
            () => {
                clearInterval(this.checkingOtherPlayersConnection);
                $('#waitingForStart').show();
                $('#waitingForStart').css({'display': 'flex'});
                $('.playerContainer').detach();
            }, 
            )
        );
        this.makeBetBtn.bind('click', () => this.send({
                makingBet: true,
                betMaker: $("#playerName").html(),
                betSum: $("#betSum").val()
        }));
        this.saveBtn.bind('click', () => this.send({
            makingBet: true,
            betMaker: $("#playerName").html(),
            betSum: 0
        }));
        this.collateBtn.bind('click', () => this.send({
            makingBet: true,
            betMaker: $("#playerName").text(),
            betSum: parseInt($('#collateSum').text())
        }));
        this.openCardsBtn.bind('click', () => this.send({
            checkUserCardsValue: true
        }));
        this.takeCashBoxBtn.bind('click', () => this.send({
            endRoundWithoutShowingUp: true
        }));
        this.takeCashBoxAfterOpeningBtn.bind('click', () => this.send({
            endRoundAfterOpeningCards: true
        }));
        this.shareCashBoxAfterOpeningBtn.bind('click', () => this.send({
            shareCashBoxAfterOpening: true
        }));

        $('#frameForAllPlayersCardsClose').bind('click', function () {
            $('#frameForAllPlayersCards').hide();
            $('#innerFrame').empty();
        });
    
        $('#mainFrame').bind('click', function () {
            $('#frameForAllPlayersCards').hide();
            $('#innerFrame').empty();
        });
        
    },

    send(objToSend, f = ()=>{}) {
        f();
        this.ws.send(JSON.stringify(objToSend));
    },

    onMessage (msgObject) {
        const composer = {
            checkConnection: onCheckConnection,
            connection_error: onConnectionError,
            dataForRoundStarting: onRoundStart,
            roundStateAfterBetting: onMakeBet,
            dataAfterOpeningCards: onOpenCards,
            nextRound: onNextRound,
            balanceError: onBalanceError,
            reconnect: () => {
                onRoundStart(msgObject, this.checkingOtherPlayersConnection),
                onMakeBet(msgObject)
            }
        };

        for (const key in msgObject) {
            if (composer.hasOwnProperty(key)) {
                composer[key](msgObject, this.checkingOtherPlayersConnection, this.playersArrangement)
            }
        }
    }, 
    onOpenSocket() {
        console.log('Соединение установлено');
    },

    openSocket() {
        this.ws = new WebSocket("ws://192.168.0.107:8050");
        this.ws.onopen = () => this.onOpenSock;
        this.ws.onmessage = (e) => this.onMessage(JSON.parse(e.data));
        this.ws.onclose = () => this.onClose();
        this.createInterval();
    },

    onClose() {
        console.log('Игрок отключился');
    },

    createInterval() {
        this.checkingOtherPlayersConnection = setInterval(() => {
            this.send(
                {
                    id: $("#playerId").text(),
                    name: $("#playerName").text(),
                    balance: $("#playerBalance").text(),
                    checkConnection: true
                }, 
                () => $('#connectedPlayers').empty()
            );
        }, 1000);
    }

};

window.addEventListener('load', () => socketUnit.init());









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