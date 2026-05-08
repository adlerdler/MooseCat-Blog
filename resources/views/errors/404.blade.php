@extends('errors.layout')

@section('title', '404 - Not Found')

@section('content')
    <error-page :error-code="404" />
@endsection
