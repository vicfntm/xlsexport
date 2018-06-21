<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Страница работы с файлами</title>
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
</head>
<body>
<div id="app" class="container">
    <div class="row">
        <div class="col-md-8">
            <h1>Страница ввода и редактирования файлов</h1>
            <hr>
        </div>
    </div>
    <form-container></form-container>
</div>
<script src="{{asset('js/app.js')}}"></script>
</body>
</html>
