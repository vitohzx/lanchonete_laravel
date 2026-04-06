@extends('layouts.app')
@section('title', 'Nova Categoria')
@section('content')
    <h2 class="mb-3">Nova Categoria</h2>
    {{ route(
        @csrf
        @include('categorias._form')
    </form>
@endsection