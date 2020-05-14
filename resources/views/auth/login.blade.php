<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Вход</title>
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
    <div class="container">
        <div class="row justify-content-center">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="row form-group">
                    <label for="email" class="col-md-4 col-form-label text-md-right">email</label>
                    <div class="col-md-6">
                        <input type="text" name="email">
                    </div>
                </div>
                <div class="row form-group">
                    <label for="email" class="col-md-4 col-form-label text-md-right">пароль</label>
                    <div class="col-md-6">
                        <input type="password" name="password">
                    </div>
                </div>
                <div class="form-group row mb-0">
                    <div class="col-md-8 offset-md-4">
                        <input type="submit" value="войти">
                    </div>
                </div>
                
            </form>
        </div>
    </div>
</body>
</html>