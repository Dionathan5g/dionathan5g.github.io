<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = isset($_POST["nome"]) ? $_POST["nome"] : "Desconhecido";
    $servico = isset($_POST["servico"]) ? $_POST["servico"] : "Serviço";
    $preco = isset($_POST["preco"]) ? floatval(str_replace(['R$', ',', ' '], '', $_POST["preco"])) : 0;
    $horario = isset($_POST["horario"]) ? $_POST["horario"] : "Horário";
    $data = isset($_POST["data"]) ? $_POST["data"] : date("Y-m-d");

    $dataFormatada = date("d-m-Y", strtotime($data));
    $diretorio = "Dados/" . $dataFormatada;

    if (!is_dir($diretorio)) {
        mkdir($diretorio, 0777, true);
    }

    $nomeArquivo = $diretorio . "/" . "{$nome} - {$servico} - {$horario}.txt";

    $conteudo = "Nome: $nome\n";
    $conteudo .= "Serviço: $servico\n";
    $conteudo .= "Preço: R$ " . number_format($preco, 2, ',', '.') . "\n";
    $conteudo .= "Horário: $horario\n";
    $conteudo .= "Data: " . date("d/m/Y", strtotime($data)) . "\n";

    file_put_contents($nomeArquivo, $conteudo);

    // Salvar também em um arquivo único para relatórios
    $arquivoRelatorios = "Dados/relatorios.csv";
    $linha = "$nome,$servico,$preco,$horario,$data\n";
    file_put_contents($arquivoRelatorios, $linha, FILE_APPEND);

    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Agendamento Concluído</title>
        <link rel='stylesheet' href='Css/Css.css'>
    </head>
    <body style='display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0;'>
        <div style='text-align: center; max-width: 500px; padding: 30px;'>
            <div class='alert-success' style='animation: fadeIn 0.5s, pulse 1s; padding: 20px; background-color: #4CAF50; color: white; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); margin-bottom: 20px;'>
                <p style='font-size: 1.2em; font-weight: bold; margin: 0;'>✓ Agendamento salvo com sucesso!</p>
            </div>
            <a href='index.html' style='display: inline-block; padding: 12px 24px; background-color: #c68df0; color: white; text-decoration: none; border-radius: 6px; transition: all 0.3s; font-weight: bold;'>
                Voltar ao Início
            </a>
        </div>
    </body>
    </html>";
} else {
    echo "<div class='alert-error' style='animation: fadeIn 0.5s, shake 0.5s; display: inline-block; padding: 15px 30px; background-color: #F44336; color: white; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);'>Método inválido!</div>";
}
?>