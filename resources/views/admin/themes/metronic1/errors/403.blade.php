<!doctype html>
<html class="no-js" lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>{{trans('admin.404')}}</title>
    <meta name="description" content="Saleh Riaz - UI/UX Engineer. Designer. Computer Scientist">
    <meta name="keywords"
          content="ui engineer, ux, saleh, riaz, qureshi, website, softwares, salehriaz, salehriazq, computer scientist, design, visual design, saleh riaz qureshi"/>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{asset(config('frontend_asset').'/errors_page/css/styles.css')}}">
    <script src="{{asset(config('frontend_asset').'/errors_page/js/jquery-3.3.1.min.js')}}"></script>

</head>
<style>

</style>
<body>
<div class="maincontainer">
    <div class="bat">
        <img class="wing leftwing"
             src="{{asset(config('frontend_asset').'/errors_page/image/bat-wing.png')}}">
        <img class="body"
             src="{{asset(config('frontend_asset').'/errors_page/image/bat-body.png')}}" alt="bat">
        <img class="wing rightwing"
             src="{{asset(config('frontend_asset').'/errors_page/image/bat-wing.png')}}">
    </div>
    <div class="bat">
        <img class="wing leftwing"
             src="{{asset(config('frontend_asset').'/errors_page/image/bat-wing.png')}}">
        <img class="body"
             src="{{asset(config('frontend_asset').'/errors_page/image/bat-body.png')}}" alt="bat">
        <img class="wing rightwing"
             src="{{asset(config('frontend_asset').'/errors_page/image/bat-wing.png')}}">
    </div>
    <div class="bat">
        <img class="wing leftwing"
             src="{{asset(config('frontend_asset').'/errors_page/image/bat-wing.png')}}">
        <img class="body"
             src="{{asset(config('frontend_asset').'/errors_page/image/bat-body.png')}}" alt="bat">
        <img class="wing rightwing"
             src="{{asset(config('frontend_asset').'/errors_page/image/bat-wing.png')}}">
    </div>
    <img class="foregroundimg" src="{{asset(config('frontend_asset').'/errors_page/image/house.png')}}" alt="haunted house">

</div>
<h1 class="errorcode">{{trans('admin.error')}}</h1>
<div class="errortext">{{trans('admin.no_role')}}</div>

</body>
</html>