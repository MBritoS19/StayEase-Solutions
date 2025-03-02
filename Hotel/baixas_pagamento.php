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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body,
        html {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        .hero {
            background: url('https://source.unsplash.com/1600x900/?hotel') no-repeat center center/cover;
            height: 100vh;
            color: #fff;
        }

        footer {
            margin-top: auto;
            text-align: center;
            background: #343a40;
            color: white;
            padding: 15px 0;
        }

        /* Sidebar */
        #mySidebar {
            width: 250px;
            height: 100%;
            position: fixed;
            top: 0;
            left: -250px;
            background-color: #343a40;
            color: white;
            transition: 0.3s;
            padding-top: 20px;
            z-index: 1050;
        }

        #mySidebar a {
            padding: 10px 20px;
            text-decoration: none;
            font-size: 18px;
            color: white;
            display: block;
            transition: 0.3s;
        }

        #mySidebar a:hover {
            background-color: #495057;
        }

        #sidebarClose {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 20px;
            cursor: pointer;
            background: none;
            border: none;
            color: white;
            padding: 10px;
        }

        #sidebarClose:hover {
            background-color: #495057;
            color: #f8f9fa;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"> Hotel Lux</a>
            <button class="btn btn-outline-light me-2" onclick="w3_openSidebar()">☰</button>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="perfilDropdown" role="button" data-bs-toggle="modal" data-bs-target="#perfilModal">
                            <i class="fas fa-user-circle fa-lg"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div id="mySidebar">
        <button class="btn btn-danger w-100" onclick="w3_closeSidebar()">Fechar</button>
        <?php if ($usuarioTipo === 'cliente'): ?>
            <a href="quartos.php">Fazer Reserva</a>
        <?php else: ?>
            <a href="perfil.php">Gerenciar</a>
            <a href="adicionar_quarto.php">Adicionar Quartos</a>
            <a href="servico_quarto.php">Serviço de Quarto</a>
            <a href="baixas_pagamento.php">Baixas de Pagamento</a>
            <a href="funcionarios.php">Cadastrar Funcionário</a>
        <?php endif; ?>
    </div>

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

    <!-- Modal de Baixa -->
    <div class="modal fade" id="modalBaixa" tabindex="-1" aria-labelledby="modalBaixaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalBaixaLabel">Confirmar Baixa de Pagamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza de que deseja dar baixa neste pagamento?</p>
                </div>
                <div class="modal-footer">
                    <form method="POST">
                        <input type="hidden" id="pagamentoId" name="pagamentoId">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Confirmar Baixa</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-5 pt-5">
        <h2 class="text-center mb-4">Baixa de Pagamentos</h2>

        <h4 class="text-center mb-4">Pagamentos Pendentes</h4>
        <form method="GET" action="funcionarios.php" class="mb-3">
            <div class="input-group">
                <input type="text" class="form-control" name="pesquisa" placeholder="Pesquisar Pagamento..." value="">
                <button class="btn btn-primary" type="submit">Buscar</button>
            </div>
        </form>
        <div class="card p-4">
            <table class="table table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Código</th>
                        <th>Nº do Quarto</th>
                        <th>Cliente</th>
                        <th>Tipo do Quarto</th>
                        <th>Tipo de Pagamento</th>
                        <th>Data Baixa</th>
                        <th>Data Recebimento</th>
                        <th>Valor (R$)</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>001</td>
                        <td>102</td>
                        <td>João Silva</td>
                        <td>Duplex</td>
                        <td>Cartão de Crédito</td>
                        <td>28/02/2025</td>
                        <td>27/02/2025</td>
                        <td>350,00</td>
                        <td><button class="btn btn-sm btn-danger ms-2" data-bs-toggle="modal" data-bs-target="#modalBaixa" onclick="setPagamentoId(1)">Baixa</button></td>
                    </tr>
                    <tr>
                        <td>002</td>
                        <td>205</td>
                        <td>Maria Oliveira</td>
                        <td>Simples</td>
                        <td>Pix</td>
                        <td>26/02/2025</td>
                        <td>25/02/2025</td>
                        <td>280,00</td>
                        <td><button class="btn btn-sm btn-danger ms-2" data-bs-toggle="modal" data-bs-target="#modalBaixa" onclick="setPagamentoId(2)">Baixa</button></td>
                    </tr>
                    <tr>
                        <td>003</td>
                        <td>308</td>
                        <td>Carlos Mendes</td>
                        <td>Luxo</td>
                        <td>Boleto</td>
                        <td>24/02/2025</td>
                        <td>23/02/2025</td>
                        <td>500,00</td>
                        <td><button class="btn btn-sm btn-danger ms-2" data-bs-toggle="modal" data-bs-target="#modalBaixa" onclick="setPagamentoId(3)">Baixa</button></td>
                    </tr>
                    <tr>
                        <td>004</td>
                        <td>412</td>
                        <td>Ana Souza</td>
                        <td>Simples</td>
                        <td>Dinheiro</td>
                        <td>22/02/2025</td>
                        <td>21/02/2025</td>
                        <td>150,00</td>
                        <td><button class="btn btn-sm btn-danger ms-2" data-bs-toggle="modal" data-bs-target="#modalBaixa" onclick="setPagamentoId(4)">Baixa</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 Hotel Lux. Todos os direitos reservados.</p>
    </footer>

    <script>
        function setPagamentoId(id) {
            document.getElementById('pagamentoId').value = id;
        }
        
        function w3_openSidebar() {
            document.getElementById("mySidebar").style.left = "0";
        }
        
        function w3_closeSidebar() {
            document.getElementById("mySidebar").style.left = "-250px";
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>

</html>
