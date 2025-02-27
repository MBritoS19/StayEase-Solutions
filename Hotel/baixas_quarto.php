<?php
session_start();
include 'dbHotel.php';

if (!isset($_SESSION['usuarioId'])) {
    header("Location: index.php");
    exit;
}

$usuarioId = $_SESSION['usuarioId'];
$usuarioTipo = $_SESSION['usuarioTipo']; // 'cliente' ou 'hotel'

$quartoId = $_GET['id'] ?? null;
$acao = $_GET['acao'] ?? null;

if ($quartoId && is_numeric($quartoId)) {
    try {
        // Se a ação for 'manutencao', atualizar status para 'Em Manutenção'
        if ($acao === 'manutencao') {
            $stmt = $pdo->prepare("UPDATE Quartos SET status = 'Manutenção' WHERE id = ?");
        } else {
            // Caso contrário, liberar o quarto
            $stmt = $pdo->prepare("UPDATE Quartos SET status = 'Disponível' WHERE id = ?");
        }

        $stmt->execute([$quartoId]);

        // Verifique se a atualização foi bem-sucedida
        if ($stmt->rowCount() > 0) {
            // Sucesso na atualização
            header("Location: perfil.php");
            exit;
        } else {
            // Se não houver alterações, informe o usuário
            echo "Nenhuma alteração foi realizada.";
        }

    } catch (PDOException $e) {
        echo "Erro ao atualizar status do quarto: " . $e->getMessage();
    }
} else {
    echo "ID do quarto inválido.";
    exit;
}
?>
