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
</head>

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

            <!-- BARRA LATERAL -->
            <aside class="col-md-4 col-lg-3 mb-6">
                <div class="p-3 bg-white shadow rounded-3">
                    <h5 class="mb-3" style="color:var(--color-negro); font-weight:600;">Información del Local</h5>
                    <p class="mb-1"><strong>Nombre:</strong> <?php echo $nombreLocal; ?></p>
                    <p class="mb-1"><strong>Dirección:</strong> <?php echo $direccion; ?></p>
                    <p class="mb-1"><strong>Email:</strong> <?php echo $email; ?></p>
                    <p><strong>Teléfono:</strong> <?php echo $telefono; ?></p>
                </div>

                <div class="mt-4 p-3 bg-white shadow rounded-3">
                    <h6 class="mb-3" style="color:var(--color-negro); font-weight:600;">Vínculos</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-decoration-none d-block py-2" style="color:var(--color-gris);">Gestión de empleados</a></li>
                        <li><a href="#" class="text-decoration-none d-block py-2" style="color:var(--color-gris);">Reportes</a></li>
                    </ul>
                </div>
            </aside>

      <!-- CONTENIDO PRINCIPAL -->
      <section class="col-12 col-md-8 m-1">
        <h5 class="mb-3">Promociones del local</h5>

        <div class="row g-3">

          <!-- Card 1 -->
          <div class="col-12 col-md-6">
            <div class="card promo-card shadow-sm border-success">
              <div class="card-body">
                <h6 class="card-title text-success">10% de descuento para jubilados</h6>
                <p class="card-text small">Aplicable en todas las compras en <strong>Verdulería Don Pepe</strong>.</p>
                <p class="small text-muted mb-2">Válido todos los días, presentando credencial.</p>
                <button class="btn btn-sm btn-outline-success">Aprovechar</button>
              </div>
            </div>
          </div>

          <!-- Card 2 -->
          <div class="col-12 col-md-6">
            <div class="card promo-card shadow-sm border-primary">
              <div class="card-body">
                <h6 class="card-title text-primary">50% de descuento en Frutas de Estacion.</h6>
                <p class="card-text small">Promoción válida en artículos seleccionados de la tienda deportiva.</p>
                <p class="small text-muted mb-2">Solo hasta agotar stock.</p>
                <button class="btn btn-sm btn-primary">Ver productos</button>
              </div>
            </div>
          </div>

          <!-- Card 3 -->
          <div class="col-12 col-md-6">
            <div class="card promo-card shadow-sm border-warning">
              <div class="card-body">
                <h6 class="card-title text-warning">3x2 en productos de Línea Arcor</h6>
                <p class="card-text small">Llevá 3 productos y pagá solo 2 en toda la línea <strong>Arcor</strong>.</p>
                <p class="small text-muted mb-2">Válido en locales adheridos.</p>
                <button class="btn btn-sm btn-outline-warning">Ver más</button>
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
