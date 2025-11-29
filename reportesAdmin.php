

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Dueño</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="../Estilos/adiministraDuenoEstilos.css">
    <link rel="stylesheet" href="../Estilos/estilos.css">

    <!-- Íconos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<style>
     :root {
            --color-dorado-fondo: #eac764;
            --color-dorado-btn: #DAB561;
            --color-verde-login: #315c3d;
            --color-foco: #fff4b8; 
        }
        a:focus, button:focus {
            outline: 3px solid var(--color-foco) !important;
            outline-offset: 2px;
            border-radius: 4px;
        }
</style>
    <body>

    <!-- HEADER -->
    <header class="py-3" style="background: linear-gradient(90deg, var(--color-dorado), var(--color-dorado-oscuro));">
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.html">
                <img src="../Footage/Logo.png" alt="Paseo de la Fortuna Logo" style="margin=0;border:none;" >
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="sobreNosotros.php">Sobre Nosotros</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contacto.php">Contacto</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
        </div>
    </header>

    <!-- CONTENEDOR PRINCIPAL -->
  <main class="container-fluid">
    <div class="row gx-4">

      <!-- SIDEBAR -->
      <aside class="col-12 col-md-3 mb-3">
        <div class="card sidebar-links">
          <div class="card-body">
            <h6 class="card-title">Vínculos</h6>
            <ul class="nav flex-column">
              <li class="nav-item"><a class="nav-link" href="#">Vinculo</a></li>
              <li class="nav-item"><a class="nav-link" href="#">Vinculo</a></li>
              <li class="nav-item"><a class="nav-link" href="#">Vinculo</a></li>
            </ul>
          </div>
        </div>
      </aside>

      <!-- CONTENIDO PRINCIPAL -->
      <section class="col-12 col-md-9">
        <h5 class="mb-3">Reportes</h5>

        <div class="row g-3">

          <!-- Card 1 -->
          <div class="col-12 col-md-6">
            <div class="card report-card shadow-sm">
              <div class="card-body">
                <h6 class="card-title">Promociones Utilizadas por Local</h6>
                <p class="card-text small text-muted">Resumen de promociones utilizadas de cada local.</p>
                <ul class="small mb-3">
                  <li>Café Central: 62</li>
                  <li>Mercadito Norte: 40</li>
                  <li>Librería Sur: 26</li>
                </ul>
                <a href="#" class="btn btn-sm btn-primary">Ver detalle</a>
              </div>
            </div>
          </div>

          <!-- Card 2 -->
          <div class="col-12 col-md-6">
            <div class="card report-card shadow-sm">
              <div class="card-body">
                <h6 class="card-title">Promociones mas utilizadas.</h6>
                <p class="card-text small text-muted">Promociones mas utilizadas en la última semana.</p>
                <ol class="small mb-3">
                  <li>50% de descuento en Ropa Deportiva.</li>
                  <li>3x2 en productos de Linea Arcor.</li>
                  <li>10% para jubilados en la Verdulería Don Pepe.</li>
                </ol>
                <a href="#" class="btn btn-sm btn-outline-primary">Ver detalle</a>
              </div>
            </div>
          </div>

        <!--card 3-->
          <div class="col-12 col-md-6">
            <div class="card report-card shadow-sm">
              <div class="card-body">
                <h6 class="card-title">Nuevos Locales Registrados</h6>
                <p class="card-text small text-muted">Últimos locales agregados al sistema.</p>
                <ul class="small mb-3">
                  <li>Pizzería Napoli</li>
                  <li>MiniMarket Express</li>
                  <li>Floristería Bella Flor</li>
                </ul>
                <a href="#" class="btn btn-sm btn-success">Ver todos</a>
              </div>
            </div>
          </div>


        </div>
      </section>
    </div>
  </main>
    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
