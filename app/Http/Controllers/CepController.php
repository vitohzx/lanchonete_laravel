<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CepController extends Controller
{
    public function show(string $cep)
    {
        // Normaliza CEP: mantém apenas números
        $cep = preg_replace('/\D/', '', $cep);

        // Validação simples (CEP brasileiro tem 8 dígitos)
        if (strlen($cep) !== 8) {
            return response()->json([
                'message' => 'CEP inválido. Informe 8 dígitos.',
                'errors' => ['cep' => ['CEP deve conter 8 números.']]
            ], 422);
        }

        $cacheKey = "cep:$cep";

        $data = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($cep) {
            // ViaCEP: https://viacep.com.br/ws/{cep}/json/
            $resp = Http::timeout(3)->acceptJson()
                ->get("https://viacep.com.br/ws/{$cep}/json/");

            if (!$resp->ok()) {
                return ['_error' => 'Falha ao consultar serviço de CEP.'];
            }

            $json = $resp->json();

            // ViaCEP retorna {"erro": true} quando não encontra
            if (isset($json['erro']) && $json['erro'] === true) {
                return ['_error' => 'CEP não encontrado.'];
            }

            return $json;
        });

        if (isset($data['_error'])) {
            return response()->json([
                'message' => $data['_error']
            ], 404);
        }

        // Padroniza resposta para o front
        return response()->json([
            'message' => 'CEP encontrado!',
            'cep' => $data['cep'] ?? $cep,
            'logradouro' => $data['logradouro'] ?? '',
            'bairro' => $data['bairro'] ?? '',
            'localidade' => $data['localidade'] ?? '',
            'uf' => $data['uf'] ?? '',
            'ibge' => $data['ibge'] ?? null,
        ], 200);
    }
}
