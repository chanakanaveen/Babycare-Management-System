@extends('back.layout.pages-layout')
@section('pagetitle', isset($pageTitle) ? $pageTitle : 'Notice')
@section('content')
<div class="col-md-12">
    <div class="container">
        @foreach ($notices as $notice)
            @if ($notice->target_group === 'all' || $notice->target_group === $user->group)
                <div class="alert alert-info">
                    <h4>{{ $notice->title }}</h4>
                    <p>{{ $notice->content }}</p>
                    <small>Expires at: {{ $notice->expires_at }}</small>
                </div>
            @endif
        @endforeach
    </div>
</div>
@endsection
