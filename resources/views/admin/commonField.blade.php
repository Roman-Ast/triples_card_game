
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="Cache-control" content="no-cache">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="-1">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <title>Админ-панель</title>
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
        
        <div id="mainFrame">
            <div id="room">
                
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
                <div class="options">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="btn nav-item-inactive" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true" >
                                Баланс
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn nav-item-inactive" id="contact-tab" data-toggle="tab" href="#generatePassword" role="tab" aria-controls="contact" aria-selected="false">
                                Пароль
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn nav-item-inactive" id="reviews-tab"data-toggle="tab"href="#reviews"role="tab"aria-controls="reviews"aria-selected="false">
                                Ещё
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                            <table>
                                @foreach ($allUsers as $user)
                                <tr>
                                    <td class="playerName">{{ $user->name }}</td>
                                    <td class="playerBalance">{{ $user->balance }}</td>
                                    <td>
                                        <input type="text" class="newBalance" ownerName="{{ $user->name }}">
                                        <button class="chargeNewBalance" class="btn btn-sm btn-success">начислить</button>
                                        <input type="hidden" value="{{ $user->id }}" class="userId">
                                    </td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                        <div class="tab-pane fade" id="generatePassword" role="tabpanel" aria-labelledby="contact-tab">
                            <button id="passwordGenerate" class="btn btn-sm btn-danger">Сгенерировать пароль</button>
                            <div id="passwordArea">
                                <div id="password"></div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                            <div class="reviews-show">
                                <button class="btn btn-sm btn-success" id="runServer">Запустить сервер</button>
                                <button class="btn btn-sm btn-danger" id="stopServer">Остановить сервер</button>
                            </div>
                        </div> 
                    </div>
                </div>
            </div>
        </div>



        <script src="{{ URL::asset('js/copyToClipBoard.js') }}" type="module"></script>
        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
        <script src="{{ URL::asset('js/jquery_min.js') }}"></script>
        <script src="{{ URL::asset('js/game.js') }}" type="module"></script>
        <script src="{{ URL::asset('js/gameEventsHandlers/onRoundStart.js') }}" async type="module"></script>
        <script src="{{ URL::asset('js/gameEventsHandlers/onCheckConnection.js') }}" type="module"></script>
        <script src="{{ URL::asset('js/gameEventsHandlers/onBalanceError.js') }}" type="module"></script>
        <script src="{{ URL::asset('js/gameEventsHandlers/onNoneNotWinnersAgreedToCook.js') }}" type="module"></script>
        <script src="{{ URL::asset('js/gameEventsHandlers/onAllWinnersAgreeToCook.js') }}" type="module"></script>
        <script src="{{ URL::asset('js/gameEventsHandlers/onSomeNotWinnersAgreeToCook.js') }}" type="module"></script>
        <script type="text/javascript">
            $(function () {
            
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $('#passwordGenerate').on('click', function(e) {
                    e.preventDefault();
                    
                    $.ajax({
                        data: {},
                        url: "{{ route('passwordGenerate') }}",
                        type: "GET",
                        dataType: 'json',
                        success: function (data) {
                            console.log(data);
                            $('#password').text(error.responseText);
                        },
                        error: function (error) {
                            console.log(error);
                            $('#password').text(error.responseText);
                        }
                    });
                });
                
                $('.chargeNewBalance').on('click', function(e) {
                    e.preventDefault();
                    
                    const data = {
                        name: $(this).prev().attr('ownerName'),
                        newBalance: $(this).prev().val(),
                        id: $(this).next().val()
                    };
                    $(this).prev().val('');
                    $.ajax({
                        data: data,
                        url: "{{ route('chargeBalance') }}",
                        type: "POST",
                        dataType: 'json',
                        success: function (data) {
                            console.log(data);
                            $('.playerName').each(function () {
                               if ($(this).text() === data.playerName) {
                                   $(this).next().text(data.newBalance);
                               } 
                            });
                        },
                        error: function (error) {
                            console.log(error);
                        }
                    });
                });

                $('#runServer').on('click', function () {
                    $.ajax({
                        data: {},
                        url: "{{ route('runServer') }}",
                        type: "GET",
                        dataType: 'json',
                        success: function (data) {
                            console.log(data);
                        },
                        error: function (error) {
                            console.log(error);
                        }
                    });
                });

                $('#stopServer').on('click', function () {
                    $.ajax({
                        data: {},
                        url: "{{ route('stopServer') }}",
                        type: "GET",
                        dataType: 'json',
                        success: function (data) {
                            console.log(data);
                        },
                        error: function (error) {
                            console.log(error);
                        }
                    });
                });
            });
        </script>
    </body>
</html>
