<?php
session_start();
include 'dbHotel.php';

if (!isset($_SESSION['usuarioId'])) {
    header("Location: index.php");
    exit;
}

$usuarioId = $_SESSION['usuarioId'];
$usuarioTipo = $_SESSION['usuarioTipo']; // 'cliente' ou 'hotel'

// Buscar informações do usuário
try {
    $stmt = $pdo->prepare("SELECT Id, Nome, Email, Tipo FROM Usuarios WHERE Id = ?");
    $stmt->execute([$usuarioId]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erro ao carregar usuário: " . $e->getMessage();
}

$quartos = [];
$termo = isset($_GET['pesquisa']) ? trim($_GET['pesquisa']) : '';

if ($usuarioTipo === 'hotel') {
    $sql = "SELECT q.id AS QuartoId, q.numero, q.tipo, q.preco, q.status, 
                   u.Nome AS ClienteNome, r.data_checkin, r.data_checkout
            FROM Quartos q
            LEFT JOIN Reservas r ON q.id = r.quarto_id
            LEFT JOIN Usuarios u ON r.usuario_id = u.id";

    if (!empty($termo)) {
        $sql .= " WHERE u.Nome LIKE :termo";
    }

    try {
        $stmt = $pdo->prepare($sql);
        if (!empty($termo)) {
            $stmt->bindValue(':termo', "%$termo%");
        }
        $stmt->execute();
        $quartos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Erro ao buscar quartos: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Reservas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        .hero {
            background: url('https://source.unsplash.com/1600x900/?hotel') no-repeat center center;
            background-size: cover;
            height: 100vh;
            position: relative;
            color: #fff;
        }

        footer {
            margin-top: auto;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="#">Hotel Lux</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto">
                <?php if ($usuarioTipo === 'cliente'): ?>
                    <li class="nav-item"><a class="nav-link" href="quartos.php">Fazer Reserva</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="adicionar_quarto.php">Adicionar Quartos</a></li>
                    <li class="nav-item"><a class="nav-link" href="servico_quarto.php">Serviço de Quarto</a></li>
                    <li class="nav-item"><a class="nav-link" href="baixas_pagamento.php">Baixas de Pagamento</a></li>
                    <li class="nav-item"><a class="nav-link" href="funcionarios.php">Cadastrar Funcionário</a></li>
                <?php endif; ?>
                
                <!-- Ícone de Perfil -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="perfilDropdown" role="button" data-bs-toggle="modal" data-bs-target="#perfilModal">
                        <i class="fas fa-user-circle fa-lg"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Modal do Perfil -->
<div class="modal fade" id="perfilModal" tabindex="-1" aria-labelledby="perfilModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="perfilModalLabel">Meu Perfil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p><strong>Nome:</strong> <?php echo htmlspecialchars($usuario['Nome']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($usuario['Email']); ?></p>
                <p><strong>Tipo de Usuário:</strong> <?php echo $usuarioTipo === 'cliente' ? 'Cliente' : 'Hotel'; ?></p>
            </div>
            <div class="modal-footer">
                <a href="logout.php" class="btn btn-danger">Sair</a>
            </div>
        </div>
    </div>
</div>


<!-- Conteúdo principal -->
<div class="container mt-5 pt-5">
    <h4>Gerenciar Reservas</h4>
    
    <!-- Formulário de busca -->
    <div class="input-group mb-3">
        <form method="GET" action="teste.php" class="d-flex w-100">
            <input type="text" class="form-control" name="pesquisa" placeholder="Pesquisar cliente..." value="<?php echo htmlspecialchars($termo); ?>">
            <button class="btn btn-primary" type="submit">Buscar</button>
        </form>
    </div>

    <!-- Tabela de Quartos -->
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>Quarto</th>
                <th>Tipo</th>
                <th>Preço</th>
                <th>Status</th>
                <th>Cliente</th>
                <th>Data Check-in</th>
                <th>Data Check-out</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($quartos as $quarto): ?>
                <tr>
                    <td><?php echo htmlspecialchars($quarto['numero']); ?></td>
                    <td><?php echo htmlspecialchars($quarto['tipo']); ?></td>
                    <td>R$ <?php echo number_format($quarto['preco'], 2, ',', '.'); ?></td>
                    <td><?php echo htmlspecialchars($quarto['status']); ?></td>
                    <td><?php echo $quarto['ClienteNome'] ? htmlspecialchars($quarto['ClienteNome']) : 'Nenhum'; ?></td>
                    <td><?php echo !empty($quarto['data_checkin']) ? date('d/m/Y', strtotime($quarto['data_checkin'])) : 'N/A'; ?></td>
                    <td><?php echo !empty($quarto['data_checkout']) ? date('d/m/Y', strtotime($quarto['data_checkout'])) : 'N/A'; ?></td>
                    <td>
                        <a href="baixas_quarto.php?id=<?php echo $quarto['QuartoId']; ?>&acao=disponivel" class="btn btn-success btn-sm">Liberar</a>
                        <a href="baixas_quarto.php?id=<?php echo $quarto['QuartoId']; ?>&acao=ocupado" class="btn btn-danger btn-sm">Ocupado</a>
                        <a href="baixas_quarto.php?id=<?php echo $quarto['QuartoId']; ?>&acao=manutencao" class="btn btn-warning btn-sm">Manutenção</a>
                        <a href="editar_quarto.php?id=<?php echo $quarto['QuartoId']; ?>" class="btn btn-info btn-sm">Editar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Footer -->
<footer class="bg-dark text-white py-4 text-center">
    <p>&copy; 2025 Hotel Lux. Todos os direitos reservados.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
