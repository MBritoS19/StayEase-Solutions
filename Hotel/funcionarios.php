<?php
session_start();
include 'dbHotel.php';

if (!isset($_SESSION['usuarioId'])) {
    header("Location: index.php");
    exit;
}

$usuarioId = $_SESSION['usuarioId'];
$usuarioTipo = $_SESSION['usuarioTipo'];

// Obter os dados do usuário logado
if (isset($usuarioId)) {
    $stmt = $pdo->prepare("SELECT Nome, Email FROM Usuarios WHERE id = ?");
    $stmt->execute([$usuarioId]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($usuarioTipo !== 'hotel') {
    header("Location: index.php");
    exit;
}

$funcionarios = [];
$termo = "";

if (isset($_GET['pesquisa']) && !empty($_GET['pesquisa'])) {
    $termo = trim($_GET['pesquisa']);
    $stmt = $pdo->prepare("SELECT id, nome, email, cargo, telefone FROM Funcionarios WHERE nome LIKE ? OR cargo LIKE ? OR email LIKE ?");
    $stmt->execute(["%$termo%", "%$termo%", "%$termo%"]);
} else {
    $stmt = $pdo->prepare("SELECT id, nome, email, cargo, telefone FROM Funcionarios");
    $stmt->execute();
}
$funcionarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'cadastrar') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); // Aplicando hash na senha
    $cargo = $_POST['cargo'];
    $telefone = $_POST['telefone'];

    try {
        $stmt = $pdo->prepare("INSERT INTO Funcionarios (nome, email, senha, cargo, telefone) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $email, $senha, $cargo, $telefone]);
        header("Location: funcionarios.php");
    } catch (PDOException $e) {
        echo "Erro ao cadastrar funcionário: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Funcionários - Hotel Lux</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">>
    <style>
         body, html {
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
    <?php //if ($usuarioTipo === 'cliente'): ?>
        <a href="quartos.php">Fazer Reserva</a>
    <?php //else: ?>
        <a href="perfil.php">Gerenciar</a>
        <a href="adicionar_quarto.php">Adicionar Quartos</a>
        <a href="servico_quarto.php">Serviço de Quarto</a>
        <a href="baixas_pagamento.php">Baixas de Pagamento</a>
        <a href="funcionarios.php">Cadastrar Funcionário</a>
    <?php //endif; ?>
</div> -->

<!-- Modal do Perfil -->
<!-- <div class="modal fade" id="perfilModal" tabindex="-1" aria-labelledby="perfilModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="perfilModalLabel">Meu Perfil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p><strong>Nome:</strong> <?php //echo htmlspecialchars($usuario['Nome']); ?></p>
                <p><strong>Email:</strong> <?php //echo htmlspecialchars($usuario['Email']); ?></p>
                <p><strong>Tipo de Usuário:</strong> <?php //echo $usuarioTipo === 'cliente' ? 'Cliente' : 'Hotel'; ?></p>
            </div>
            <div class="modal-footer">
                <a href="logout.php" class="btn btn-danger">Sair</a>
            </div>
        </div>
    </div>
</div>-->

<?php include("./components/navbar.php"); ?>

<?php include("./components/modal_perfil.php"); ?>

    <div class="container mt-5 pt-5">
        <h1 class="text-center">Funcionários do Hotel Lux</h1>
        
        <div class="card p-4">
            <h4>Lista de Funcionários</h4>
            <form method="GET" action="funcionarios.php" class="mb-3">
                <div class="input-group">
                    <input type="text" class="form-control" name="pesquisa" placeholder="Pesquisar funcionário..." value="<?php echo htmlspecialchars($termo); ?>">
                    <button class="btn btn-primary" type="submit">Buscar</button>
                </div>
            </form>
            <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalCadastrarFuncionario">Cadastrar Funcionário</button>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Cargo</th>
                        <th>Telefone</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($funcionarios as $funcionario): ?>
                        <tr>
                            <td><?php echo $funcionario['id']; ?></td>
                            <td><?php echo htmlspecialchars($funcionario['nome']); ?></td>
                            <td><?php echo htmlspecialchars($funcionario['email']); ?></td>
                            <td><?php echo htmlspecialchars($funcionario['cargo']); ?></td>
                            <td><?php echo htmlspecialchars($funcionario['telefone']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modalCadastrarFuncionario" tabindex="-1" aria-labelledby="modalCadastrarFuncionarioLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCadastrarFuncionarioLabel">Cadastrar Novo Funcionário</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="funcionarios.php">
                        <input type="hidden" name="acao" value="cadastrar">
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome</label>
                            <input type="text" class="form-control" id="nome" name="nome" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="cargo" class="form-label">Cargo</label>
                            <input type="text" class="form-control" id="cargo" name="cargo" required>
                        </div>
                        <div class="mb-3">
                            <label for="senha" class="form-label">Senha</label>
                            <input type="text" class="form-control" id="senha" name="senha" required>
                        </div>
                        <div class="mb-3">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input type="text" class="form-control" id="telefone" name="telefone" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                            <button type="submit" class="btn btn-primary">Cadastrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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
</script>
</body>
</html>
