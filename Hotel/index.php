<?php
session_start();
include 'dbHotel.php';

$logado = isset($_SESSION['usuarioId']);
$erroLogin = "";
$erroCadastro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        // Processar Login
        $email = trim($_POST['email']);
        $senha = trim($_POST['senha']);

        try {
            $stmt = $pdo->prepare("SELECT Id, Nome, Email, Senha, Tipo FROM Usuarios WHERE Email = ?");
            $stmt->execute([$email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($senha, $usuario['Senha'])) {
                $_SESSION['usuarioId'] = $usuario['Id'];
                $_SESSION['usuarioNome'] = $usuario['Nome'];
                $_SESSION['usuarioTipo'] = $usuario['Tipo']; // 'cliente' ou 'hotel'

                // Redireciona com base no tipo de usuário
                if ($usuario['Tipo'] === 'hotel') {
                    header("Location: perfil.php");
                } else {
                    header("Location: perfil.php");
                }
                exit;
            } else {
                $erroLogin = "Email ou senha inválidos.";
            }
        } catch (PDOException $e) {
            $erroLogin = "Erro ao conectar ao banco de dados.";
        }
    } elseif (isset($_POST['cadastro'])) {
        // Processar Cadastro
        $nome = trim($_POST['nome']);
        $email = trim($_POST['email']);
        $senha = password_hash(trim($_POST['senha']), PASSWORD_DEFAULT);
        $tipo = $_POST['tipo']; // 'cliente' ou 'hotel'

        try {
            $stmt = $pdo->prepare("INSERT INTO Usuarios (Nome, Email, Senha, Tipo) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nome, $email, $senha, $tipo]);

            $_SESSION['mensagem_sucesso'] = "Cadastro realizado com sucesso! Faça login.";
        } catch (PDOException $e) {
            $erroCadastro = "Erro ao cadastrar: " . $e->getMessage();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Hotel Lux</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <style>
    .hero {
      background: url('https://source.unsplash.com/1600x900/?hotel') no-repeat center center;
      background-size: cover;
      height: 100vh;
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
    }

    /* Estilos dos modais */
    .w3-modal-content { border-radius: 10px; }
        .modal-header { font-size: 1.5rem; font-weight: bold; color: #333; }

        .card {
      height: 100%;
      transition: transform var(--transition-speed);
      border: none;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .card:hover {
      transform: translateY(-5px);
    }

    .card-img-top {
      height: 250px;
      object-fit: cover;
    }

    .form-control:focus {
      box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .required-asterisk {
      color: #dc3545;
      margin-left: 3px;
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
    <a class="navbar-brand" href="#"><i class="bi bi-building fs-4 me-2"></i>Hotel Lux</a>      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarContent">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link active" href="#">Início</a></li>
          <li class="nav-item"><a class="nav-link" href="#rooms">Quartos</a></li>
          <li class="nav-item"><a class="nav-link" href="#services">Serviços</a></li>
          <li class="nav-item"><a class="nav-link" href="#about">Sobre</a></li>
          <li class="nav-item"><a class="nav-link" href="#contact">Contato</a></li>
          <li class="nav-item">
            <?php if ($logado): ?>
                <a class="nav-link text-white" href="perfil.php"><i class="bi bi-person-circle me-2"></i>Perfil</a>
            <?php else: ?>
                <button onclick="document.getElementById('loginModal').style.display='block'" class="w3-button w3-blue w3-round"><i class="bi bi-box-arrow-in-right me-2"></i>Login</button>
           <?php endif; ?>
        </li>
        </ul>
      </div>
    </div>
  </nav>

<!-- Modal de Login -->
<div id="loginModal" class="w3-modal">
    <div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px">
        <div class="w3-center"><br>
            <span onclick="document.getElementById('loginModal').style.display='none'" class="w3-button w3-xlarge w3-transparent w3-display-topright">×</span>
            <img src="img/igreja.jpg" alt="Avatar" style="width:30%" class="w3-circle w3-margin-top">
        </div>

        <!-- Formulário de Login -->
        <form class="w3-container" method="POST">
            <input type="hidden" name="login">
            <div class="w3-section">
                <label><b>Email</b></label>
                <input class="w3-input w3-border w3-margin-bottom" type="email" name="email" required>
                
                <label><b>Senha</b></label>
                <input class="w3-input w3-border" type="password" name="senha" required>
                
                <button class="w3-button w3-block w3-blue w3-section w3-padding w3-round" type="submit">Entrar</button>
                
                <p>Não possui um login? <a href="#" onclick="abrirCadastro()">Registrar</a></p>
            </div>

            <?php if (!empty($erroLogin)): ?>
                <div class="w3-panel w3-red w3-padding"><?php echo $erroLogin; ?></div>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Modal de Cadastro -->
<div id="cadastroModal" class="w3-modal">
    <div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px">
        <div class="w3-center"><br>
            <span onclick="document.getElementById('cadastroModal').style.display='none'" class="w3-button w3-xlarge w3-transparent w3-display-topright">×</span>
        </div>

        <div class="w3-container text-center">
            <h3>Crie sua conta conosco</h3>
            <p>Preencha os campos abaixo para se cadastrar e ter acesso aos nossos serviços.</p>
        </div>

        <form class="w3-container" method="POST">
            <input type="hidden" name="cadastro">
            <div class="w3-section">
                <label><b>Nome</b></label>
                <input class="w3-input w3-border" type="text" name="nome" required>

                <label><b>Email</b></label>
                <input class="w3-input w3-border" type="email" name="email" required>

                <label><b>Senha</b></label>
                <input class="w3-input w3-border" type="password" name="senha" required>

                <label><b>Tipo de Conta</b></label>
                <select class="w3-input w3-border" name="tipo" required>
                    <option value="cliente">Cliente</option>
                    <option value="hotel">Hotel</option>
                </select>

                <button class="w3-button w3-block w3-blue w3-section w3-padding w3-round" type="submit">Cadastrar</button>
            </div>
            
            <?php if (!empty($erroCadastro)): ?>
                <div class="w3-panel w3-red w3-padding"><?php echo $erroCadastro; ?></div>
            <?php endif; ?>
        </form>
    </div>
</div>

  <!-- Hero Section -->
  <header class="hero d-flex align-items-center">
    <div class="hero-overlay"></div>
    <div class="container hero-content text-center">
      <h1 class="display-4 fw-bold">Bem-vindo ao Hotel Lux</h1>
      <p class="lead">Sua estadia de luxo e conforto em um ambiente sofisticado</p>
      <a href="quartos.php" class="btn btn-primary btn-lg mt-3"><i class="bi bi-calendar-check me-2"></i>Reservar Agora</a>
    </div>
  </header>

  <!-- Seção de Quartos -->
  <section id="rooms" class="py-5">
    <div class="container">
      <div class="text-center mb-5">
        <h2>Nossos Quartos</h2>
        <p>Experimente o melhor em conforto e elegância</p>
      </div>
      <div class="row">
<!-- Quarto Deluxe -->
<div class="col-lg-4 col-md-6">
          <div class="card h-100">
            <img src="https://source.unsplash.com/600x400/?luxury-room"
              class="card-img-top"
              alt="Quarto Deluxe com cama king-size e vista panorâmica"
              loading="lazy">
            <div class="card-body d-flex flex-column">
              <h3 class="h5 card-title">Deluxe Room</h3>
              <p class="card-text text-muted">32m² • Vista para o mar • Wi-Fi premium</p>
              <ul class="list-unstyled mb-4">
                <li class="mb-2"><i class="bi bi-person-fill me-2"></i>Até 2 adultos</li>
                <li class="mb-2"><i class="bi bi-tv me-2"></i>Smart TV 50"</li>
                <li><i class="bi bi-lock me-2"></i>Cofre digital</li>
              </ul>
              <div class="mt-auto">
                <a href="#reservation" class="btn btn-primary w-100 stretched-link">Detalhes</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Suite Presidencial -->
        <div class="col-lg-4 col-md-6">
          <div class="card h-100">
            <img src="https://source.unsplash.com/600x400/?luxury-room"
              class="card-img-top"
              alt="Quarto precidencial com cama king-size e vista panorâmica"
              loading="lazy">
            <div class="card-body d-flex flex-column">
              <h3 class="h5 card-title">Presidency Suite</h3>
              <p class="card-text text-muted">40m² • Vista para o mar • Wi-Fi plus</p>
              <ul class="list-unstyled mb-4">
                <li class="mb-2"><i class="bi bi-person-fill me-2"></i>Até 4 adultos</li>
                <li class="mb-2"><i class="bi bi-tv me-2"></i>Smart TV 60"</li>
                <li><i class="bi bi-lock me-2"></i>Cofre digital</li>
              </ul>
              <div class="mt-auto">
                <a href="#reservation" class="btn btn-primary w-100 stretched-link">Detalhes</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Quarto Executivo -->
        <div class="col-lg-4 col-md-6">
          <div class="card h-100">
            <img src="https://source.unsplash.com/600x400/?luxury-room"
              class="card-img-top"
              alt="Quarto Executivo com cama king-size e vista panorâmica"
              loading="lazy">
            <div class="card-body d-flex flex-column">
              <h3 class="h5 card-title">Executive Room</h3>
              <p class="card-text text-muted">56m² • Vista para o mar • Wi-Fi platinum</p>
              <ul class="list-unstyled mb-4">
                <li class="mb-2"><i class="bi bi-person-fill me-2"></i>Até 6 adultos</li>
                <li class="mb-2"><i class="bi bi-tv me-2"></i>Smart TV 70"</li>
                <li><i class="bi bi-lock me-2"></i>Cofre digital</li>
              </ul>
              <div class="mt-auto">
                <a href="#reservation" class="btn btn-primary w-100 stretched-link">Detalhes</a>
              </div>
            </div>
          </div>
        </div>
  </section>

  <!-- Seção de Serviços -->
  <section id="services" class="bg-light py-5">
    <div class="container">
        <div class="text-center mb-5">
        <h2>Serviços</h2>
        <p>Descubra nossas ofertas exclusivas</p>
      </div>
      <div class="row">
        <div class="col-md-4 text-center mb-4">
          <i class="bi bi-wifi fs-1"></i>
          <h4 class="mt-3">Wi-Fi Grátis</h4>
          <p>Conexão rápida e segura em todas as áreas do hotel.</p>
        </div>
        <div class="col-md-4 text-center mb-4">
          <i class="bi bi-cup-straw fs-1"></i>
          <h4 class="mt-3">Restaurante</h4>
          <p>Culinária refinada e ambiente aconchegante para suas refeições.</p>
        </div>
        <div class="col-md-4 text-center mb-4">
          <i class="bi bi-bell fs-1"></i>
          <h4 class="mt-3">Serviço de Quarto</h4>
          <p>Atendimento 24 horas para garantir sua comodidade.</p>
        </div>

      </div>
    </div>
  </section>

  <!-- Seção Sobre o Hotel -->
  <section id="about" class="py-5">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-6">
          <img src="uploads/1740785290_Captura de tela 2022-08-14 022249.png" alt="Sobre Nós" class="img-fluid rounded">
        </div>
        <div class="col-md-6">
          <h2>Sobre o Hotel Lux</h2>
          <p>Localizado no coração da cidade, o Hotel Lux oferece uma experiência única de conforto e sofisticação. Nossa missão é proporcionar momentos inesquecíveis com serviços de alta qualidade e atendimento personalizado.</p>
          <a href="#contact" class="btn btn-primary">Saiba Mais</a>
        </div>
      </div>
    </div>
  </section>

  <!-- Seção de Reserva -->
  <section id="reservation" class="bg-primary text-white py-5">
    <div class="container text-center">
      <h2 class="mb-4">Faça sua Reserva</h2>
      <p class="mb-4">Garanta já a sua estadia com condições especiais</p>
      <a href="#" class="btn btn-light btn-lg">Reserve Agora</a>
    </div>
  </section>

  <!-- Seção de Contato -->
  <section id="contact" class="py-5">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="fw-bold">Entre em Contato</h2>
            <p class="text-muted">Estamos disponíveis para esclarecer suas dúvidas</p>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <p><strong>Telefones:</strong> <span class="text-muted">(11) 1234-5678</span> | <span class="text-muted">(11) 8765-4321</span></p>
                <p><strong>E-mail:</strong> 
                    <a href="mailto:contato@hotel.com" class="text-decoration-none" aria-label="Enviar e-mail para contato@hotel.com">
                        contato@hotel.com
                    </a>
                </p>
                <p><strong>WhatsApp:</strong> 
                    <a href="https://wa.me/5511999999999" target="_blank" class="text-decoration-none" aria-label="Conversar pelo WhatsApp">
                        (11) 99999-9999
                    </a>
                </p>
            </div>
        </div>
    </div>
</section>

  <!-- Footer -->
  <footer class="bg-dark text-white py-4">
    <div class="container text-center">
      <p class="mb-0">&copy; 2025 Hotel Lux. Todos os direitos reservados.</p>
    </div>
  </footer>

  <script>
    function abrirCadastro() {
        document.getElementById('loginModal').style.display = 'none';
        document.getElementById('cadastroModal').style.display = 'block';
    }
</script>

  <!-- Bootstrap JS e dependências -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
