
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="Cache-control" content="no-cache">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="-1">
        <title>Тринька</title>
        <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    </head>
    <body>
        <div id="frameForAllPlayersCards">
            <div id="innerFrame">
                <div id="frameForAllPlayersCardsClose">
                    <span>&CircleTimes;</span>
                </div>
            </div>
        </div>
        <div id="modal">
            <div id="modalHeader">Детали раунда</div>
            <div id="modalBody"></div>
            <div id="modalButtons">
                <button class="btn btn-sm btn-secondary" id="modalClose">Ок</button>
            </div>
        </div>
        <div id="mainFrame">
            <div id="room">
                <div id="waitingForStart">
                    <div class="spinner-border text-warning" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div>Ждем других игроков...</div>
                </div>
                <div id="connectedPlayers"></div>
                <div id="table">
                    <div id="internalRound"></div>
                    <div id="cashBox">
                        <div id="round-status"></div>
                        <div id="cashBoxSum"></div>
                        <img src="{{URL::asset('images/table/Coins.png')}}"style="width:100%" alt="card">
                    </div>
                </div>
            </div>
            <div id="myInterface">
                <div id="playerId" style="display:none;">{{ intval($user->id) }}</div>
                <div id="isAdmin" style="display:none;">{{ intval($user->admin) }}</div>
                <div id="userData">
                    <div id="playerName">{{ $user->name }}</div>
                    <div>Ваш баланс:</div>
                    <div id="playerBalance">{{ $user->balance }}</div>
                </div>
                <div id="controllers">
                    <div id="buttons">
                        <div id="radiness">
                            <button id="startPlay" class="btn btn-sm btn-primary">Начать раунд</button>
                            <button id="connect" class="btn btn-sm btn-primary">Подключиться</button>
                        </div>
                        
                        <div id="bet">
                            <button id="save" class="btn btn-sm btn-danger">Пасс</button>
                            <button id="collate" class="btn btn-sm btn-warning">
                                Сравнять:
                                <span id="collateSum"></span>
                            </button>
                            <div id="raise">
                                <button id="makeBet" class="btn btn-sm btn-primary">Поднять</button>
                                <select id="betSum"></select>
                            </div>
                        </div>
                        <button id="openCards" class="btn btn-sm btn-primary">Открыть карты</button>
                        <button id="takeCashBox" class="btn btn-sm btn-success">Забрать не вскрываясь</button>
                        <button id="takeCashBoxAfterOpening" class="btn btn-sm btn-success">Забрать кассу</button>
                        <button id ="shareCashBoxAfterOpening" class="btn btn-sm btn-success ">Разделить кассу</button>
                        <button id="cooking" class="btn btn-sm btn-success">Варить</button>
                        <button id="notCooking" class="btn btn-sm btn-danger">Отказаться</button>
                    </div>
                </div>
                <div id="myCards">
    
                </div>
            </div>
        </div>






        <script src="{{ URL::asset('js/jquery_min.js') }}"></script>
        <script src="{{ URL::asset('js/game.js') }}" type="module"></script>
        <script src="{{ URL::asset('js/gameEventsHandlers/onRoundStart.js') }}" async type="module"></script>
        <script src="{{ URL::asset('js/gameEventsHandlers/onCheckConnection.js') }}" type="module"></script>
        <script src="{{ URL::asset('js/gameEventsHandlers/onBalanceError.js') }}" type="module"></script>
        <script src="{{ URL::asset('js/gameEventsHandlers/onNoneNotWinnersAgreedToCook.js') }}" type="module"></script>
        <script src="{{ URL::asset('js/gameEventsHandlers/onAllWinnersAgreeToCook.js') }}" type="module"></script>
        <script src="{{ URL::asset('js/gameEventsHandlers/onSomeNotWinnersAgreeToCook.js') }}" type="module"></script>
    </body>
</html>
