<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a href="{{ route('home') }}">{{ config('app.name') }}</a>
        <button class="navbar-toggler" type="button" data-bs- toggle="collapse" data-bs-target="#navbarConteudo">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarConteudo">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a href="pedidos">Pedidos</a> </li> &emsp; &emsp;
                <li class="nav-item"><a href="contato">Contato</a> </li> &emsp; &emsp;
                <li class="nav-item"><a href="sobre">Sobre</a> </li> &emsp; &emsp;
                @guest
                    <li class="nav-item"><a class="nav-link" href="{{ route('login.form') }}">Entrar</a></li> &emsp; &emsp;
                    <li class="nav-item"><a class="nav-link" href="{{ route('register.form') }}">Criar conta</a></li> &emsp; &emsp;
                @endguest

                @auth
                    <li class="nav-item"><a href="{{ route('dashboard') }}">Dashboard</a></li> &emsp; &emsp;

                    @if (auth()->user()->role === 'gerente')
                        <li class="nav-item"><a href="{{ route('categorias.index') }}">Categorias</a></li> &emsp; &emsp;
                    @endif

                    <li class="nav-item"><a href="{{ route('produtos.index') }}">Produtos</a></li> &emsp; &emsp;
                    <li class="text-success">{{ auth()->user()->name }} ({{ auth()->user()->role }})</li> &emsp; &emsp;
                    <li class="nav-item"><a href="{{ route('profile.edit') }}">Minha Conta</a></li> &emsp; &emsp;
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-danger px-5 p-0" type="submit">Sair</button>
                        </form>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>