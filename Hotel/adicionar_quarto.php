<?php
session_start();
include('dbHotel.php'); 

// Verifica se o usuário está logado e tem permissão para adicionar quartos
if (!isset($_SESSION['usuarioId']) || $_SESSION['usuarioTipo'] !== 'hotel') {
    header("Location: login.php");
    exit;
}

$usuarioTipo = $_SESSION['usuarioTipo'] ?? null; // Define a variável corretamente
$usuario = null; // Inicializa a variável para evitar erros

// Obtém os dados do usuário logado
$usuarioId = $_SESSION['usuarioId'];

try {
    $stmt = $pdo->prepare("SELECT Nome, Email FROM usuarios WHERE id = ?");
    $stmt->execute([$usuarioId]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $erro = "Erro ao buscar informações do usuário: " . $e->getMessage();
}

// Processa o formulário de adição de quarto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero = $_POST['numero'];
    $tipo = $_POST['tipo'];
    $preco = $_POST['preco'];
    $descricao = $_POST['descricao'];
    $imagem = '';

    // Verifica se um arquivo foi enviado
    if (!empty($_FILES['imagem']['name'])) {
        $diretorioDestino = "uploads/";
        if (!is_dir($diretorioDestino)) {
            mkdir($diretorioDestino, 0777, true);
        }

        $nomeArquivo = time() . "_" . basename($_FILES['imagem']['name']);
        $caminhoArquivo = $diretorioDestino . $nomeArquivo;

        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminhoArquivo)) {
            $imagem = $caminhoArquivo;
        } else {
            $erro = "Erro ao fazer upload da imagem.";
        }
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO quartos (numero, tipo, preco, descricao, imagem) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$numero, $tipo, $preco, $descricao, $imagem]);
        $sucesso = "Quarto adicionado com sucesso!";
    } catch (PDOException $e) {
        $erro = "Erro ao adicionar quarto: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Adicionar Quarto - Hotel Lux</title>
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

<!-- Conteúdo Principal -->
<div class="container mt-5">
  <h3 class="mb-4">Adicionar Novo Quarto</h3>

  <?php if (isset($sucesso)): ?>
    <div class="alert alert-success"><?php echo $sucesso; ?></div>
  <?php elseif (isset($erro)): ?>
    <div class="alert alert-danger"><?php echo $erro; ?></div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label class="form-label">Número do Quarto</label>
      <input type="text" name="numero" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Tipo</label>
      <select name="tipo" class="form-select" required>
        <option value="Simples">Simples</option>
        <option value="Duplo">Duplo</option>
        <option value="Luxo">Luxo</option>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Preço por Noite (R$)</label>
      <input type="number" name="preco" step="0.01" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Descrição</label>
      <textarea name="descricao" class="form-control" rows="3" required></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Imagem do Quarto</label>
      <input type="file" name="imagem" class="form-control" accept="image/*">
    </div>
    <button type="submit" class="btn btn-primary">Adicionar Quarto</button>
    <a href="perfil.php" class="btn btn-secondary">Voltar</a>
  </form>
</div>

<!-- Footer -->
<footer class="bg-dark text-white py-4">
    <div class="container text-center">
      <p class="mb-0">&copy; 2025 Hotel Lux. Todos os direitos reservados.</p>
    </div>
  </footer>

<!-- Bootstrap JS -->
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
