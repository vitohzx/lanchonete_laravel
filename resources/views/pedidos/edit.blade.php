@extends('layouts.app')
 
@section('title', 'Editar Pedido')
 
@section('content')
 
@include('partials.alerts')
 
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2>Pedido #{{ $pedido->id }}</h2>
        <span class="badge bg-info text-dark fs-6">{{ ucfirst($pedido->status) }}</span>
    </div>
 
    <div class="d-flex align-items-center gap-2">
        @if($pedido->status !== 'fechado')
            <form action="{{ route('pedidos.atualizarStatus', $pedido) }}" method="POST" class="d-inline">
                @csrf
                @method('PATCH')
                @if($pedido->status === 'aberto')
                    <input type="hidden" name="status" value="em preparo">
                    <button class="btn btn-warning" type="submit">Enviar para Preparo</button>
                @elseif($pedido->status === 'em preparo')
                    <input type="hidden" name="status" value="pronto">
                    <button class="btn btn-info" type="submit">Marcar como Pronto</button>
                @elseif($pedido->status === 'pronto')
                    <input type="hidden" name="status" value="entregue">
                    <button class="btn btn-primary" type="submit">Marcar como Entregue</button>
                @elseif($pedido->status === 'entregue')
                    <input type="hidden" name="status" value="fechado">
                    <button class="btn btn-success" type="submit">Fechar Pedido</button>
                @endif
            </form>
        @endif
 
        <a class="btn btn-outline-secondary" href="{{ route('pedidos.index') }}">
            Voltar
        </a>
    </div>
</div>
 
