<?php

namespace App;

use NumberToWords\NumberToWords;

class Sonda
{

    const LIMITE_X = 4;
    const LIMITE_Y = 4;

    private $descricoesMovimentos = [];
    private static $quantidadeMovimentacoes = 0;

    private $x, $y, $direcao;
    private $_localArquivo = __DIR__.'/../database/sonda-actual-state';

    public function __construct($env = 'prod')
    {
        if( $env == 'test' ) {
            $this->_localArquivo = __DIR__.'/../database/sonda-actual-state-test';
        }

        $this->load();
    }

    public function posicaoInicial()
    {
        $this->x = 0;
        $this->y = 0;
        $this->direcao = [1,0];
        self::$quantidadeMovimentacoes = 0;
        $this->save();
    }

    public function movimentar()
    {
        foreach (['x','y'] as $index => $prop) {
            $this->$prop += $this->direcao[$index];

            if( $this->$prop < 0 || $this->$prop > constant("self::LIMITE_".strtoupper($prop)) ) {
                throw new \Exception("Um movimento inválido foi detectado, infelizmente a sonda ainda não possui a habilidade de transpor os limites impostos");
            }
        }

        self::$quantidadeMovimentacoes++;

        $this->save();
    }

    public function girar($direcao)
    {
        $direcaoAtualX = $this->direcao[0];
        $direcaoAtualY = $this->direcao[1];

        $this->calcularMovimentacoes();

        if( $direcao == 'GE' ) {
            $fatorMovimentacaoX = 0;
            $fatorMovimentacaoY = 1;

            $this->descricoesMovimentos[] = 'girou para a esquerda';
        } else {
            $fatorMovimentacaoX = 1;
            $fatorMovimentacaoY = 0;

            $this->descricoesMovimentos[] = 'virou para a direita';
        }

        if( abs($direcaoAtualX) == $fatorMovimentacaoX ) {
            $direcaoAtualY *= -1;
        }
        if( abs($direcaoAtualY) == $fatorMovimentacaoY ) {
            $direcaoAtualX *= -1;
        }

        $this->direcao[0] = $direcaoAtualY;
        $this->direcao[1] = $direcaoAtualX;

        $this->save();
    }

    public function toArray()
    {
        return ['x' => $this->x, 'y' => $this->y, 'direcao' => $this->direcao];
    }

    private function save()
    {
        file_put_contents($this->_localArquivo, json_encode($this->toArray()));
    }

    private function load()
    {
        $data = json_decode(file_get_contents($this->_localArquivo));
        $this->x = $data->x;
        $this->y = $data->y;
        $this->direcao = $data->direcao;
    }

    public static function face($direcao)
    {
        if( abs($direcao[0]) == 1 ) {
            if( $direcao[0] > 0 ) {
                return 'D';
            }

            return 'E';
        }

        if( $direcao[1] > 0 ) {
            return 'C';
        }

        return 'B';
    }

    private function calcularMovimentacoes() {

        if( self::$quantidadeMovimentacoes > 0 ) {
            $eixoAtual = $this->direcao[0] == 0 ? 'y' : 'x';
            if( $eixoAtual == 'y' ) {
                $this->descricoesMovimentos[] = 'se moveu '.self::$quantidadeMovimentacoes.' espaço'. (self::$quantidadeMovimentacoes > 1 ? 's' : '') .' no eixo y';
            } else {
                $this->descricoesMovimentos[] = 'se moveu '.(new NumberToWords())->getNumberTransformer('pt_BR')->toWords(self::$quantidadeMovimentacoes).' espaço'. (self::$quantidadeMovimentacoes > 1 ? 's' : '') .' no eixo x';
            }
            self::$quantidadeMovimentacoes = 0;
        }

    }

    public function descreverTodasMovimentacoes()
    {

        $this->calcularMovimentacoes();
        $inicioDescricao = 'A sonda ';
        $ultimaDescricao = array_pop($this->descricoesMovimentos);
        if( !empty($this->descricoesMovimentos) ) {
            return $inicioDescricao . implode(', ', $this->descricoesMovimentos) . ' e '. $ultimaDescricao;
        }

        return $inicioDescricao . $ultimaDescricao;
    }

}