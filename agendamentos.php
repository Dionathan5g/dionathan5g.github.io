<?php
require_once 'config.php';

// Processar formulário de agendamento
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = $_POST['cliente_id'];
    $servico_id = $_POST['servico_id'];
    $data = $_POST['data'];
    $hora = $_POST['hora'];
    $observacoes = $_POST['observacoes'];

    try {
        $stmt = $pdo->prepare("INSERT INTO agendamentos (cliente_id, servico_id, data_agendamento, hora_agendamento, observacoes) 
                              VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$cliente_id, $servico_id, $data, $hora, $observacoes]);
        
        $success = "Agendamento realizado com sucesso!";
    } catch (PDOException $e) {
        $error = "Erro ao agendar: " . $e->getMessage();
    }
}

// Buscar agendamentos
$stmt = $pdo->query("
    SELECT a.id, c.nome AS cliente, s.nome AS servico, s.preco, 
           a.data_agendamento, a.hora_agendamento, a.observacoes, a.status
    FROM agendamentos a
    JOIN clientes c ON a.cliente_id = c.id
    JOIN servicos s ON a.servico_id = s.id
    ORDER BY a.data_agendamento DESC, a.hora_agendamento DESC
");
$agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar clientes e serviços para o formulário
$clientes = $pdo->query("SELECT id, nome FROM clientes ORDER BY nome")->fetchAll();
$servicos = $pdo->query("SELECT id, nome, preco FROM servicos ORDER BY nome")->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamentos - NailBook</title>
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
            <h1>Agendamentos <span>NailBook</span></h1>
        </div>

        <nav class="menu">
            <ul>
                <li><a href="index.html"><i class="fas fa-home"></i> Início</a></li>
                <li><a href="agendamentos.php" class="active"><i class="far fa-calendar-alt"></i> Agendamentos</a></li>
                <li><a href="servicos.php"><i class="fas fa-spa"></i> Serviços</a></li>
                <li><a href="clientes.php"><i class="fas fa-users"></i> Clientes</a></li>
                <li><a href="relatorios.php"><i class="fas fa-chart-line"></i> Relatórios</a></li>
            </ul>
        </nav>
    </header>

    <main class="container">
        <section class="agendamento-form">
            <h2><i class="far fa-calendar-plus"></i> Novo Agendamento</h2>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success">
                    <?= $success ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="cliente_id"><i class="fas fa-user"></i> Cliente:</label>
                    <select id="cliente_id" name="cliente_id" required>
                        <option value="">Selecione um cliente</option>
                        <?php foreach ($clientes as $cliente): ?>
                            <option value="<?= $cliente['id'] ?>"><?= htmlspecialchars($cliente['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="servico_id"><i class="fas fa-spa"></i> Serviço:</label>
                    <select id="servico_id" name="servico_id" required>
                        <option value="">Selecione um serviço</option>
                        <?php foreach ($servicos as $servico): ?>
                            <option value="<?= $servico['id'] ?>" data-preco="<?= $servico['preco'] ?>">
                                <?= htmlspecialchars($servico['nome']) ?> - R$ <?= number_format($servico['preco'], 2, ',', '.') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="data"><i class="far fa-calendar"></i> Data:</label>
                        <input type="date" id="data" name="data" required>
                    </div>

                    <div class="form-group">
                        <label for="hora"><i class="far fa-clock"></i> Hora:</label>
                        <input type="time" id="hora" name="hora" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="observacoes"><i class="fas fa-edit"></i> Observações:</label>
                    <textarea id="observacoes" name="observacoes" rows="3"></textarea>
                </div>

                <button type="submit" class="btn-primary">
                    <i class="far fa-save"></i> Agendar
                </button>
            </form>
        </section>

        <section class="agendamentos-list">
            <h2><i class="far fa-list-alt"></i> Agendamentos Recentes</h2>
            
            <div class="filters">
                <input type="text" id="search" placeholder="Buscar cliente...">
                <select id="filter-status">
                    <option value="">Todos os status</option>
                    <option value="agendado">Agendados</option>
                    <option value="concluido">Concluídos</option>
                    <option value="cancelado">Cancelados</option>
                </select>
            </div>
            
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Hora</th>
                            <th>Cliente</th>
                            <th>Serviço</th>
                            <th>Valor</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($agendamentos as $agendamento): ?>
                            <tr data-status="<?= $agendamento['status'] ?>">
                                <td><?= date('d/m/Y', strtotime($agendamento['data_agendamento'])) ?></td>
                                <td><?= substr($agendamento['hora_agendamento'], 0, 5) ?></td>
                                <td><?= htmlspecialchars($agendamento['cliente']) ?></td>
                                <td><?= htmlspecialchars($agendamento['servico']) ?></td>
                                <td>R$ <?= number_format($agendamento['preco'], 2, ',', '.') ?></td>
                                <td>
                                    <span class="status-badge status-<?= $agendamento['status'] ?>">
                                        <?= ucfirst($agendamento['status']) ?>
                                    </span>
                                </td>
                                <td class="actions">
                                    <a href="editar_agendamento.php?id=<?= $agendamento['id'] ?>" class="btn-edit" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="mudar_status.php?id=<?= $agendamento['id'] ?>&status=concluido" class="btn-complete" title="Concluir">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    <a href="mudar_status.php?id=<?= $agendamento['id'] ?>&status=cancelado" class="btn-cancel" title="Cancelar">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2023 NailBook - Studio de Beleza. Todos os direitos reservados.</p>
    </footer>

    <script src="Java/javacript.js"></script>
    <script>
        // Filtros de busca
        document.getElementById('search').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('.agendamentos-list tbody tr');
            
            rows.forEach(row => {
                const cliente = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                if (cliente.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Filtro por status
        document.getElementById('filter-status').addEventListener('change', function() {
            const status = this.value;
            const rows = document.querySelectorAll('.agendamentos-list tbody tr');
            
            rows.forEach(row => {
                if (!status || row.getAttribute('data-status') === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>