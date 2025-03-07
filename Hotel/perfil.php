<?php
session_start();
include 'dbHotel.php';

if (!isset($_SESSION['usuarioId'])) {
    header("Location: index.php");
    exit;
}

/*$usuarioId = $_SESSION['usuarioId'];
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
}*/
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Reservas</title>
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
    <!-- <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"> Pousada Mazin</a>
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
        <?php //if ($usuarioTipo === 'cliente'): 
        ?>
            <a href="quartos.php">Fazer Reserva</a>
        <?php //else: 
        ?>
            <a href="adicionar_quarto.php">Adicionar Quartos</a>
            <a href="servico_quarto.php">Serviço de Quarto</a>
            <a href="baixas_pagamento.php">Baixas de Pagamento</a>
            <a href="funcionarios.php">Cadastrar Funcionário</a>
        <?php //endif; 
        ?>
    </div> -->

    <?php include("./components/navbar.php"); ?>

    <!-- Modal do Perfil -->
    <!-- <div class="modal fade" id="perfilModal" tabindex="-1" aria-labelledby="perfilModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="perfilModalLabel">Meu Perfil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Nome:</strong> <?php //echo htmlspecialchars($usuario['Nome']); 
                                                ?></p>
                    <p><strong>Email:</strong> <?php //echo htmlspecialchars($usuario['Email']); 
                                                ?></p>
                    <p><strong>Tipo de Usuário:</strong> <?php //echo $usuarioTipo === 'cliente' ? 'Cliente' : 'Hotel'; 
                                                            ?></p>
                </div>
                <div class="modal-footer">
                    <a href="logout.php" class="btn btn-danger">Sair</a>
                </div>
            </div>
        </div>
    </div> -->

    <?php include("./components/modal_perfil.php"); ?>

    <div class="modal fade" id="reservaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Reservar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="quartos.php" method="POST">
                        <input type="hidden" name="quarto_id" id="quarto_id">

                        <!-- Campos de Nome e Email -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nome:</label>
                                <input type="text" class="form-control" name="name" id="name" required disabled>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email:</label>
                                <input type="email" class="form-control" name="email" id="email" required>
                            </div>
                        </div>

                        <!-- Campos de Data de Check-in e Check-out Desabilitados -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="check_in" class="form-label">Data de Check-in:</label>
                                <input type="date" class="form-control" name="check_in" id="check_in" required disabled>
                            </div>
                            <div class="col-md-6">
                                <label for="check_out" class="form-label">Data de Check-out:</label>
                                <input type="date" class="form-control" name="check_out" id="check_out" required disabled>
                            </div>
                        </div>

                        <!-- Campos de Número do Quarto e Tipo de Quarto -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="numero_quarto" class="form-label">Número do Quarto:</label>
                                <input type="text" class="form-control" name="numero_quarto" id="numero_quarto" required>
                            </div>
                            <div class="col-md-6">
                                <label for="tipo_quarto" class="form-label">Tipo do Quarto:</label>
                                <input type="text" class="form-control" name="tipo_quarto" id="tipo_quarto" required>
                            </div>
                        </div>

                        <!-- Campo de Valor -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="valor" class="form-label">Valor:</label>
                                <input type="number" class="form-control" name="valor" id="valor" required>
                            </div>
                            <div class="col-md-6">
                                <label for="valor" class="form-label">Tipo de Pensão:</label>
                                <select class="form-control" id="pension" name="pension" required>
                                    <option value="completa">Pensão Completa</option>
                                    <option value="meia">Meia Pensão</option>
                                    <option value="cafe">Café da Manhã</option>
                                    <option value="nenhuma">Sem Pensão</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Salvar Reserva</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<!-- Modal de Finalizar Reserva -->
