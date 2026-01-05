<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', '勤怠管理システム')</title>
    <link rel="stylesheet" href="{{ asset('/css/reset.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('/css/common.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">
    @yield('css')
</head>
<body>

    @include('components.header')

    @yield('content')

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-bottom-right",
        }

        @if(Session::has('flashSuccess'))
            toastr.success("{{ session('flashSuccess') }}");
        @endif

        @if(Session::has('flashError'))
            toastr.error("{{ session('flashError') }}");
        @endif
    </script>
</body>
</html>