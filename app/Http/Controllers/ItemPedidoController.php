<?php
 
namespace App\Http\Controllers;
 
use App\Models\Pedido;
use App\Models\ItemPedido;
use App\Models\Produto;
use Illuminate\Http\Request;
 
class ItemPedidoController extends Controller
{
    public function storeJson(Request $request, Pedido $pedido)
    {
        $dados = $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'quantidade' => 'required|integer|min:1|max:99',
        ]);
 
        $produto = Produto::findOrFail($dados['produto_id']);
        $preco = $produto->preco;
 
        $item = ItemPedido::where('pedido_id', $pedido->id)
            ->where('produto_id', $produto->id)
            ->first();
 
        if ($item) {
            $item->quantidade += $dados['quantidade'];
            $item->preco_unitario = $preco;
            $item->subtotal = $item->quantidade * $preco;
            $item->save();
        } else {
            $item = ItemPedido::create([
                'pedido_id' => $pedido->id,
                'produto_id' => $produto->id,
                'quantidade' => $dados['quantidade'],
                'preco_unitario' => $preco,
                'subtotal' => $dados['quantidade'] * $preco,
            ]);
        }
 
        $pedido->total = ItemPedido::where('pedido_id', $pedido->id)->sum('subtotal');
        $pedido->save();
 
        $item->load('produto');
 
        return response()->json([
            'message' => 'Item adicionado!',
            'pedido' => [
                'id' => $pedido->id,
                'total' => (float) $pedido->total,
            ],
            'item' => [
                'id' => $item->id,
                'produto' => [
                    'id' => $item->produto->id,
                    'nome' => $item->produto->nome,
                ],
                'quantidade' => (int) $item->quantidade,
                'preco_unitario' => (float) $item->preco_unitario,
                'subtotal' => (float) $item->subtotal,
            ]
        ], 200);
    }
 
    public function decreaseJson(Pedido $pedido, ItemPedido $itemPedido)
    {
        abort_unless($itemPedido->pedido_id === $pedido->id, 404);
 
        if ($itemPedido->quantidade > 1) {
            $itemPedido->quantidade -= 1;
            $itemPedido->subtotal = $itemPedido->quantidade * $itemPedido->preco_unitario;
            $itemPedido->save();
            $itemPedido->load('produto');
 
            $pedido->total = ItemPedido::where('pedido_id', $pedido->id)->sum('subtotal');
            $pedido->save();
 
            return response()->json([
                'message' => 'Quantidade diminuída!',
                'pedido' => [
                    'id' => $pedido->id,
                    'total' => (float) $pedido->total,
                ],
                'item' => [
                    'id' => $itemPedido->id,
                    'produto' => [
                        'id' => $itemPedido->produto->id,
                        'nome' => $itemPedido->produto->nome,
                    ],
                    'quantidade' => (int) $itemPedido->quantidade,
                    'preco_unitario' => (float) $itemPedido->preco_unitario,
                    'subtotal' => (float) $itemPedido->subtotal,
                ]
            ], 200);
        } else {
            // Se for 1, remove o item por completo automaticamente
            $itemPedido->delete();
 
            $pedido->total = ItemPedido::where('pedido_id', $pedido->id)->sum('subtotal');
            $pedido->save();
 
            return response()->json([
                'message' => 'Item removido!',
                'pedido' => [
                    'id' => $pedido->id,
                    'total' => (float) $pedido->total,
                ],
                'removed_item_id' => (int) $itemPedido->id,
            ], 200);
        }
    }
 
     public function increaseJson(Pedido $pedido, ItemPedido $itemPedido)
    {
        abort_unless($itemPedido->pedido_id === $pedido->id, 404);
 
        if ($itemPedido->quantidade >= 0) {
            $itemPedido->quantidade += 1;
            $itemPedido->subtotal = $itemPedido->quantidade * $itemPedido->preco_unitario;
            $itemPedido->save();
            $itemPedido->load('produto');
 
            $pedido->total = ItemPedido::where('pedido_id', $pedido->id)->sum('subtotal');
            $pedido->save();
 
            return response()->json([
                'message' => 'Quantidade aumentada!',
                'pedido' => [
                    'id' => $pedido->id,
                    'total' => (float) $pedido->total,
                ],
                'item' => [
                    'id' => $itemPedido->id,
                    'produto' => [
                        'id' => $itemPedido->produto->id,
                        'nome' => $itemPedido->produto->nome,
                    ],
                    'quantidade' => (int) $itemPedido->quantidade,
                    'preco_unitario' => (float) $itemPedido->preco_unitario,
                    'subtotal' => (float) $itemPedido->subtotal,
                ]
            ], 200);
        } else {
             
        }
    }
 
    public function destroyJson(Pedido $pedido, ItemPedido $itemPedido)
    {
        abort_unless($itemPedido->pedido_id === $pedido->id, 404);
 
        $itemPedido->delete();
 
        $pedido->total = ItemPedido::where('pedido_id', $pedido->id)->sum('subtotal');
        $pedido->save();
 
        return response()->json([
            'message' => 'Item removido!',
            'pedido' => [
                'id' => $pedido->id,
                'total' => (float) $pedido->total,
            ],
            'removed_item_id' => (int) $itemPedido->id,
        ], 200);
    }
}