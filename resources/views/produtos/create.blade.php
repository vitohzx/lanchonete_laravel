@extends('layouts.app')
@section('title', 'Nova Categoria')
@section('content')
    <h2 class="mb-3">Novo Produto</h2>
     <form action="{{ route('produtos.store') }}" method="POST">
        @csrf
        @include('produtos._form')
    
    </form>
@endsection