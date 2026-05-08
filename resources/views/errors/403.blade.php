@extends('errors.layout')

@section('title', '403 - Forbidden')

@section('content')
    <error-page :error-code="403" />
@endsection
