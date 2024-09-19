@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/daily.css') }}">
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
<h1 class="main-title">Daily</h1>
    <table class="admin__table">
      <tr class="admin__row">
        <th class="admin__label">お名前</th>
        <th class="admin__label">勤務開始</th>
        <th class="admin__label">勤務終了</th>
        <th class="admin__label">休憩時間</th>
        <th class="admin__label">勤務時間</th>
        <th class="admin__label"></th>
      </tr>
      @foreach($times as $time)
      <tr class="admin__row">
      <td class="admin__data">{{$time->user->name}}</td>
        <td class="admin__data">{{\Carbon\Carbon::parse($time->punchIn)->format('H:i:s') }}</td>
        <td class="admin__data">{{\Carbon\Carbon::parse($time->punchOut)->format('H:i:s')}}</td>
        <td class="admin__data">{{$time->worktime}}</td>
        <td class="admin__data">{{$time->breaktotal}}</td>
      </tr>
      @endforeach
    </table>
  </div>
</div>

</div>
@endsection