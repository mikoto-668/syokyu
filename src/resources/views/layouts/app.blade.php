<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Atte</title>
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
  <link rel="stylesheet" href="{{ asset('css/common.css') }}">
  @yield('css')
</head>

<body>
  <div class="app">
    <header class="header">
      <p class="header__logo" href="/">Atte</p>
      @yield('link')
    </header>

    <div class="content">
      @yield('content')
    </div>

    <footer class="footer">
      <p class="footer__logo">Atte,inc</p>
    </footer>
  </div>
</body>

</html>

