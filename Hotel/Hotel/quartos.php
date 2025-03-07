<?php
// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hotel";

$conexao = new mysqli($servername, $username, $password, $dbname);
if ($conexao->connect_error) {
  die("Erro na conexão: " . $conexao->connect_error);
}

// Consulta para buscar os quartos disponíveis
$sql = "SELECT * FROM quartos WHERE status = 'disponível'";
$result = $conexao->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Quartos Disponíveis - Pousada Mazin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
    body {
      background: #f8f9fa;
    }

    .navbar-custom {
      background-color: #343a40;
    }

    .hero {
      background: url('https://source.unsplash.com/1600x900/?hotel,room') no-repeat center center;
      background-size: cover;
      height: 298px;
      position: relative;
      color: #fff;
    }

    .hero-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
    }

    .hero-content {
      position: relative;
      z-index: 2;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100%;
    }

    .card-img-top {
      height: 200px;
      object-fit: cover;
    }

    .modal-body img {
      width: 100%;
      height: auto;
      margin-bottom: 15px;
    }

    footer {
      background-color: #343a40;
      color: #fff;
      padding: 15px 0;
      text-align: center;
    }
  </style>
</head>

<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Pousada Mazin</a>
      <div class="collapse navbar-collapse" id="navbarContent">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a href="quartos.php" class="nav-link">Fazer Reserva</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="perfilDropdown" role="button" data-bs-toggle="modal" data-bs-target="#perfilModal">
              <i class="fas fa-user-circle fa-lg"></i>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="modal fade" id="perfilModal" tabindex="-1" aria-labelledby="perfilModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="perfilModalLabel">Meu Perfil</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <div class="modal-body">
          <p><strong>Nome:</strong>Teste</p>
          <p><strong>Email:</strong> cliente@cliente </p>
          <p><strong>Tipo de Usuário:</strong> cliente</p>
        </div>
        <div class="modal-footer">
          <a href="logout.php" class="btn btn-danger">Sair</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Seção Hero -->
  <header class="hero">
    <div class="hero-overlay"></div>
    <div class="hero-content">
      <h1 class="display-4">Quartos Disponíveis</h1>
    </div>
  </header>

  <div class="container my-5">

    <div class="row">
      <div class="col-md-4">
        <div class="card mb-4">
          <img src="uploads/1740695620_teste.jpeg" class="card-img-top" alt="Imagem do Quarto">
          <div class="card-body">
            <div class="d-flex justify-content-between text-center">
              <h5 class="card-title"><strong>Quartos Duplo</strong></h5>
            </div>
            <hr>
            <div class="d-flex justify-content-between">
              <p class="card-text"><strong>Valor da Reserva:</strong><span> R$ 150</span></p>
            </div>
            <div class="d-flex justify-content-between">
              <p class="card-text"><strong>Capacidade:</strong> 2 Adulto</p>
            </div>
            <div class="d-flex justify-content-between">
              <p class="card-text"><strong>Pensão:</strong> Meia</p>
            </div>
            <hr>
            <h6 class="card-title text-secondary d-flex justify-content-center align-items-center">
            <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#reservaModal" 
                        data-quarto-id="<?php echo $quarto['id']; 
                                        ?>"
                        data-quarto-tipo="<?php echo htmlspecialchars($quarto['tipo']); 
                                          ?>"
                        data-quarto-img="<?php echo !empty($quarto['imagem']) ? $quarto['imagem'] : 'img/quarto_padrao.jpg'; 
                                          ?>">Reserva</button>
            </h6>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card mb-4">
          <img src="uploads/1740695620_teste.jpeg" class="card-img-top" alt="Imagem do Quarto">
          <div class="card-body">
            <div class="d-flex justify-content-between text-center">
              <h5 class="card-title"><strong>Quartos Luxo</strong></h5>
            </div>
            <hr>
            <div class="d-flex justify-content-between">
              <p class="card-text"><strong>Valor da Reserva:</strong><span> R$ 200</span></p>
            </div>
            <div class="d-flex justify-content-between">
              <p class="card-text"><strong>Capacidade:</strong> 2 Adulto 2 Crianças</p>
            </div>
            <div class="d-flex justify-content-between">
              <p class="card-text"><strong>Pensão:</strong> Completa</p>
            </div>
            <hr>
            <h6 class="card-title text-secondary d-flex justify-content-center align-items-center">
            <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#reservaModal" 
                        data-quarto-id="<?php echo $quarto['id']; 
                                        ?>"
                        data-quarto-tipo="<?php echo htmlspecialchars($quarto['tipo']); 
                                          ?>"
                        data-quarto-img="<?php echo !empty($quarto['imagem']) ? $quarto['imagem'] : 'img/quarto_padrao.jpg'; 
                                          ?>">Reserva</button>
            </h6>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card mb-4">
          <img src="uploads/1740695620_teste.jpeg" class="card-img-top" alt="Imagem do Quarto">
          <div class="card-body">
            <div class="d-flex justify-content-between text-center">
              <h5 class="card-title"><strong>Quartos Simples</strong></h5>
            </div>
            <hr>
            <div class="d-flex justify-content-between">
              <p class="card-text"><strong>Valor da Reserva:</strong><span> R$ 100</span></p>
            </div>
            <div class="d-flex justify-content-between">
              <p class="card-text"><strong>Capacidade:</strong> 1 Adulto</p>
            </div>
            <div class="d-flex justify-content-between">
              <p class="card-text"><strong>Pensão:</strong> Café da Manhã</p>
            </div>
            <hr>
            <h6 class="card-title text-secondary d-flex justify-content-center align-items-center">
            <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#reservaModal" 
                        data-quarto-id="<?php echo $quarto['id']; 
                                        ?>"
                        data-quarto-tipo="<?php echo htmlspecialchars($quarto['tipo']); 
                                          ?>"
                        data-quarto-img="<?php echo !empty($quarto['imagem']) ? $quarto['imagem'] : 'img/quarto_padrao.jpg'; 
                                          ?>">Reserva</button>
            </h6>
          </div>
        </div>
      </div>

    </div>

    <!-- <div class="row">
      <?php //if ($result->num_rows > 0): 
      ?>
        <?php //while ($quarto = $result->fetch_assoc()): 
        ?>
          <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
              <img src="./uploads/1740695620_teste.jpeg <?php //echo !empty($quarto['imagem']) ? $quarto['imagem'] : 'img/quarto_padrao.jpg'; 
                                                        ?>" class="card-img-top" alt="Imagem do Quarto">
              <div class="card-body">
                <h5 class="card-title"><?php //echo htmlspecialchars($quarto['tipo']); 
                                        ?></h5>
                <p class="card-text"><strong>Número:</strong> <?php //echo htmlspecialchars($quarto['numero']); 
                                                              ?></p>
                <p class="card-text"><strong>Preço:</strong> R$ <?php //echo number_format($quarto['preco'], 2, ',', '.'); 
                                                                ?></p>
                <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#reservaModal" 
                        data-quarto-id="<?php //echo $quarto['id']; 
                                        ?>"
                        data-quarto-tipo="<?php //echo htmlspecialchars($quarto['tipo']); 
                                          ?>"
                        data-quarto-img="<?php //echo !empty($quarto['imagem']) ? $quarto['imagem'] : 'img/quarto_padrao.jpg'; 
                                          ?>">
                  Reservar
                </button>
              </div>
            </div>
          </div>
        <?php //endwhile; 
        ?>
      <?php //else: 
      ?>
        <div class="col-12 text-center">
          <p>Nenhum quarto disponível no momento.</p>
        </div>
      <?php //endif; 
      ?>
    </div> -->
  </div>

  <div class="modal fade" id="reservaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Reservar Quarto</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="d-flex justify-content-center">
            <img src="./uploads/1740695620_teste.jpeg"
                 class="card-img-top w-50"
                 alt="Quarto Executivo com cama king-size e vista panorâmica"
                 loading="lazy">
          </div>
          <form action="quartos.php" method="POST">
            <input type="hidden" name="quarto_id" id="quarto_id">
            <div class="mb-3">
              <label for="name" class="form-label">Nome:</label>
              <input type="text" class="form-control" name="name" id="name" required>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email:</label>
              <input type="email" class="form-control" name="email" id="email" required>
            </div>
            <div class="mb-3">
              <label for="check_in" class="form-label">Data de Check-in:</label>
              <input type="date" class="form-control" name="check_in" id="check_in" required>
            </div>
            <div class="mb-3">
              <label for="check_out" class="form-label">Data de Check-out:</label>
              <input type="date" class="form-control" name="check_out" id="check_out" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Confirmar Reserva</button>
          </form>
        </div>
      </div>
    </div>
</div>


  <!-- Footer -->
  <footer class="bg-dark text-white py-4 text-center">
    <p>&copy; 2025 Pousada Mazin. Todos os direitos reservados.</p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    var reservaModal = document.getElementById('reservaModal');
    reservaModal.addEventListener('show.bs.modal', function(event) {
      var button = event.relatedTarget;
      var quartoId = button.getAttribute('data-quarto-id');
      var quartoImg = button.getAttribute('data-quarto-img');
      document.getElementById('quarto_id').value = quartoId;
      document.getElementById('modal-img').src = quartoImg;
    });
  </script>
</body>

</html>
