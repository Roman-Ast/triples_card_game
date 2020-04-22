
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Тринька</title>
        <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    </head>
    <body>
        @if (Session::has('message'))

            <div class="alert {{ Session::get('class') }}" style="align-text:center;">
                <div style="display:flex;justify-content:flex-end;" class="close-flash">
                    &times;
                </div>
                {{ Session::get('message') }}
            </div>
        
        @endif 
        <div id="mainFrame">
            <div id="otherPlayers">

            </div>
            <div id="cashBox">

            </div>
            <div id="myInterface">
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
                            <input type="number" name="bet" placeholder="ваша ставка" class="form-control">
                            <button id="makeBet" class="btn btn-sm btn-success">Поставить</button>
                        </div>
                        
                        <div id="dangerZone">
                            <button id="save" class="btn btn-sm btn-warning">Пасс</button>
                            <button id="leaveGame" class="btn btn-sm btn-danger">Покинуть игру</button>
                        </div>
                    </div>
                    <div id="myCards">
    
                    </div>
                    <div id="myPoints">
    
                    </div>
                </div>
            </div>
        </div>







        <script src="{{ URL::asset('js/game.js') }}"></script>
        <script src="{{ URL::asset('js/jquery.js') }}"></script>
    </body>
</html>
