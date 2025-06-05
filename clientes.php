<?php
require_once 'config.php';

// Processar formulário de cliente
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];

    try {
        if (isset($_POST['id'])) {
            // Editar cliente existente
            $stmt = $pdo->prepare("UPDATE clientes SET nome = ?, telefone = ?, email = ? WHERE id = ?");
            $stmt->execute([$nome, $telefone, $email, $_POST['id']]);
            $success = "Cliente atualizado com sucesso!";
        } else {
            // Adicionar novo cliente
            $stmt = $pdo->prepare("INSERT INTO clientes (nome, telefone, email) VALUES (?, ?, ?)");
            $stmt->execute([$nome, $telefone, $email]);
            $success = "Cliente cadastrado com sucesso!";
        }
    } catch (PDOException $e) {
        $error = "Erro ao salvar cliente: " . $e->getMessage();
    }
}

// Processar exclusão de cliente
if (isset($_GET['delete'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM clientes WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
        $success = "Cliente excluído com sucesso!";
    } catch (PDOException $e) {
        $error = "Erro ao excluir cliente: " . $e->getMessage();
    }
}

// Buscar cliente para edição
$cliente = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Buscar todos os clientes
$stmt = $pdo->query("SELECT * FROM clientes ORDER BY nome");
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes - NailBook</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="Css/Css.css">
</head>
<body>
    <div class="loader">
        <div class="spinner"></div>
    </div>

    <header>
        <div class="logo-container">
            <img src="Imagens/logosite.png" alt="Logo NailBook" class="logo">
            <h1>Clientes <span>NailBook</span></h1>
        </div>

        <nav class="menu">
            <ul>
                <li><a href="index.html"><i class="fas fa-home"></i> Início</a></li>
                <li><a href="agendamentos.php"><i class="far fa-calendar-alt"></i> Agendamentos</a></li>
                <li><a href="servicos.php"><i class="fas fa-spa"></i> Serviços</a></li>
                <li><a href="clientes.php" class="active"><i class="fas fa-users"></i> Clientes</a></li>
                <li><a href="relatorios.php"><i class="fas fa-chart-line"></i> Relatórios</a></li>
            </ul>
        </nav>
    </header>

    <main class="container">
        <section class="clientes-form">
            <h2><i class="fas fa-user-edit"></i> <?= isset($cliente) ? 'Editar Cliente' : 'Novo Cliente' ?></h2>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= $success ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <?php if (isset($cliente)): ?>
                    <input type="hidden" name="id" value="<?= $cliente['id'] ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="nome"><i class="fas fa-user"></i> Nome:</label>
                    <input type="text" id="nome" name="nome" required 
                           value="<?= isset($cliente) ? htmlspecialchars($cliente['nome']) : '' ?>">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="telefone"><i class="fas fa-phone"></i> Telefone:</label>
                        <input type="text" id="telefone" name="tele