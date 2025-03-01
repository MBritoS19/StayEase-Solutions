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

$minhasReservas = [];

// Buscar reservas do cliente
if ($usuarioTipo === 'cliente') {
    $sql = "SELECT r.id AS ReservaId, q.numero AS QuartoNumero, q.tipo AS QuartoTipo, q.preco AS Preco, r.data_checkin, r.data_checkout
            FROM Reservas r
            LEFT JOIN Quartos q ON r.quarto_id = q.id
            WHERE r.usuario_id = :usuarioId";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':usuarioId', $usuarioId);
        $stmt->execute();
        $minhasReservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Erro ao buscar reservas: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil do Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        footer {
            margin-top: auto;
            text-align: center;
            background: #343a40;
            color: white;
            padding: 15px 0;
        }

        .navbar-toggler-icon {
            color: white;
        }

        .navbar-collapse {
            justify-content: flex-end;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Hotel Lux</a>
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a href="quartos.php" class="nav-link">Fazer Reserva</a>
                </li>
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


    <h5>Minhas Reservas</h5>
    <?php if (count($minhasReservas) > 0): ?>
        <div class="row">
            <?php foreach ($minhasReservas as $reserva): ?>
                <div class="col-md-4">
                    <div class="card mb-4">
                        <img src="path/to/quarto-image.jpg" class="card-img-top" alt="Imagem do Quarto">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($reserva['QuartoNumero']); ?> - <?php echo htmlspecialchars($reserva['QuartoTipo']); ?></h5>
                            <p class="card-text">Preço: R$ <?php echo number_format($reserva['Preco'], 2, ',', '.'); ?></p>
                            <p class="card-text"><strong>Check-in:</strong> <?php echo date('d/m/Y', strtotime($reserva['data_checkin'])); ?></p>
                            <p class="card-text"><strong>Check-out:</strong> <?php echo date('d/m/Y', strtotime($reserva['data_checkout'])); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Você ainda não fez nenhuma reserva.</p>
    <?php endif; ?>
</div>

<!-- Footer -->
<footer class="bg-dark text-white py-4 text-center">
    <p>&copy; 2025 Hotel Lux. Todos os direitos reservados.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
