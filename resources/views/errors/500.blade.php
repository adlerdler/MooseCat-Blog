@extends('errors.layout')

@section('title', '500 - Server Error')

@section('content')
    <error-page :error-code="500" />
@endsection
