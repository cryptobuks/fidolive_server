<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1,user-scalable=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Index</title>
    </head>
    <body>
        <div id="app">
            <app></app>
        </div>
        <script src="/js/manifest.js"></script>
        <script src="/js/vendor.js"></script>
        <script src="{{ mix('js/app.js') }}"></script>
    </body>
</html>
