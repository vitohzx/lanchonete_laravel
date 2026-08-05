@php
    $isEdit = isset($produto);
@endphp
<div class="mb-3">
    @include("partials.alerts")
    <label for="nome" class="form-label">Nome</label>
    <input type="text" class="form-control @error('nome') is-invalid @enderror"
           id="nome" name="nome" value="{{ old('nome', $produto->nome ?? '') }}" maxlength="100">
    @error('nome')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="descricao" class="form-label">Descrição</label>
    <textarea class="form-control @error('descricao') is-invalid @enderror"
              id="descricao" name="descricao" rows="3" maxlength="500">{{ old('descricao', $produto->descricao ?? '') }}</textarea>
    @error('descricao')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="preco" class="form-label">Preço</label>
    <input class="form-control @error('descricao') is-invalid @enderror"
              id="preco" name="preco" rows="3" maxlength="500" value="{{ old('preco', $produto->preco ?? '') }}"> </input>
    @error('preco')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <select name="categoria_id" id="" >
        <option value="">Selecione uma categoria...</option>
        @foreach ($categorias as $categoria)
            <option value="{{ $categoria->id }}" {{ old('categoria_id', $produto->categoria_id ?? '') == $categoria->id ? 'selected' : '' }}>
                {{ $categoria->nome }} 
            </option>
        @endforeach
    </select>
</div>

<div class="form-check form-switch mb-3">
    <input class="form-check-input" type="checkbox" role="switch" id="ativa" name="ativa" value = "1"
           {{ old('ativa', $produto->ativa ?? true) ? 'checked' : '' }}>
    <label class="form-check-label" for="ativa">Ativa</label>
</div>

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Atualizar' : 'Salvar' }}</button>
    <a href="{{ route('produtos.index') }}" class="btn btn-secondary"> Cancelar </a>
</div>