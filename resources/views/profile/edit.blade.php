
@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6"><div class="card">
            <div class="card-header">Minha Conta</div>
            <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>                    @endif

                    <form action="{{ route('profile.update') }}" method="POST">                        @csrf
                        @method('PUT')

                        <div class="mb-3"><label for="name" class="form-label">Nome</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name" value="{{ old('name', $user->name) }}">                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>                            @enderror
                        </div><div class="mb-3"><label for="email" class="form-label">E-mail</label><input type="email" name="email" class="form-control @error('email') is-invalid @enderror" id="email" value="{{ old('email', $user->email) }}">                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>                            @enderror
                        </div><button type="submit" class="btn btn-primary">Salvar Alterações</button></form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection