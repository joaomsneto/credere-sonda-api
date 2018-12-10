<?php

namespace App;


use PHPUnit\Framework\TestCase;

class SondaTest extends TestCase
{

    public function testFoiParaPosicaoInicial()
    {
        $sonda = new Sonda('test');

        $sonda->posicaoInicial();
        $this->assertEquals(
            $sonda->toArray(),
            ['x' => 0, 'y' => 0, 'direcao' => [1,0]]
        );
    }

    public function testRetornaPosicaoAtualCorreta()
    {
        $sonda = new Sonda('test');

        $sonda->posicaoInicial();
        $this->assertEquals(
            $sonda->toArray(),
            json_decode(file_get_contents(__DIR__ . '/../database/sonda-actual-state-test'), true)
        );
    }

    public function testGiroExecutado()
    {
        $sonda = new Sonda('test');

        $sonda->posicaoInicial();
        $sonda->girar('GE');
        $this->assertEquals(
            $sonda->toArray(),
            ['x' => 0, 'y' => 0, 'direcao' => [0,1]]
        );

        $sonda->girar('GD');
        $this->assertEquals(
            $sonda->toArray(),
            ['x' => 0, 'y' => 0, 'direcao' => [1,0]]
        );

        $sonda->girar('GD');
        $this->assertEquals(
            $sonda->toArray(),
            ['x' => 0, 'y' => 0, 'direcao' => [0,-1]]
        );
    }

    public function testMovimentacaoExecutada()
    {
        $sonda = new Sonda('test');

        $sonda->posicaoInicial();
        $sonda->movimentar();
        $this->assertEquals(
            $sonda->toArray(),
            ['x' => 1, 'y' => 0, 'direcao' => [1,0]]
        );

        for($i = 0; $i <= 2; $i++)
            $sonda->movimentar();
        $this->assertEquals(
            $sonda->toArray(),
            ['x' => 4, 'y' => 0, 'direcao' => [1,0]]
        );
    }

    public function testMovimentacaoNaoExecutada()
    {
        $this->expectException(\Exception::class);
        $sonda = new Sonda('test');

        for($i = 0; $i <= 10; $i++) {
            $sonda->movimentar();
        }
    }

    public function testArquivoExiste()
    {
        $this->assertFileExists(__DIR__ . '/../database/sonda-actual-state');
        $this->assertFileExists(__DIR__ . '/../database/sonda-actual-state-test');
    }

    public function testFaceCorreta()
    {
        $resultadosEsperados = ['D' => [1,0], 'C' => [0,1], 'E' => [-1,0], 'B' => [0,-1]];

        foreach ($resultadosEsperados as $face => $direcao) {
            $this->assertEquals(
                Sonda::face($direcao),
                $face
            );
        }
    }

    public function testDescricaoMovimentacaoCorreta()
    {
        $sonda = new Sonda('test');

        $sonda->posicaoInicial();
        $sonda->girar('GE');
        for($i = 0; $i <= 2; $i++) {
            $sonda->movimentar();
        }
        $sonda->girar('GD');
        for($i = 0; $i <= 1; $i++) {
            $sonda->movimentar();
        }
        $sonda->girar('GD');
        $sonda->movimentar();
        $this->assertEquals(
            $sonda->descreverTodasMovimentacoes(),
            'A sonda girou para a esquerda, se moveu 3 espaços no eixo y, virou para a direita, se moveu dois espaços no eixo x, virou para a direita e se moveu 1 espaço no eixo y'
        );

    }
}
