@extends('layouts.app')
@section('title', 'Editar Produto')
@section('content')
    <h2 class="mb-3">Editar Produto</h2>
    <form action="{{ route('produtos.update', $produto) }}" method="POST">
        @csrf
        @method('PUT')
        @include('produtos._form', ["produto" => $produto])
    </form>
@endsection