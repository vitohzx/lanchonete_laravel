<?php

namespace App\Http\Controllers;

class SobreController extends Controller {
    public function index() {
        $titulo = "Bem vindo a pagina sobre";
        return view('contato', compact("titulo"));
    }
}