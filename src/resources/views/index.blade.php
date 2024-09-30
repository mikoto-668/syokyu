@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('link')
<div class="heder__link">
  <nav>
  <ul class="header-nav">
  @if (Auth::check())
  <li class="header-nav__item">
  <form class="form" action="/" method="get">
  <button class="header-nav__button">ホーム</button>
  @csrf
  </form>
  </li>
  <li class="header-nav__item">
  <form class="form" action="/daily" method="get">
  <button class="header-nav__button">日付一覧</button>
  @csrf
  </form>
  </li>
  <li class="header-nav__item">
  <form class="form" action="/logout" method="post">
  @csrf
  <button class="header-nav__button">ログアウト</button>
  @csrf
  </form>
  </li>
  @endif
  </ul>
  </nav>
</div>
@endsection

@section('content')
<div class="welcome_message">
{{ Auth::user()->name }}<p>さんお疲れ様です!</p>
</div>
@if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif
@if (session('error'))
    <div class="alert alert-success">
        {{ session('error') }}
    </div>
@endif
<div class="test">あ</div>
<div class="button-container">
  <form class="timestamp" action="/timein" method="get">
    <button class="button1">勤務開始</button>
  </form>
  <form class="timestamp" action="/timeout" method="get">
    <button class="button2">勤務終了</button>
  </form>
  <form class="timestamp" action="/breakin" method="get">
    <button class="button3">休憩開始</button>
  </form>
  <form class="timestamp" action="/breakout" method="get">
    <button class="button4">休憩終了</button>
  </form>
</div>
@endsection