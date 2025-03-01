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

$reservas = [];
$quartos = [];

if ($usuarioTipo === 'cliente') {
    // Carregar reservas do cliente
    try {
        $stmt = $pdo->prepare("SELECT r.Id, r.DataCheckIn, r.DataCheckOut, q.numero AS Quarto, u.Nome AS Cliente
                               FROM Reservas r
                               JOIN Quartos q ON r.quarto_id = q.id
                               JOIN Usuarios u ON r.usuario_id = u.id
                               WHERE r.usuario_id = ?");
        $stmt->execute([$usuarioId]);
        $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erro ao carregar reservas: " . $e->getMessage();
    }
} elseif ($usuarioTipo === 'hotel') {
    // Carregar todos os quartos com informações de reservas e nome do cliente
    try {
        $stmt = $pdo->prepare("SELECT q.id AS QuartoId, q.numero, q.tipo, q.preco, q.status, u.Nome AS ClienteNome
                               FROM Quartos q
                               LEFT JOIN Reservas r ON q.id = r.quarto_id
                               LEFT JOIN Usuarios u ON r.usuario_id = u.id");
        $stmt->execute();
        $quartos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Erro ao buscar quartos: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Perfil - Hotel Lux</title>
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
          <?php if ($usuarioTipo === 'cliente'): ?>
            <li class="nav-item"><a class="nav-link" href="quartos.php">Fazer Reserva</a></li>
          <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="adicionar_quarto.php">Adicionar Quartos</a></li>
            <li class="nav-item"><a class="nav-link" href="servico_quarto.php">Serviço de Quarto</a></li>
            <li class="nav-item"><a class="nav-link" href="baixas_pagamento.php">Baixas de Pagamento</a></li>
            <li class="nav-item"><a class="nav-link" href="funcionarios.php">Cadastrar Funcionário</a></li>
          <?php endif; ?>
          <li class="nav-item"><a class="nav-link" href="logout.php">Sair</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Page Container -->
  <div class="w3-container w3-content" style="max-width:1400px;margin-top:80px">
    <!-- The Grid -->
    <div class="w3-row">
      <!-- Left Column -->
      <div class="w3-col m3">
        <!-- Profile -->
        <div class="w3-card w3-round w3-white">
          <div class="w3-container">
            <h4 class="w3-center">Meu Perfil</h4>
            <p class="w3-center">
              <img src="/w3images/avatar3.png" class="w3-circle" style="height:106px;width:106px" alt="Avatar">
            </p>
            <hr>
            <p><i class="fa fa-user fa-fw w3-margin-right w3-text-theme"></i> Nome: <?php echo htmlspecialchars($usuario['Nome']); ?></p>
            <p><i class="fa fa-envelope fa-fw w3-margin-right w3-text-theme"></i> Email: <?php echo htmlspecialchars($usuario['Email']); ?></p>
          </div>
        </div>
        <br>
      </div>

      <!-- Right Column -->
      <div class="w3-col m9">
        <!-- Exibir Reservas ou Quartos -->
        <?php if ($usuarioTipo === 'cliente'): ?>
          <div class="card p-4">
            <h4>Minhas Reservas</h4>
            <?php if (count($reservas) > 0): ?>
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>Quarto</th>
                    <th>Nome do Cliente</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($reservas as $reserva): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($reserva['Quarto']); ?></td>
                      <td><?php echo htmlspecialchars($reserva['Cliente']); ?></td>
                      <td><?php echo date('d/m/Y', strtotime($reserva['DataCheckIn'])); ?></td>
                      <td><?php echo date('d/m/Y', strtotime($reserva['DataCheckOut'])); ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            <?php else: ?>
              <p>Nenhuma reserva encontrada.</p>
            <?php endif; ?>
          </div>
        <?php else: ?>
          <div class="card p-4">
            <h4>Gerenciar Quartos</h4>
            <table class="table table-bordered table-hover">
              <thead class="table-dark">
                <tr>
                  <th>Quarto</th>
                  <th>Tipo</th>
                  <th>Preço</th>
                  <th>Status</th>
                  <th>Cliente</th>
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
                    <td>
                      <?php if ($quarto['status'] !== 'Em Manutenção'): ?>
                        <a href="baixas_quarto.php?id=<?php echo $quarto['QuartoId']; ?>" class="btn btn-success btn-sm">Liberar Quarto</a>
                        <a href="baixas_quarto.php?id=<?php echo $quarto['QuartoId']; ?>&acao=manutencao" class="btn btn-warning btn-sm">Colocar em Manutenção</a>
                      <?php else: ?>
                        <span class="badge bg-warning">Em Manutenção</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>
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
