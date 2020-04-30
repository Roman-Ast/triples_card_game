
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="Cache-Control" content="no-cache">
        <title>Тринька</title>
        <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    </head>
    <body>
        <div id="modal">
            <div id="modalHeader">Детали раунда</div>
            <div id="modalBody"></div>
            <div id="modalButtons">
                <button class="btn btn-sm btn-secondary" id="modalClose">Ок</button>
            </div>
        </div>
        <div id="mainFrame">
            <div id="room">
                <div id="table">
                    <div id="internalRound"></div>
                    <div id="cashBox">
                        <div id="cashBoxSum"></div>
                        <img src="{{URL::asset('images/table/chips24.png')}}" alt="card">
                    </div>
                </div>
            </div>
            <div id="myInterface">
                <div id="playerId" style="display:none;">{{ intval($user->id) }}</div>
                <div id="userData">
                    <div id="playerName">{{ $user->name }}</div>
                    <div>Ваш баланс:</div>
                    <div id="playerBalance">{{ $user->balance }}</div>
                </div>
                <div id="controllers">
                    <div id="buttons">
                        <div id="radiness">
                            <button id="startPlay" class="btn btn-sm btn-primary">Готов</button>
                        </div>
                        
                        <div id="bet">
                            <button id="save" class="btn btn-sm btn-danger">Пасс</button>
                            <button id="collate" class="btn btn-sm btn-warning">Уравнять</button>
                            <div id="raise">
                                <select id="betSum">
                                    <option disabled selected>сумма</option>
                                </select>
                                <button id="makeBet" class="btn btn-sm btn-success">Поднять</button>
                            </div>
                        </div>
                        <button id="openCards" class="btn btn-sm btn-primary">Открыть карты</button>
                        <button id="takeCashBox" class="btn btn-sm btn-success">Забрать не вскрываясь</button>
                    </div>
                </div>
                <div id="myCards">
    
                </div>
            </div>
        </div>






        <script src="{{ URL::asset('js/jquery_min.js') }}"></script>
        <script src="{{ URL::asset('js/game.js') }}" type="module"></script>
        
    </body>
</html>