<div class="row g-3">
 
    <div class="col-lg-5">
        <div class="card">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Adicionar Item</h5>
                
                @if($pedido->status === 'fechado')
                    <div class="alert alert-warning mb-0">
                        Este pedido está <strong>fechado</strong> e não pode receber novos itens.
                    </div>
                @else
                    <form id="formAddItem">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Produto</label>
                            <select name="produto_id" class="form-select" required>
                                <option value="">Selecione...</option>
                                @foreach($produtos as $prod)
                                    <option value="{{ $prod->id }}">{{ $prod->nome }} (R${{ number_format($prod->preco, 2, ',', '.') }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Quantidade</label>
                            <input type="number" name="quantidade" class="form-control" value="1" min="1" max="99" required>
                        </div>
                        
                        <button class="btn btn-primary w-100" type="submit" id="btnSubmitAdd">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="spinnerAdd"></span>
                            <span id="btnTextAdd">Adicionar (AJAX)</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
 
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
                                @if($pedido->status !== 'fechado')
                                    <th class="text-end">Ações</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody id="itensBody">
                          @foreach($pedido->itens as $item)
                            <tr id="item-{{ $item->id }}">
                              <td>{{ $item->produto->nome }}</td>
                              <td class="text-end item-qtd">{{ $item->quantidade }}</td>
                              <td class="text-end">R${{ number_format($item->preco_unitario, 2, ',', '.') }}</td>
                              <td class="text-end item-subtotal">R${{ number_format($item->subtotal, 2, ',', '.') }}</td>
                              @if($pedido->status !== 'fechado')
                                  <td class="text-end">
                                    <button class="btn btn-sm btn-outline-warning" data-increase="{{ $item->id }}" title="Aumentar quantidade">+</button>
                                    <button class="btn btn-sm btn-outline-warning" data-decrease="{{ $item->id }}" title="Diminuir quantidade">-</button>
                                    <button class="btn btn-sm btn-outline-danger" data-remove="{{ $item->id }}" title="Remover item">Remover</button>
                                  </td>
                              @endif
                            </tr>
                          @endforeach
                        </tbody>
                    </table>
                </div>
 
                <div class="border-top mt-3 pt-3 text-end">
                    <span class="fw-bold fs-5">
                        Total:
                        <span id="pedidoTotal">R${{ number_format($pedido->total, 2, ',', '.') }}</span>
                    </span>
                </div>
 
            </div>
        </div>
    </div>
 
</div>
 
<div class="card mt-3">
  <div class="card-body">
    <h5 class="fw-bold">Entrega</h5>
 
    <div class="row g-2">
      <div class="col-md-4">
        <label class="form-label">CEP</label>
        <input id="cep" class="form-control" placeholder="00000-000">
        <div class="form-text">Digite o CEP e clique em Buscar.</div>
      </div>
      <div class="col-md-2 d-flex align-items-end">
        <button id="btnBuscarCep" class="btn btn-outline-secondary w-100" type="button">Buscar</button>
      </div>
 
      <div class="col-md-6">
        <label class="form-label">Logradouro</label>
        <input id="logradouro" class="form-control">
      </div>
 
      <div class="col-md-4">
        <label class="form-label">Bairro</label>
        <input id="bairro" class="form-control">
      </div>
      <div class="col-md-6">
        <label class="form-label">Cidade</label>
        <input id="cidade" class="form-control">
      </div>
      <div class="col-md-2">
        <label class="form-label">UF</label>
        <input id="uf" class="form-control" maxlength="2">
      </div>
    </div>
  </div>
</div>
 
<script>
  window.PW3CEP = {
    urlBase: "{{ url('/cep') }}"
  };
    function showToast(title, body) {
        const el = document.getElementById('pw3Toast');
        document.getElementById('pw3ToastTitle').textContent = title;
        document.getElementById('pw3ToastBody').textContent = body;
 
        const toast = bootstrap.Toast.getOrCreateInstance(el, {
            delay: 2500
        });
 
        toast.show();
    }
 
    // Remove tudo que não for número
    function onlyDigits(v) {
        return (v || '').replace(/\D/g, '');
    }
 
    // Máscara de CEP: 00000-000
    const cepInput = document.getElementById('cep');
 
    cepInput?.addEventListener('input', function () {
        let valor = this.value.replace(/\D/g, '');
 
        // Máximo de 8 números
        valor = valor.substring(0, 8);
 
        // Adiciona o hífen
        if (valor.length > 5) {
            valor = valor.substring(0, 5) + '-' + valor.substring(5);
        }
 
        this.value = valor;
 
            
    this.classList.remove('is-invalid');
    document.getElementById('cepErro').textContent = '';
    });
 
    // Buscar CEP
    document.getElementById('btnBuscarCep')?.addEventListener('click', async () => {
 
        const cepEl = document.getElementById('cep');
 
        // Remove o hífen antes de enviar
        const cep = onlyDigits(cepEl.value);
 
        // Verifica se possui 8 números
        if (cep.length !== 8) {
            showToast('Erro', 'Digite um CEP válido com 8 números.');
            return;
        }
 
        try {
            const resp = await fetch(`${window.PW3CEP.urlBase}/${cep}`, {
                headers: {
                    'Accept': 'application/json'
                }
            });
 
            const data = await resp.json();
 
    if (!resp.ok) {
 
    if (resp.status === 422) {
        cepEl.classList.add('is-invalid');
 
        const mensagem =
            data.errors?.cep?.[0] ||
            data.message ||
            'CEP inválido.';
 
        document.getElementById('cepErro').textContent = mensagem;
 
        return;
    }
 
    showToast('Erro', data.message || 'Falha ao consultar CEP.');
    return;
}
 
            document.getElementById('logradouro').value = data.logradouro || '';
            document.getElementById('bairro').value = data.bairro || '';
            document.getElementById('cidade').value = data.localidade || '';
            document.getElementById('uf').value = data.uf || '';
 
            showToast('Sucesso', data.message);
 
        } catch (e) {
            console.error(e);
            showToast('Erro', 'Falha de conexão. Verifique servidor e internet.');
        }
    });
</script>
 
<script>
  window.PW3 = {
    pedidoId: "{{ $pedido->id }}",
    urlAdd: "{{ route('pedidos.itens.storeJson', $pedido) }}",
    urlDelBase: "{{ url('pedidos/'.$pedido->id.'/itens-json') }}",
    isFechado: "@json($pedido->status === 'fechado')"
  };
 
  function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  }
 
  function moneyBR(value) {
    return (value ?? 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
  }
 
  function upsertRow(item) {
    const tbody = document.getElementById('itensBody');
    let row = document.getElementById('item-' + item.id);
 
    if (row) {
      // Atualiza apenas a quantidade e o subtotal sem destruir a linha da tabela
      const qtdCell = row.querySelector('.item-qtd') || row.cells[1];
      const subtotalCell = row.querySelector('.item-subtotal') || row.cells[3];
 
      if (qtdCell) qtdCell.textContent = item.quantidade;
      if (subtotalCell) subtotalCell.textContent = moneyBR(item.subtotal);
    } else {
      // Se for um item novo adicionado
      const nomeProduto = item.produto ? item.produto.nome : '';
      const actionsHtml = window.PW3.isFechado ? '' : `
        <td class="text-end">
          <button class="btn btn-sm btn-outline-warning" data-increase="${item.id}" title="Aumentar quantidade">+</button>
          <button class="btn btn-sm btn-outline-warning" data-decrease="${item.id}" title="Diminuir quantidade">-</button>
          <button class="btn btn-sm btn-outline-danger" data-remove="${item.id}" title="Remover item">Remover</button>
        </td>`;
 
      const html = `
        <tr id="item-${item.id}">
          <td>${nomeProduto}</td>
          <td class="text-end item-qtd">${item.quantidade}</td>
          <td class="text-end">${moneyBR(item.preco_unitario)}</td>
          <td class="text-end item-subtotal">${moneyBR(item.subtotal)}</td>
          ${actionsHtml}
        </tr>`;
 
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
 
  const formAdd = document.getElementById('formAddItem');
  if (formAdd) {
    formAdd.addEventListener('submit', async (e) => {
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
  }
 
  const itensBody = document.getElementById('itensBody');
  if (itensBody) {
    itensBody.addEventListener('click', async (e) => {
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
 
        } catch (err) {
          console.error(err);
          showToast('Erro', 'Falha de conexão.');
        }
      }
    });
  }
</script>
 
@endsection
 