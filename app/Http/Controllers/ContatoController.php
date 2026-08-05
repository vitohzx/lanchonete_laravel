<?php

namespace App\Http\Controllers;

class ContatoController extends Controller {
    public function index() {
        $titulo = "Bem vindo a página contatos";
        $teste = "entre em contato";
        return view("contato", compact(("titulo"), ("teste")));
    }
}