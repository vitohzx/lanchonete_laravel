@extends('layouts.app')
 
@section('title', 'Editar Pedido')
 
@section('content')
 
@include('partials.alerts')
 
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Pedido #{{ $pedido->id }}</h2>
 
    <a class="btn btn-outline-secondary"
        href="{{ route('pedidos.index') }}">
        Voltar
    </a>
</div>
 
<div class="row g-3">
 
    {{-- COLUNA DA ESQUERDA: ADICIONAR ITEM --}}
    <div class="col-lg-5">
        <div class="card">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Adicionar Item</h5>
                
                <form id="formAddItem">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Produto</label>
                        <select name="produto_id" class="form-select" required>
                            <option value="">Selecione...</option>
                            @foreach($produtos as $prod)
                                <option value="{{ $prod->id }}">{{ $prod->nome }} (R$ {{ number_format($prod->preco,2,',','.') }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantidade</label>
                        <input type="number" name="quantidade" class="form-control" value="1" min="1" max="99" required>
                    </div>
                    
                    <button class="btn btn-primary w-100" type="submit" id="btnSubmitAdd">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="spinnerAdd"></span>
                        <span id="btnTextAdd">Adicionar"</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
 
    {{-- COLUNA DA DIREITA: ITENS DO PEDIDO --}}
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body">
 
                <h5 class="fw-bold mb-3">
                    Itens do pedido
                </h5>
 
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th class="text-end">Qtd</th>
                                <th class="text-end">Unit.</th>
                                <th class="text-end">Subtotal</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="itensBody">
                          @foreach($pedido->itens as $item)
                            <tr id="item-{{ $item->id }}">
                              <td>{{ $item->produto->nome }}</td>
                              <td class="text-end">{{ $item->quantidade }}</td>
                              <td class="text-end">R$ {{ number_format($item->preco_unitario,2,',','.') }}</td>
                              <td class="text-end">R$ {{ number_format($item->subtotal,2,',','.') }}</td>
                              <td class="text-end">
                                <button class="btn btn-sm btn-outline-warning" data-increase="{{ $item->id }}" title="aumentar quantidade">+</button>
                                <button class="btn btn-sm btn-outline-warning" data-decrease="{{ $item->id }}" title="Diminuir quantidade">-</button>
                                <button class="btn btn-sm btn-outline-danger" data-remove="{{ $item->id }}" title="Remover item">Remover</button>
                              </td>
                            </tr>
                          @endforeach
                        </tbody>
                    </table>
                </div>
 
                {{-- TOTAL --}}
                <div class="border-top mt-3 pt-3 text-end">
                    <span class="fw-bold fs-5">
                        Total:
                        <span id="pedidoTotal">R$ {{ number_format($pedido->total, 2, ',', '.') }}</span>
                    </span>
                </div>
 
            </div>
        </div>
    </div>
 
</div>
 
<script>
  window.PW3 = {
    pedidoId: "{{ $pedido->id }}",
    urlAdd: "{{ route('pedidos.itens.storeJson', $pedido) }}",
    urlDelBase: "{{ url('pedidos/'.$pedido->id.'/itens-json') }}"
  };
 
  function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  }
 
  function moneyBR(value) {
    return (value ?? 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
  }
 
  function showToast(title, body) {
    const el = document.getElementById('pw3Toast');
    if (!el) return;
    document.getElementById('pw3ToastTitle').textContent = title;
    document.getElementById('pw3ToastBody').textContent = body;
    const toast = bootstrap.Toast.getOrCreateInstance(el, { delay: 2500 });
    toast.show();
  }
 
  function upsertRow(item) {
    const tbody = document.getElementById('itensBody');
    let row = document.getElementById('item-' + item.id);
 
    const html = `
      <tr id="item-${item.id}">
        <td>${item.produto.nome}</td>
        <td class="text-end">${item.quantidade}</td>
        <td class="text-end">${moneyBR(item.preco_unitario)}</td>
        <td class="text-end">${moneyBR(item.subtotal)}</td>
        <td class="text-end">
        <button class="btn btn-sm btn-outline-warning" data-increase="${item.id}" title="Aumentar quantidade">+</button>
          <button class="btn btn-sm btn-outline-warning" data-decrease="${item.id}" title="Diminuir quantidade">-</button>
          <button class="btn btn-sm btn-outline-danger" data-remove="${item.id}" title="Remover item">Remover</button>
        </td>
      </tr>`;
 
    if (row) {
      row.outerHTML = html;
    } else {
      tbody.insertAdjacentHTML('beforeend', html);
    }
  }
 
  function removeRow(itemId) {
    const row = document.getElementById('item-' + itemId);
    if (row) row.remove();
  }
 
  function setTotal(total) {
    document.getElementById('pedidoTotal').textContent = moneyBR(total);
  }
 
  // Evento de Adicionar Item
  document.getElementById('formAddItem').addEventListener('submit', async (e) => {
    e.preventDefault();
 
    const form = e.currentTarget;
    const btnSubmit = document.getElementById('btnSubmitAdd');
    const spinner = document.getElementById('spinnerAdd');
    const btnText = document.getElementById('btnTextAdd');
    const fd = new FormData(form);
 
    btnSubmit.disabled = true;
    spinner.classList.remove('d-none');
    btnText.textContent = 'Adicionando...';
 
    try {
      const resp = await fetch(window.PW3.urlAdd, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrfToken(),
          'Accept': 'application/json'
        },
        body: fd
      });
 
      const data = await resp.json();
 
      if (!resp.ok) {
        showToast('Erro', data.message || 'Erro ao adicionar item.');
        return;
      }
 
      upsertRow(data.item);
      setTotal(data.pedido.total);
      showToast('Sucesso', data.message);
 
      form.quantidade.value = 1;
      form.produto_id.value = '';
 
    } catch (err) {
      console.error(err);
      showToast('Erro', 'Falha de conexão.');
    } finally {
      btnSubmit.disabled = false;
      spinner.classList.add('d-none');
      btnText.textContent = 'Adicionar (AJAX)';
    }
  });
 
  // Eventos na Tabela (Diminuir ou Remover)
  document.getElementById('itensBody').addEventListener('click', async (e) => {
    const btnIncrease = e.target.closest('[data-increase]');
    const btnDecrease = e.target.closest('[data-decrease]');
    const btnRemove = e.target.closest('[data-remove]');
 
    if (btnDecrease) {
      const itemId = btnDecrease.getAttribute('data-decrease');
 
      try {
        const resp = await fetch(`${window.PW3.urlDelBase}/${itemId}/decrease`, {
          method: 'PATCH',
          headers: {
            'X-CSRF-TOKEN': csrfToken(),
            'Accept': 'application/json'
          }
        });
 
        const data = await resp.json();
 
        if (!resp.ok) {
          showToast('Erro', data.message || 'Erro ao diminuir.');
          return;
        }
 
        if (data.item) {
          upsertRow(data.item);
        } else if (data.removed_item_id) {
          removeRow(data.removed_item_id);
        }
 
        setTotal(data.pedido.total);
        showToast('Sucesso', data.message);
 
      } catch (err) {
        console.error(err);
        showToast('Erro', 'Falha de conexão.');
      }
    }
 
    if (btnIncrease) {
      const itemId = btnIncrease.getAttribute('data-increase');
 
      try {
        const resp = await fetch(`${window.PW3.urlDelBase}/${itemId}/increase`, {
          method: 'PATCH',
          headers: {
            'X-CSRF-TOKEN': csrfToken(),
            'Accept': 'application/json'
          }
        });
 
        const data = await resp.json();
 
        if (!resp.ok) {
          showToast('Erro', data.message || 'Erro ao aumentar.');
          return;
        }
 
        if (data.item) {
          upsertRow(data.item);
        } else if (data.removed_item_id) {
          removeRow(data.removed_item_id);
        }
 
        setTotal(data.pedido.total);
        showToast('Sucesso', data.message);
 
      } catch (err) {
        console.error(err);
        showToast('Erro', 'Falha de conexão.');
      }
    }
 
    if (btnRemove) {
      const itemId = btnRemove.getAttribute('data-remove');
      if (!confirm('Deseja realmente remover este item do pedido?')) return;
 
      try {
        const resp = await fetch(`${window.PW3.urlDelBase}/${itemId}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': csrfToken(),
            'Accept': 'application/json'
          }
        });
 
        const data = await resp.json();
 
        if (!resp.ok) {
          showToast('Erro', data.message || 'Erro ao remover.');
          return;
        }
 
        removeRow(data.removed_item_id);
        setTotal(data.pedido.total);
        showToast('Sucesso', data.message);
 
      } catch (err) {
        console.error(err);
        showToast('Erro', 'Falha de conexão.');
      }
    }
  });
</script>
 
@endsection