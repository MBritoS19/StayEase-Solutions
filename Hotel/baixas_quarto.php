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
        // Definir status do quarto com base na ação recebida
        if ($acao === 'manutencao') {
            $stmt = $pdo->prepare("UPDATE Quartos SET status = 'Manutenção' WHERE id = ?");
        } elseif ($acao === 'ocupado') {
            $stmt = $pdo->prepare("UPDATE Quartos SET status = 'Ocupado' WHERE id = ?");
        } else {
            $stmt = $pdo->prepare("UPDATE Quartos SET status = 'Disponível' WHERE id = ?");
        }

        $stmt->execute([$quartoId]);

        if ($stmt->rowCount() > 0) {
            header("Location: perfil.php");
            exit;
        } else {
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
