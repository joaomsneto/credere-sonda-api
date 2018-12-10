<?php

namespace App\Http\Controllers;


use App\Sonda;
use Illuminate\Http\Request;

class SondaController extends Controller
{
    public function __construct()
    {

    }

    /**
     * @description Endpoint para retornar a sonda à posição inicial
     * @response 204
     */
    public function irParaPosicaoInicial()
    {
        $sonda = new Sonda();
        $sonda->posicaoInicial();

        return response(null, 204);
    }

    /**
     * @description Endpoint para receber a posição atual da sonda
     * @response {
     *  "x": 0,
     *  "y": 0,
     *  "face": "D"
     * }
     */
    public function posicaoAtual()
    {
        $sonda = new Sonda();
        $posicaoAtual = $sonda->toArray();
        $posicaoAtual['face'] = Sonda::face($posicaoAtual['direcao']);
        unset($posicaoAtual['direcao']);
        return response()->json($posicaoAtual, 200);
    }

    /**
     * @description Endpoint para movimentar a sonda através de uma lista de movimentos
     * @bodyParam movimentos array required A lista de movimentos a serem executados pela sonda
     * @response {
     *  "x": 2,
     *  "y": 1,
     *  "face": "C",
     *  "descricao": "A sonda se moveu dois espaços no eixo x, girou para a esquerda e se moveu 1 espaço no eixo y"
     * }
     * @response 405 {
     *  "erro" : "Um movimento inválido foi detectado, infelizmente a sonda ainda não possui a habilidade de transpor os limites impostos"
     * }
     */
    public function movimentar(Request $request)
    {
        $sonda = new Sonda();
        $movimentos = json_decode($request->post('movimentos', '[]'));

        if( !is_array($movimentos) ) {
            $movimentos = [$movimentos];
        }

        foreach ($movimentos as $movimento) {
            if( $movimento == 'M' ) {
                try{
                    $sonda->movimentar();
                } catch (\Exception $e) {
                    return response()->json(["erro" => $e->getMessage()], 405);
                }
            } else if(in_array($movimento, ['GD','GE'])) {
                $sonda->girar($movimento);
            }
        }

        $posicaoAtual = $sonda->toArray();
        $posicaoAtual['face'] = Sonda::face($posicaoAtual['direcao']);
        unset($posicaoAtual['direcao']);
        $posicaoAtual['descricao'] = $sonda->descreverTodasMovimentacoes();
        return response()->json($posicaoAtual, 200);
    }

}