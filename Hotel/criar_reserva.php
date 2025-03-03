<?php
session_start();

if (!isset($_SESSION['usuarioId'])) {
    header("Location: index.php");
    exit;
}

$usuarioId = $_SESSION['usuarioId'];
$usuarioTipo = $_SESSION['usuarioTipo'];

if ($usuarioTipo !== 'hotel') {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cadastrar Reserva - Hotel Lux</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
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
    <?php include("./components/navbar.php"); ?>
    <?php include("./components/modal_perfil.php"); ?>

    <button class="btn btn-outline-dark" onclick="w3_openSidebar()">☰ Abrir Menu</button>
    
    <div id="mySidebar">
        <button id="sidebarClose" onclick="w3_closeSidebar()">&times;</button>
        <a href="index.php">Início</a>
        <a href="quartos.php">Quartos</a>
        <a href="reservas.php">Reservas</a>
        <a href="perfil.php">Perfil</a>
    </div>

    <div class="container mt-5 pt-5">
        <h1 class="text-center">Cadastrar Reserva</h1>
        <div class="card p-4">
            <form method="POST" action="processar_reserva.php">
                <div class="mb-3">
                    <label for="cliente" class="form-label">Nome do Cliente</label>
                    <input type="text" class="form-control" id="cliente" name="cliente" required>
                </div>
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="quarto" class="form-label">Número do Quarto</label>
                        <input type="text" class="form-control" id="quarto" name="quarto" required>
                    </div>
                    <div class="col-md-6">
                        <label for="tipo_quarto" class="form-label">Tipo de Quarto</label>
                        <select class="form-control" id="tipo_quarto" name="tipo_quarto" required>
                            <option value="solteiro">Solteiro</option>
                            <option value="casal">Casal</option>
                            <option value="luxo">Luxo</option>
                            <option value="presidencial">Presidencial</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="data_checkin" class="form-label">Data de Check-in</label>
                        <input type="date" class="form-control" id="data_checkin" name="data_checkin" required>
                    </div>
                    <div class="col-md-6">
                        <label for="data_checkout" class="form-label">Data de Check-out</label>
                        <input type="date" class="form-control" id="data_checkout" name="data_checkout" required>
                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="valor" class="form-label">Valor da Reserva</label>
                        <input type="text" class="form-control" id="valor" name="valor" required>
                    </div>
                    <div class="col-md-6">
                        <label for="pension" class="form-label">Tipo de Pensão</label>
                        <select class="form-control" id="pension" name="pension" required>
                            <option value="completa">Pensão Completa</option>
                            <option value="meia">Meia Pensão</option>
                            <option value="cafe">Café da Manhã</option>
                            <option value="nenhuma">Sem Pensão</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="obs" class="form-label">Observações</label>
                    <textarea class="form-control" id="obs" name="obs" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-success">Cadastrar Reserva</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function w3_openSidebar() {
            document.getElementById("mySidebar").style.left = "0";
        }

        function w3_closeSidebar() {
            document.getElementById("mySidebar").style.left = "-250px";
        }
    </script>
</body>
</html>
