import onRoundStart from './gameEventsHandlers/onRoundStart.js';
import onMakeBet from './gameEventsHandlers/onMakeBet.js';
import onOpenCards from './gameEventsHandlers/onOpenCards.js';
import onConnectionError from './gameEventsHandlers/onConnectionError.js';
import onCheckConnection from './gameEventsHandlers/onCheckConnection.js';
import onNextRound from './gameEventsHandlers/onNextRound.js';
import playersArrangement from './ playersArrangement.js';
import onBalanceError from './gameEventsHandlers/onBalanceError.js';
import onCook from './gameEventsHandlers/onCook.js';
import someNotWinnersAgreeToCook from './gameEventsHandlers/onSomeNotWinnersAgreeToCook.js';
import noneNotWinnersAgreedToCook from './gameEventsHandlers/onNoneNotWinnersAgreedToCook.js';
import allWinnersAgreeToCook from './gameEventsHandlers/onAllWinnersAgreeToCook.js';
import waitingForAllSaid from './gameEventsHandlers/onWaitingForAllSaid.js';
import chargeNewBalance from './gameEventsHandlers/onChargeNewBalance.js';
import showCard from './gameEventsHandlers/onShowingCard.js';

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
        this.cookingBtn = $('#cooking');
        this.notCookingBtn = $('#notCooking');
        this.stopServerBtn = $('#stopServer');
        this.passwordGenerateBtn = $('#passwordGenerate');
        this.showCardBtn = $('#showCard');
        this.playersArrangement = playersArrangement;
        this.mainFrame = $('#mainFrame');
        this.frameForAllPlayersCardsClose = $('#frameForAllPlayersCardsClose');
        this.timeOutToBet = null;

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
        this.connectBtn.bind('click', () => {
            this.openSocket();
            this.startRoundBtn.attr('disabled', true);
        });
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
        this.makeBetBtn.bind('click', () => {
            this.send({
                makingBet: true,
                betMakerId: parseInt($('#playerId').text()),
                betMaker: $("#playerName").text(),
                betSum: $("#betSum").val()
            });
        });
        this.saveBtn.bind('click', () => {
            this.send({
                makingBet: true,
                betMakerId: parseInt($('#playerId').text()),
                betMaker: $("#playerName").text(),
                betSum: 0
            });
        });
        this.collateBtn.bind('click', () => {
            this.send({
                makingBet: true,
                betMakerId: parseInt($('#playerId').text()),
                betMaker: $("#playerName").text(),
                betSum: parseInt($('#collateSum').text())
            });
        });
        this.openCardsBtn.bind('click', () => {
            this.send({checkUserCardsValue: true});
        });
        this.takeCashBoxBtn.bind('click', () => {
            this.send({endRoundWithoutShowingUp: true});
        });
        this.takeCashBoxAfterOpeningBtn.bind('click', () => {
            this.send({endRoundAfterOpeningCards: true});
        });
        this.shareCashBoxAfterOpeningBtn.bind('click', () => {
            this.send({shareCashBoxAfterOpening: true});
        });
        this.cookingBtn.bind('click', () => {
            this.send({
                aboutCooking: true,
                cooking: true
            });
            this.cookingBtn.attr('disabled', true);
            this.notCookingBtn.attr('disabled', true);
            $('#playerBalance').text($('#playerBalance').text() - $('#cashBoxSum').text() / 2);
        });
        this.notCookingBtn.bind('click', () => {
            this.send({
                aboutCooking: true,
                cooking: false
            });
            this.cookingBtn.attr('disabled', true);
            this.notCookingBtn.attr('disabled', true);
        });
        this.stopServerBtn.bind('click', () => {
            this.send({
                stopServer: true
            });
        });
        this.showCardBtn.bind('click', () => {
            this.send({
                showCard: true,
                playerName: $('#playerName').text(),
                cardId: this.showCardBtn.attr('cardId')
            });
        });
    },

    send(objToSend, f = ()=>{}) {
        f(objToSend);
        this.ws.send(JSON.stringify(objToSend));
    },

    onMessage (msgObject) {
        this.msgObject = msgObject;
        const composer = {
            checkConnection: onCheckConnection,
            connection_error: onConnectionError,
            dataForRoundStarting: onRoundStart,
            roundStateAfterBetting: onMakeBet,
            dataAfterOpeningCards: onOpenCards,
            nextRound: onNextRound,
            balanceError: onBalanceError,
            dataForCooking: onCook,
            someNotWinnersAgreeToCook,
            noneNotWinnersAgreedToCook,
            allWinnersAgreeToCook,
            waitingForAllSaid,
            chargeNewBalance,
            showCard,
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
        this.connectBtn.hide();
        this.startRoundBtn.show();
        $('#connectedPlayers').append('<div style="color:#fff;">Соединение установлено!</div>');
        console.log('Соединение установлено');
        this.createInterval();
    },

    openSocket() {
        this.ws = new WebSocket("ws://192.168.1.102:8050");
        this.ws.onopen = () => this.onOpenSocket();
        this.ws.onmessage = (e) => this.onMessage(JSON.parse(e.data));
        this.ws.onclose = () => this.onClose();
    },

    onClose() {
        $('#connectedPlayers').append('<div style="color:#fff;">Сервер не отвечает!</div>');
    },

    createInterval() {
        this.checkingOtherPlayersConnection = setInterval(() => {
            this.send(
                {
                    id: $("#playerId").text(),
                    isAdmin: $("#isAdmin").text(),
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

$('#frameForAllPlayersCardsClose').on('click', function () {
    $('#frameForAllPlayersCards').hide();
    $('#innerFrame').empty();
});

$('#mainFrame').on('click', function () {
    $('#frameForAllPlayersCards').hide();
    $('#innerFrame').empty();
});
$('.chargeNewBalance').on('click', function (e) {
    e.preventDefault();
    
    socketUnit.send({
        chargeNewBalance: true,
        name: $(this).prev().attr('ownerName'),
        newBalance: $(this).prev().val(),
        id: $(this).next().val()
    });
    
    $(this).prev().val('');
});
$('.stepInsteadUser').on('click', function (e) {
    e.preventDefault();
    
    socketUnit.send({
        makingBet: true,
        betMakerId: parseInt($(this).next().val()),
        betMaker: $("#playerName").text(),
        betSum: 0
    });
    
    $(this).prev().val('');
});
$('.nav-item-inactive').on('click', function () {
    $('.nav-item-inactive').each(function () {
       $(this).css({'color':'#fff'}); 
    });

    $(this).css({'color': 'yellow'});
});

