<?php
session_start();
include 'dbHotel.php';

// Verificar se o usuário está logado e se o tipo é 'hotel'
if (!isset($_SESSION['usuarioId']) || $_SESSION['usuarioTipo'] !== 'hotel') {
    header("Location: index.php");
    exit;
}

$usuarioId = $_SESSION['usuarioId'];

// Buscar pedidos de serviço de quarto do cliente
$pedidos = [];
try {
    $stmt = $pdo->prepare("SELECT s.Id, s.Descricao, s.Status, s.DataSolicitacao, s.NumeroQuarto, s.TaxaCusto 
                           FROM ServicoQuarto s 
                           WHERE s.ClienteId = ? 
                           ORDER BY s.DataSolicitacao DESC");
    $stmt->execute([$usuarioId]);
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erro ao carregar pedidos: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descricao = $_POST['descricao'];
    $numeroQuarto = $_POST['numero_quarto'];
    $taxaCusto = isset($_POST['taxa_custo']) ? 1 : 0; // Verifica se a taxa foi marcada
    
    try {
        $stmt = $pdo->prepare("INSERT INTO ServicoQuarto (ClienteId, Descricao, Status, DataSolicitacao, NumeroQuarto, TaxaCusto) 
                               VALUES (?, ?, 'Pendente', NOW(), ?, ?)");
        $stmt->execute([$usuarioId, $descricao, $numeroQuarto, $taxaCusto]);
        header("Location: servico_quarto.php");
        exit;
    } catch (PDOException $e) {
        echo "Erro ao solicitar serviço: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Serviço de Quarto - Hotel Lux</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">]
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
                    <li class="nav-item"><a class="nav-link" href="perfil.php">Perfil</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Sair</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5 pt-5">
        <h2 class="text-center mb-4">Solicitar Serviço de Quarto</h2>
        <form method="POST" class="mb-4">
            <div class="mb-3">
                <label class="form-label">Descrição do Serviço</label>
                <textarea name="descricao" class="form-control" required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Número do Quarto</label>
                <input type="text" name="numero_quarto" class="form-control" required>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" name="taxa_custo">
                <label class="form-check-label">Haverá uma taxa de custo adicional?</label>
            </div>

            <button type="submit" class="btn btn-primary">Solicitar</button>
        </form>

        <h4>Meus Pedidos</h4>
        <?php if (count($pedidos) > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Descrição</th>
                        <th>Status</th>
                        <th>Data da Solicitação</th>
                        <th>Número do Quarto</th>
                        <th>Taxa de Custo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pedidos as $pedido): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($pedido['Descricao']); ?></td>
                            <td><?php echo htmlspecialchars($pedido['Status']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($pedido['DataSolicitacao'])); ?></td>
                            <td><?php echo htmlspecialchars($pedido['NumeroQuarto']); ?></td>
                            <td><?php echo $pedido['TaxaCusto'] ? 'Sim' : 'Não'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nenhum pedido de serviço encontrado.</p>
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



        <!------ MOSTRAR OS PEDIDOS QUE FOI FEITO PROS SERVIÇOS DE QUARTO---------->