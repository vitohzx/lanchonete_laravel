<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PedidoController extends Controller
{
    /**
     * Lista todos os pedidos evitando o problema de N+1.
     */
    public function index()
    {
        $pedidos = Pedido::with(['user', 'itens.produto'])
            ->orderByDesc('id')
            ->paginate(10);

        return view('pedidos.index', compact('pedidos'));
    }

    /**
     * Exibe o formulário de criação de pedido.
     */
    public function create()
    {
        return view('pedidos.create');
    }

    /**
     * Cria um novo pedido com status inicial 'aberto'.
     */
    public function store(Request $request)
    {
        $pedido = Pedido::create([
            'user_id' => auth()->id(),
            'status' => 'aberto',
            'total' => 0,
            'observacoes' => $request->input('observacoes'),
        ]);

        return redirect()->route('pedidos.edit', $pedido)
            ->with('sucesso', 'Pedido iniciado! Agora adicione itens.');
    }

    /**
     * Exibe os detalhes de um pedido.
     */
    public function show(Pedido $pedido)
    {
        $pedido->load('itens.produto', 'user');

        return view('pedidos.show', compact('pedido'));
    }

    /**
     * Exibe a tela de edição do pedido (se não estiver fechado).
     */
    public function edit(Pedido $pedido)
    {
        // Regra: pedidos "fechados" não podem ser editados
        if ($pedido->status === 'fechado') {
            return redirect()->route('pedidos.index')
                ->with('erro', 'Pedidos fechados não podem ser editados.');
        }

        $pedido->load('itens.produto');
        $produtos = \App\Models\Produto::orderBy('nome')->get();

        return view('pedidos.edit', compact('pedido', 'produtos'));
    }

    /**
     * Atualiza o status do pedido (ex.: Enviar para preparo, Pronto, Entregue, Fechado).
     */
    public function atualizarStatus(Request $request, Pedido $pedido)
    {
        // Bloqueia alterações se o pedido já estiver fechado
        if ($pedido->status === 'fechado') {
            return redirect()->back()
                ->with('erro', 'Este pedido está fechado e não pode ter seu status alterado.');
        }

        $request->validate([
            'status' => 'required|in:aberto,em preparo,pronto,entregue,fechado',
        ]);

        $pedido->update([
            'status' => $request->input('status'),
        ]);

        return redirect()->back()
            ->with('sucesso', 'Status do pedido atualizado com sucesso!');
    }

    /**
     * Exclui um pedido.
     */
    public function destroy(Pedido $pedido)
    {
        if ($pedido->status === 'fechado') {
            return redirect()->route('pedidos.index')
                ->with('erro', 'Pedidos fechados não podem ser excluídos.');
        }

        $pedido->delete();

        return redirect()->route('pedidos.index')
            ->with('sucesso', 'Pedido excluído com sucesso!');
    }
    public function relatorioDia(Request $request)
    {
        // 1. Pega a data vinda do filtro ou usa a data de hoje por padrão
        $dataSelecionada = $request->input('data', now()->format('Y-m-d'));

        // 2. Busca os pedidos filtrados por essa data
        $pedidos = Pedido::with('itens')
            ->whereDate('created_at', $dataSelecionada)
            ->latest()
            ->get();

        // 3. Calcula os totais do dia
        $totalFaturado = $pedidos->sum('total');
        $totalPedidos = $pedidos->count();
        $ticketMedio = $totalPedidos > 0 ? ($totalFaturado / $totalPedidos) : 0;

        // 4. Retorna a view PASSANDO a $dataSelecionada no compact
        return view('pedidos.relatorio', compact(
            'pedidos',
            'dataSelecionada',
            'totalFaturado',
            'ticketMedio'
        ));
    }
}
