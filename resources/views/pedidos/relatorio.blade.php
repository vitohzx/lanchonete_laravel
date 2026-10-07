@extends('layouts.app')
 
@section('title', 'Relatório Diário')
 
@section('content')
 
{{-- Cabeçalho com Filtro --}}
<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div>
        <h3 class="fw-bold mb-1">Relatório Diário</h3>
        <span class="text-muted small">Data: {{ \Carbon\Carbon::parse($dataSelecionada)->format('d/m/Y') }}</span>
    </div>
 
    <form method="GET" action="{{ route('pedidos.relatorioDia') }}" class="d-flex gap-2">
        <input type="date" name="data" class="form-control form-control-sm" value="{{ $dataSelecionada }}" required>
        <button type="submit" class="btn btn-sm btn-dark">Filtrar</button>
    </form>
</div>
 
{{-- Cabeçalho de Impressão --}}
<div class="d-none d-print-block mb-4 text-center">
    <h3 class="fw-bold mb-1">Relatório Diário de Pedidos</h3>
    <p class="text-muted">Data: {{ \Carbon\Carbon::parse($dataSelecionada)->format('d/m/Y') }}</p>
    <hr>
</div>
 
{{-- Cards de Métricas Minimalistas --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="p-3 bg-light border rounded">
            <div class="text-muted small">Total Faturado</div>
            <div class="fs-4 fw-bold text-dark mt-1">R$ {{ number_format($totalFaturado, 2, ',', '.') }}</div>
        </div>
    </div>
 
    <div class="col-md-4">
        <div class="p-3 bg-light border rounded">
            <div class="text-muted small">Total de Pedidos</div>
            <div class="fs-4 fw-bold text-dark mt-1">{{ $pedidos->count() }}</div>
        </div>
    </div>
 
    <div class="col-md-4">
        <div class="p-3 bg-light border rounded">
            <div class="text-muted small">Ticket Médio</div>
            <div class="fs-4 fw-bold text-dark mt-1">R$ {{ number_format($ticketMedio, 2, ',', '.') }}</div>
        </div>
    </div>
</div>
 
{{-- Tabela Minimalista --}}
<div class="border rounded bg-white p-3">
    <h6 class="fw-bold mb-3">Pedidos do Dia</h6>
 
    @if($pedidos->isEmpty())
        <p class="text-muted small mb-0 py-2">Nenhum pedido registrado nesta data.</p>
    @else
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr class="text-muted border-bottom">
                        <th># ID</th>
                        <th>Horário</th>
                        <th>Status</th>
                        <th class="text-center">Itens</th>
                        <th class="text-end">Total</th>
                        <th class="text-end d-print-none"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pedidos as $pedido)
                        <tr>
                            <td class="fw-semibold">#{{ $pedido->id }}</td>
                            <td>{{ $pedido->created_at->format('H:i') }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ ucfirst($pedido->status) }}</span>
                            </td>
                            <td class="text-center">{{ $pedido->itens_count ?? $pedido->itens->sum('quantidade') }}</td>
                            <td class="text-end fw-semibold">R$ {{ number_format($pedido->total, 2, ',', '.') }}</td>
                            <td class="text-end d-print-none">
                                <a href="{{ route('pedidos.edit', $pedido) }}" class="btn btn-sm btn-link text-decoration-none p-0">Ver</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
 
<style>
    @media print {
        body { background: white !important; }
        .border { border: none !important; }
    }
</style>
 
@endsection