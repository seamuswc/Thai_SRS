@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    <li class="nav-item">
                        <a href="{{ route('flash', ['language' => 'thai']) }}">Thai Flashcards</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('flash', ['language' => 'chinese']) }}">Chinese Flashcards</a>
                    </li>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
