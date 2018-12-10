<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function(){
    return redirect('https://github.com/joaomsneto/credere-sonda-api/blob/master/documentation.md');
});
$router->post('/sonda/posicao-inicial', 'SondaController@irParaPosicaoInicial');
$router->get('/sonda', 'SondaController@posicaoAtual');
$router->post('/sonda', 'SondaController@movimentar');