<div class="modal fade" id="finalizarModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Finalização da Reserva</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Você tem certeza que deseja finalizar esta reserva?</p>
                <p><strong>Nome do Cliente:</strong> <span id="clienteNome"></span></p>
                <p><strong>Data de Check-out:</strong> <span id="checkOutData"></span></p>
                <p><strong>Quarto:</strong> <span id="numeroQuarto"></span></p>

                <!-- Div para o QR Code Pix -->
                <div id="qrcode" style="text-align: center;"></div>
                <p style="text-align: center; margin-top: 10px;">Escaneie o QR Code para realizar o pagamento via Pix</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmarFinalizacao" onclick="finalizarReserva()">Finalizar</button>
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

        <div class="p">
            <button class="btn btn-success mb-3" onclick="window.location.href='criar_reserva.php'">Cadastrar Reserva</button>
        </div>


        <!-- Tabela de Quartos -->
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th class="d-flex align-items-center">
                        <input type="checkbox" id="selectAll" class="me-2"> Ações
                        <select id="statusGlobal" class="form-select form-select-sm ms-2 w-auto">
                            <option value="">Selecionar status</option>
                            <option value="disponivel">Liberado</option>
                            <option value="ocupado">Ocupado</option>
                            <option value="manutencao">Manutenção</option>
                        </select>
                        <button class="btn btn-sm btn-primary ms-2" onclick="aplicarStatusGlobal()">Aplicar</button>
                    </th>
                    <th>Quarto</th>
                    <th>Tipo</th>
                    <th>Preço</th>
                    <th>Status</th>
                    <th>Cliente</th>
                    <th>Data Check-in</th>
                    <th>Data Check-out</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <input type="checkbox" class="selectItem" value="1">
                    </td>
                    <td>101</td>
                    <td>Duplo</td>
                    <td>R$ 200,00</td>
                    <td>Disponível</td>
                    <td>João Silva</td>
                    <td>01/03/2025</td>
                    <td>05/03/2025</td>
                    <td>
                        <button class="btn btn-sm btn-success ms-2" onclick="window.location.href='servico_quarto.php'">Serviço de Quarto</button>
                        <button class="btn btn-sm btn-secondary ms-2" onclick="abrirModalEdicao('1', '101', 'Duplo', '200,00', '2025-03-01', '2025-03-05')">Editar</button>
                        <button class="btn btn-sm btn-danger ms-2" onclick="window.location.href='qr.php'">Finalizar</button>
                    </td>
                </tr>
                <?php //foreach ($quartos as $quarto): ?>
                    <!--<tr>
                        <td>
                            <input type="checkbox" class="selectItem" value="<?php echo $quarto['QuartoId']; ?>">
                        </td>
                        <td><?php //echo htmlspecialchars($quarto['numero']); ?></td>
                        <td><?php //echo htmlspecialchars($quarto['tipo']); ?></td>
                        <td>R$ <?php //echo number_format($quarto['preco'], 2, ',', '.'); ?></td>
                        <td><?php //echo htmlspecialchars($quarto['status']); ?></td>
                        <td><?php //echo $quarto['ClienteNome'] ? htmlspecialchars($quarto['ClienteNome']) : 'Nenhum'; ?></td>
                        <td><?php //echo !empty($quarto['data_checkin']) ? date('d/m/Y', strtotime($quarto['data_checkin'])) : 'N/A'; ?></td>
                        <td><?php //echo !empty($quarto['data_checkout']) ? date('d/m/Y', strtotime($quarto['data_checkout'])) : 'N/A'; ?></td>
                        <td><button class="btn btn-sm btn-primary ms-2" onclick="aplicarStatusGlobal()">Serviço de Quarto</button> <button class="btn btn-sm btn-primary ms-2" onclick="aplicarStatusGlobal()">Editar</button></td>
                    </tr> -->
                <?php //endforeach; ?>
            </tbody>
        </table>
    </div>
    </div>
    <!-- Footer -->
    <footer class="bg-dark text-white py-4 text-center">
        <p>&copy; 2025 Pousada Mazin. Todos os direitos reservados.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>


    <script>
        function w3_openSidebar() {
            document.getElementById("mySidebar").style.left = "0";
        }

        function w3_closeSidebar() {
            document.getElementById("mySidebar").style.left = "-250px";
        }

        document.getElementById('selectAll')?.addEventListener('change', function() {
            let checkboxes = document.querySelectorAll('.selectItem');
            checkboxes.forEach(checkbox => checkbox.checked = this.checked);
        });

        function aplicarStatusGlobal() {
            let status = document.getElementById('statusGlobal').value;
            if (!status) {
                alert("Selecione um status válido.");
                return;
            }

            let checkboxes = document.querySelectorAll('.selectItem:checked');
            if (checkboxes.length === 0) {
                alert("Selecione pelo menos um quarto.");
                return;
            }

            let ids = Array.from(checkboxes).map(checkbox => checkbox.value);
            window.location.href = `baixas_quarto.php?id=${ids.join(',')}&acao=${status}`;
        }

        function abrirModalEdicao(id, numero, tipo, preco, checkin, checkout) {
            document.getElementById('quarto_id').value = id;
            document.getElementById('name').value = "João Silva"; // Nome do cliente
            document.getElementById('email').value = "joao.silva@email.com"; // Email do cliente
            document.getElementById('check_in').value = checkin;
            document.getElementById('check_out').value = checkout;

            // Abrir o modal
            let reservaModal = new bootstrap.Modal(document.getElementById('reservaModal'));
            reservaModal.show();
        }

        function abrirModalFinalizacao(id, nome, checkout, quarto) {
    // Preencher os dados no modal
    document.getElementById('clienteNome').textContent = nome;
    document.getElementById('checkOutData').textContent = checkout;
    document.getElementById('numeroQuarto').textContent = quarto;

    // Gerar QR Code Pix
    var chavePix = 'seu@pix.com.br'; // Substitua pela sua chave Pix (ex: CPF, CNPJ, e-mail, telefone)
    var valor = 100.00;  // Valor da reserva, em R$
    var descricao = 'Pagamento reserva hotel';  // Descrição do pagamento
    var nomeBeneficiario = 'Hotel XYZ';  // Nome do beneficiário
    var cidade = 'Cidade ABC';  // Cidade do beneficiário

    // Montar o código Pix (Payload)
    var payload = `00020126580014BR.GOV.BCB.PIX0114${chavePix}520400005303986540${valor.toFixed(2).replace('.', '')}5802BR5915${nomeBeneficiario}6009${cidade}62130505${descricao}6304`; 

    // Gerar QR Code com o Payload Pix
    var qrcodeContainer = document.getElementById('qrcode');
    qrcodeContainer.innerHTML = ""; // Limpar QR Code anterior
    QRCode.toCanvas(qrcodeContainer, payload, function(error) {
        if (error) console.error(error);
    });

    // Abrir o modal
    let finalizarModal = new bootstrap.Modal(document.getElementById('finalizarModal'));
    finalizarModal.show();
}

function finalizarReserva() {
    // Aqui você pode adicionar a lógica para finalizar a reserva
    // Por exemplo, enviar uma requisição para o servidor para atualizar o status da reserva

    
    // Fechar o modal após finalizar
    let finalizarModal = bootstrap.Modal.getInstance(document.getElementById('finalizarModal'));
    finalizarModal.hide();
}

    </script>

</body>

</html>
