
<!-----

PAGINA TESTE DE CADASTRAR O FUNCIONARIO E PESQUISAR O NOME DO FUNCIONARIO PELO CARGO E ETC


------->



<?php
session_start();
include 'dbHotel.php';

if (!isset($_SESSION['usuarioId'])) {
    header("Location: index.php");
    exit;
}

$usuarioId = $_SESSION['usuarioId'];
$usuarioTipo = $_SESSION['usuarioTipo']; // 'cliente' ou 'hotel'

// Verificar se é um administrador para permitir o cadastro de funcionários
if ($usuarioTipo !== 'hotel') {
    header("Location: index.php");
    exit;
}

// Buscar funcionários do hotel
$funcionarios = [];
try {
    $stmt = $pdo->prepare("SELECT id, nome, email, cargo, telefone FROM Funcionarios");
    $stmt->execute();
    $funcionarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erro ao carregar funcionários: " . $e->getMessage();
}

// Adicionar um novo funcionário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'cadastrar') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $cargo = $_POST['cargo'];
    $telefone = $_POST['telefone'];

    try {
        $stmt = $pdo->prepare("INSERT INTO Funcionarios (nome, email, cargo, telefone) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nome, $email, $cargo, $telefone]);
        header("Location: funcionarios.php"); // Redireciona para a página após o cadastro
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
                    <li class="nav-item"><a class="nav-link" href="funcionarios.php">Funcionários</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Sair</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5 pt-5">
        <h1 class="text-center">Funcionários do Hotel Lux</h1>

        <!-- Tabela de Funcionários -->
        <div class="card p-4">
            <h4>Lista de Funcionários</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCadastrarFuncionario">Cadastrar Funcionário</button>
            <table class="table table-striped mt-4">
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

    <!-- Modal para Cadastrar Funcionário -->
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
</body>
</html>
