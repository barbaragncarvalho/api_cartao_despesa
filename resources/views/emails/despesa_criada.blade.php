<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Document</title>
</head>
<body>
<h1>Olá, tudo bem?</h1>
<h2>Uma nova despesa foi criada no valor de R${{ number_format($valor,2,',','.') }}.</h2>
<p>Descrição desta despesa: {{$descricao}}.</p>
<p>Número do cartão: {{$cartao}}</p>
<p>Se não foi você, favor entrar em contato.</p>
</body>
</html>
