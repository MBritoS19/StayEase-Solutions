<?php
session_start();
include 'dbHotel.php';

// Verificar se o usuário está logado e se o tipo é 'hotel'
if (!isset($_SESSION['usuarioId']) || $_SESSION['usuarioTipo'] !== 'hotel') {
    header("Location: index.php");
    exit;
}

$usuarioId = $_SESSION['usuarioId'];
$usuarioTipo = $_SESSION['usuarioTipo'];

// Buscar dados do usuário logado
/*try {
    $stmt = $pdo->prepare("SELECT Nome, Email FROM usuarios WHERE Id = ?");
    $stmt->execute([$usuarioId]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        throw new Exception("Usuário não encontrado.");
    }
} catch (Exception $e) {
    echo "Erro ao carregar usuário: " . $e->getMessage();
    exit;
}*/

// Buscar pedidos de serviço de quarto do cliente
$pedidos = [];
try {
    $stmt = $pdo->prepare("SELECT Id, Descricao, Status, DataSolicitacao, NumeroQuarto, TaxaCusto 
                           FROM ServicoQuarto 
                           WHERE ClienteId = ? 
                           ORDER BY DataSolicitacao DESC");
    $stmt->execute([$usuarioId]);
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Depuração: Exibir os pedidos para verificar se há dados
    var_dump($pedidos); // Para ver o conteúdo dos pedidos
} catch (PDOException $e) {
    echo "Erro ao carregar pedidos: " . $e->getMessage();
}

// Adicionar um novo pedido (se houver um POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descricao = $_POST['descricao'];
    $numeroQuarto = $_POST['numero_quarto'];
    $taxaCusto = isset($_POST['taxa_custo']) ? 1 : 0;

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
                <input type="checkbox" class="form-check-input" name="taxa_custo" id="taxaCustoCheckbox">
                <label class="form-check-label">Haverá uma taxa de custo adicional?</label>
            </div>

            <div class="mb-3" id="campoValor" style="display: none;">
                <label class="form-label">Valor do Serviço</label>
                <input type="number" name="valor_taxa" class="form-control" placeholder="Informe o valor" step="0.01">
            </div>

            <button type="submit" class="btn btn-primary">Solicitar</button>
        </form>

        <h4>Meus Pedidos</h4>
        <?php //if (count($pedidos) > 0): 
        ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Descrição</th>
                    <th>Status</th>
                    <th>Data da Solicitação</th>
                    <th>Número do Quarto</th>
                    <th>Taxa de Custo</th>
                    <th>Valor da Taxa</th> <!-- Nova coluna de valor -->
                </tr>
            </thead>
            <tbody>
                <!-- Linha com dados estáticos -->
                <tr>
                    <td>Troca de toalhas</td>
                    <td>
                        <select id="statusGlobal" class="form-select form-select-sm ms-2 w-auto">
                            <option value="pendente" selected>Pendente</option>
                            <option value="em_andamento">Em andamento</option>
                            <option value="concluido">Concluído</option>
                        </select>
                    </td>
                    <td>02/03/2025 14:30</td>
                    <td>101</td>
                    <td>Sim</td>
                    <td>R$ 20,00</td> <!-- Valor da taxa -->
                </tr>

                <?php foreach ($pedidos as $pedido): ?>
                    <tr>
                        <td><?php echo nl2br(htmlspecialchars($pedido['Descricao'])); ?></td>
                        <td><?php echo htmlspecialchars($pedido['Status']); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($pedido['DataSolicitacao'])); ?></td>
                        <td><?php echo htmlspecialchars($pedido['NumeroQuarto']); ?></td>
                        <td><?php echo $pedido['TaxaCusto'] ? 'Sim' : 'Não'; ?></td>
                        <td>
                            <?php
                            // Se o pedido tiver taxa de custo, exibe o valor, senão exibe "N/A"
                            echo $pedido['TaxaCusto'] ? htmlspecialchars($pedido['ValorTaxa']) : 'N/A';
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php //else: 
        ?>
        <p>Nenhum pedido de serviço encontrado.</p>
        <?php //endif; 
        ?>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p class="mb-0">&copy; 2025 Hotel Lux. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function w3_openSidebar() {
            document.getElementById("mySidebar").style.left = "0";
        }

        function w3_closeSidebar() {
            document.getElementById("mySidebar").style.left = "-250px";
        }

        document.getElementById('taxaCustoCheckbox').addEventListener('change', function() {
            var campoValor = document.getElementById('campoValor');
            if (this.checked) {
                campoValor.style.display = 'block'; // Exibe o campo de valor
            } else {
                campoValor.style.display = 'none'; // Esconde o campo de valor
            }
        });
    </script>

</body>

</html>
