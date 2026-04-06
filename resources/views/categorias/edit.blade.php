@extends('layouts.app')
@section('title', 'Editar Categoria')
@section('content')
    <h2 class="mb-3">Editar Categoria</h2>
    {{ route(
        @csrf
        @method('PUT')
        @include('categorias._form', ['categoria' => $categoria])
    </form>
@endsection