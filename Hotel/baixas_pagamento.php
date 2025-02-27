<?php
session_start();
include 'dbHotel.php';

if (!isset($_SESSION['usuarioId']) || $_SESSION['usuarioTipo'] !== 'hotel') {
    header("Location: index.php");
    exit;
}

// Buscar pagamentos pendentes
$pagamentos = [];
try {
    $stmt = $pdo->query("SELECT p.Id, p.Valor, p.Status, u.Nome AS Cliente, r.DataCheckIn, r.DataCheckOut 
                         FROM Pagamentos p
                         JOIN Reservas r ON p.ReservaId = r.Id
                         JOIN Usuarios u ON r.ClienteId = u.Id
                         WHERE p.Status = 'Pendente'");
    $pagamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erro ao carregar pagamentos: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pagamentoId = $_POST['pagamentoId'];
    
    try {
        $stmt = $pdo->prepare("UPDATE Pagamentos SET Status = 'Pago' WHERE Id = ?");
        $stmt->execute([$pagamentoId]);
        header("Location: baixas_pagamento.php");
        exit;
    } catch (PDOException $e) {
        echo "Erro ao processar pagamento: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Baixa de Pagamentos - Hotel Lux</title>
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
    .hero-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
    }
    .hero-content {
      position: relative;
      z-index: 2;
    }

    /* Adicionando o footer fixo na parte inferior */
    footer {
      margin-top: auto; /* Faz o footer ir para o final da página */
    }

    /* Estilos dos modais */
    .w3-modal-content { border-radius: 10px; }
    .modal-header { font-size: 1.5rem; font-weight: bold; color: #333; }
  </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">Hotel Lux</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="adicionar_quarto.php">Adicionar Quartos</a></li>
                    <li class="nav-item"><a class="nav-link" href="servico_quarto.php">Serviço de Quarto</a></li>
                    <li class="nav-item"><a class="nav-link" href="baixas_pagamento.php">Baixas de Pagamento</a></li>
                    <li class="nav-item"><a class="nav-link" href="perfil.php">Perfil</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Sair</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5 pt-5">
        <h2 class="text-center mb-4">Baixa de Pagamentos</h2>
        
        <h4>Pagamentos Pendentes</h4>
        <?php if (count($pagamentos) > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Valor</th>
                        <th>Status</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pagamentos as $pagamento): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($pagamento['Cliente']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($pagamento['DataCheckIn'])); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($pagamento['DataCheckOut'])); ?></td>
                            <td>R$ <?php echo number_format($pagamento['Valor'], 2, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($pagamento['Status']); ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="pagamentoId" value="<?php echo $pagamento['Id']; ?>">
                                    <button type="submit" class="btn btn-success btn-sm">Confirmar Pagamento</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nenhum pagamento pendente.</p>
        <?php endif; ?>
    </div>

    <!-- Footer -->
  <footer class="bg-dark text-white py-4">
    <div class="container text-center">
      <p class="mb-0">&copy; 2025 Hotel Lux. Todos os direitos reservados.</p>
    </div>
  </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
