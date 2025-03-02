<?php

if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

/*if (!isset($_SESSION['usuarioId']) || $_SESSION['usuarioTipo'] !== 'hotel') {
  header("Location: index.php");
  exit;
}*/

$usuarioId = $_SESSION['usuarioId'];
$usuarioTipo = $_SESSION['usuarioTipo']; // 'cliente' ou 'hotel'

?>

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
            <a href="adicionar_quarto.php">Adicionar Quartos</a>
            <a href="servico_quarto.php">Serviço de Quarto</a>
            <a href="baixas_pagamento.php">Baixas de Pagamento</a>
            <a href="funcionarios.php">Cadastrar Funcionário</a>
            <a href="criar_reserva.php">Cadastrar Reserva</a>
        <?php endif; ?>
    </div>
