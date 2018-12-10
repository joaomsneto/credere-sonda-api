# Documentação API Sonda

## Endpoints

#### Enviar a sonda para a posição inicial

##### Requisição
```
POST /sonda/posicao-inicial
```

##### Resposta
* Código: 204

#### Receber a posição atual da sonda

##### Requisição
```
GET /sonda
```

##### Resposta
* Código: 200
* Conteúdo: `{
                 "x": 0,
                 "y": 0,
                 "face": "D"
             }`

#### Enviar movimentações para a sonda

##### Requisição
+ Parâmetros
    + movimentacoes: `["M","M","GE","M"]` - Opções possíveis: `"M","GE" e "GD"`
```
POST /sonda
```
##### Resposta

* Código: 200
* Conteúdo: `{
                 "x": 2,
                 "y": 1,
                 "face": "C",
                 "descricao": "A sonda se moveu dois espaços no eixo x, girou para a esquerda e se moveu 1 espaço no eixo y"
             }`
 ---
 * Código: 405
 * Conteúdo: `{
                  "erro": "Um movimento inválido foi detectado, infelizmente a sonda ainda não possui a habilidade de transpor os limites impostos"
              }`
