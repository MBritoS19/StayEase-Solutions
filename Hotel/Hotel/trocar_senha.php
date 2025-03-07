<?php
include 'dbHotel.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuarioId'])) {
    header("Location: index.php");
    exit;
}

$usuarioId = $_SESSION['usuarioId'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $senhaAtual = trim($_POST['senhaAtual']);
    $novaSenha = trim($_POST['novaSenha']);
    $confirmarSenha = trim($_POST['confirmarSenha']);

    // Verifica se os campos não estão vazios
    if (empty($senhaAtual) || empty($novaSenha) || empty($confirmarSenha)) {
        $_SESSION['mensagem'] = "Todos os campos são obrigatórios!";
        header("Location: perfil.php");
        exit;
    }

    // Verifica se a nova senha tem pelo menos 8 caracteres
    if (strlen($novaSenha) < 8) {
        $_SESSION['mensagem'] = "A nova senha deve ter pelo menos 8 caracteres!";
        header("Location: perfil.php");
        exit;
    }

    // Verifica se a nova senha e a confirmação coincidem
    if ($novaSenha !== $confirmarSenha) {
        $_SESSION['mensagem'] = "A nova senha e a confirmação não coincidem!";
        header("Location: perfil.php");
        exit;
    }

    try {
        // Buscar a senha atual do usuário no banco
        $stmt = $pdo->prepare("SELECT senha FROM usuarios WHERE id = ?");
        $stmt->execute([$usuarioId]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifica se a senha atual está correta
        if (!$usuario || !password_verify($senhaAtual, $usuario['senha'])) {
            $_SESSION['mensagem'] = "Senha atual incorreta!";
            header("Location: perfil.php");
            exit;
        }

        // Verifica se a nova senha é diferente da senha atual
        if (password_verify($novaSenha, $usuario['senha'])) {
            $_SESSION['mensagem'] = "A nova senha não pode ser igual à senha atual!";
            header("Location: perfil.php");
            exit;
        }

        // Atualizar a senha no banco
        $novaSenhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
        $stmt->execute([$novaSenhaHash, $usuarioId]);

        $_SESSION['mensagem'] = "Senha alterada com sucesso!";
        header("Location: perfil.php");
        exit;

    } catch (PDOException $e) {
        $_SESSION['mensagem'] = "Erro ao atualizar senha: " . $e->getMessage();
        header("Location: perfil.php");
        exit;
    }
}
?>
