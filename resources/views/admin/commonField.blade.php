
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
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css"
integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
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
                <div>
                    <i id="interfaceUp" class="fas fa-angle-up" style="color:#fff;font-size: 25px;"></i>
                    <i id="interfaceDown" class="fas fa-angle-down" style="color:#fff;font-size: 25px;"></i>
                </div>
                <div id="playerId" style="display:none;">{{ intval($user->id) }}</div>
                <div id="isAdmin" style="display:none;">{{ intval($user->admin) }}</div>
                <button id="connect" class="btn btn-sm btn-primary" style="display: none;">Подключиться</button>
                <div class="options">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="btn nav-item-inactive" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true" style="color:yellow;border:1px solid #fff;margin:2px 0 2px 2px;">
                                Баланс
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn nav-item-inactive" id="contact-tab" data-toggle="tab" href="#generatePassword" role="tab" aria-controls="contact" aria-selected="false" style="color:#fff;border:1px solid #fff;margin:2px 0 2px 2px;">
                                Пароль
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn nav-item-inactive" id="reviews-tab"data-toggle="tab"href="#reviews"role="tab"aria-controls="reviews"aria-selected="false" style="color:#fff;border:1px solid #fff;margin:2px 0 2px 2px;">
                                Сервер
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn nav-item-inactive" id="stepFor-tab" data-toggle="tab" href="#stepFor"role="tab"aria-controls="reviews"aria-selected="false" style="color:#fff;border:1px solid #fff;margin:2px 0 2px 2px;">
                                Сходить за ...
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn nav-item-inactive" id="moneyOutput-tab" data-toggle="tab" href="#moneyOutput"role="tab"aria-controls="reviews"aria-selected="false" style="color:#fff;border:1px solid #fff;margin:2px 0 2px 2px;">
                                Вывод фантиков
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
                                        <button class="chargeNewBalance" class="btn btn-sm btn-success">в игре</button>
                                        <input type="hidden" value="{{ $user->id }}" class="userId">
                                        <button class="chargeNewBalanceOutGame" class="btn btn-sm btn-success">вне игры</button>
                                    </td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                        <div class="tab-pane fade" id="generatePassword" role="tabpanel" aria-labelledby="contact-tab">
                            <button id="passwordGenerate" class="btn btn-sm btn-danger">Сгенерировать пароль</button>
                            <div id="passwordArea">
                                <div id="password"></div>
                                <button id="copyToClipBoard" onclick="CopyToClipboard('password')" class="btn btn-sm btn-warning">Копировать</button>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                            <div class="reviews-show">
                                <button class="btn btn-sm btn-success" id="runServer">Запустить сервер</button>
                                <button class="btn btn-sm btn-danger" id="stopServer">Остановить сервер</button>
                            </div>
                        </div> 
                        <div class="tab-pane fade" id="stepFor" role="tabpanel" aria-labelledby="stepFor-tab">
                            <table style="border-spacing: 10px;">
                                @foreach ($allUsers as $user)
                                <tr>
                                    <td class="playerName">{{ $user->name }}</td>
                                    <td class="playerBalance">{{ $user->balance }}</td>
                                    <td>
                                        <button class="stepInsteadUser" class="btn btn-sm btn-success">пасс</button>
                                        <input type="hidden" value="{{ $user->id }}" class="userId">
                                    </td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                        <div class="tab-pane fade" id="moneyOutput" role="tabpanel" aria-labelledby="home-tab">
                            <table>
                                @foreach ($allUsers as $user)
                                <tr>
                                    <td class="playerName">{{ $user->name }}</td>
                                    <td class="playerBalance">{{ $user->balance }}</td>
                                    <td>
                                        <input type="text" class="" ownerName="{{ $user->name }}">
                                        <button class="" class="btn btn-sm btn-success">начислить</button>
                                        <input type="hidden" value="{{ $user->id }}" class="userId">
                                    </td>
                                </tr>
                                @endforeach
                            </table>
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
            $('.newBalance').each(function () {
                if (!$(this).val()) {
                    $(this).next().attr('disabled', true);
                    $(this).next().next().next().attr('disabled', true);
                }
            });

            $('.newBalance').on('input', function () {
                if (Number.isInteger(+$(this).val()) && $(this).val()) {
                    $(this).next().removeAttr('disabled');
                    $(this).next().next().next().removeAttr('disabled');
                } else {
                    $(this).next().attr('disabled', true);
                    $(this).next().next().next().attr('disabled', true);
                }
            });
            const initialInterfaceHeight = $('#myInterface').height();

            $('#interfaceUp').on('click', function () {
                interfaceUp();
                $('#myTabContent').height($(window).height() / 100 * 75);
                //$('#myInterface').height($(window).height() * 2);
                $('#myInterface').css({'z-index': '5'});
                $(this).hide();
                $('#interfaceDown').show();
            });

            $('#interfaceDown').on('click', function () {
                interfaceDown();
                $(this).hide();
                $('#interfaceUp').show();
            });

            $('.nav-item').on('click', function () {
                $('#myTabContent').height($('#myInterface').height() / 100 * 75);
            });

            const interfaceUp = () => {
                let h = 10;
                const interfaceUping = setInterval(() => {
                    $('#myInterface').height(h);
                    h += 10;
                    if (h >= $(window).height() * 2) {
                        stopInterfaceUping();
                    }
                }, 10);
                const stopInterfaceUping = () => {
                    clearInterval(interfaceUping);
                };
            };

            const interfaceDown = () => {
                let h = $(window).height() * 2;
                const interfaceDowning = setInterval(() => {
                    $('#myInterface').height(h);
                    h -= 10;
                    if (h <= initialInterfaceHeight) {
                        stopInterfaceDowning();
                    }
                }, 10);
                const stopInterfaceDowning = () => {
                    clearInterval(interfaceDowning);
                };
            };

            const CopyToClipboard = (containerid) => {
                if (document.selection) { 
                    const range = document.body.createTextRange();
                    range.moveToElementText(document.getElementById(containerid));
                    range.select().createTextRange();
                    document.execCommand("Copy"); 

                } else if (window.getSelection) {
                    const range = document.createRange();
                    range.selectNode(document.getElementById(containerid));
                    window.getSelection().addRange(range);
                    document.execCommand("Copy");
                }
            };

            $(function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $('.chargeNewBalanceOutGame').on('click', function (e) {
                    e.preventDefault();
                    
                    $.ajax({
                        data: {
                            playerId: $(this).prev().val(),
                            newBalance: $(this).prev().prev().prev().val()
                        },
                        url: "{{ route('chargeBalanceOutGame') }}",
                        type: "POST",
                        dataType: 'json',
                        success: function (data) {
                            console.log(data);
                            $('.playerName').each(function () {
                               if ($(this).text() === data.name) {
                                   $(this).next().text(data.balance);
                               } 
                            });
                        },
                        error: function (error) {
                            console.log(error);
                        }
                    });

                    $(this).prev().prev().prev().val('');
                    $(this).attr('disabled', true);
                });

                $('#passwordGenerate').on('click', function(e) {
                    e.preventDefault();
                    
                    $.ajax({
                        data: {},
                        url: "{{ route('passwordGenerate') }}",
                        type: "GET",
                        dataType: 'json',
                        success: function (data) {
                            $('#password').text(error.responseText);
                        },
                        error: function (error) {
                            $('#copyToClipBoard').show();
                            $('#password').text(error.responseText);
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

                    setTimeout(() => {
                        $('#connect').click();
                    }, 2000);
                });
            });
        </script>
    </body>
</html>
