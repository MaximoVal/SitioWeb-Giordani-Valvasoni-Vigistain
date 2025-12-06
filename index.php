<?php
    session_set_cookie_params(0);
    session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../Footage/iconoPagina.png" >
    <title>Paseo de la Fortuna - Shopping Center</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="estilos.css">
    <style>
     :root {
            --color-dorado-fondo: #eac764;
            --color-dorado-btn: #DAB561;
            --color-verde-login: #315c3d;
            --color-foco: #000000;
            --color-foco-borde: #fff4b8;
        }
        
        a:focus, button:focus {
            outline: 3px solid var(--color-foco) !important;
            box-shadow: 0 0 0 5px var(--color-foco-borde);
            border-radius: 4px;
            z-index: 5; 
        }

        .skip-link {
            position: absolute;
            top: -40px;
            left: 0;
            background: var(--color-dorado-btn);
            color: black;
            padding: 8px;
            z-index: 10000;
            transition: top 0.3s;
            text-decoration: none;
            font-weight: bold;
        }
        .skip-link:focus {
            top: 0;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
        }
        
        #carouselMini {
            max-width: 25rem;        
            margin: 1.25rem auto;   
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            transition: transform 0.35s ease, box-shadow 0.35s ease;
        }

        #carouselMini:hover {
            transform: scale(1.05); 
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.3); 
        }

        #carouselMini img {
            height: 11.25rem;
            object-fit: cover;
            transition: transform 0.4s ease, filter 0.4s ease;
        }

        #carouselMini:hover img {
            transform: scale(1.08); 
            filter: brightness(1.1); 
        }

        .card-img-placeholder p {
            background-color: rgba(0, 0, 0, 0.6);
            padding: 5px;
            border-radius: 4px;
            color: white; 
            font-size: 1.4rem;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>

<body>
    <a href="#main-content" class="skip-link">Saltar al contenido principal</a>
    
    <header>
    <?php 
        if(!isset($_SESSION['usuario'])) {
            include 'navNoRegistrado.php';   
        } else if($_SESSION['tipoUsuario'] == 'cliente') {
            include 'navCliente.php';  
        } else if($_SESSION['tipoUsuario'] == 'dueno de local') {
            include 'navDueno.php';  
        } else{
            include 'navAdmin.php';  
        }
    ?>
    </header>

    <main id="main-content">
        <section aria-label="Banner de Bienvenida">
            <div id="carouselPrincipal" class="carousel slide position-relative" data-bs-ride="carousel" aria-label="Galería de imágenes principal">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="../Footage/Portada.png" class="d-block w-100" alt="Fachada principal del Paseo de la Fortuna">
                        <div class="carousel-caption-custom">
                            <h1>Bienvenido a Paseo de la Fortuna</h1>
                            <p>Tu destino de compras, entretenimiento y gastronomía</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        
        <section class="servicios-section" id="servicios" aria-labelledby="titulo-servicios">
            <div class="container">
                <h2 id="titulo-servicios" class="visually-hidden">Nuestras Categorías y Promociones</h2>

                <div id="carouselMini" class="carousel slide" data-bs-ride="carousel" aria-label="Destacado de promociones">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <a href="tablaPromocionesComp.php" aria-label="Ir a la sección de todas las promociones">
                                <img src="../Footage/promocionesFoto.jpeg" class="d-block w-100" style="max-width:500px;" alt="Banner colorido de ofertas y descuentos">
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card card-custom h-100">
                            <div class="card-img-placeholder imagenesPromoc" role="img" aria-label="Fotografía de moda e indumentaria" style="background-image: url('../Footage/Indumentaria.png'); background-size: cover; background-position: center;">
                                <p aria-hidden="true">INDUMENTARIA</p>
                            </div>
                            <div class="card-body card-body-custom">
                                <h3 class="card-title h5">Indumentaria</h3>
                                <p class="card-text">Descubre las últimas tendencias en moda, accesorios y mucho más en nuestras exclusivas tiendas de marcas reconocidas.</p>
                                <a href="descuentoTabla.php?categoria=Indumentaria" class="btn btn-custom">Explorar Promociones de Indumentaria</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card card-custom h-100">
                            <div class="card-img-placeholder" role="img" aria-label="Fotografía del patio de comidas" style="background-image: url('../Footage/PatioComida.png'); background-size: cover; background-position: center;">
                                <p aria-hidden="true">GASTRONOMÍA</p>
                            </div>
                            <div class="card-body card-body-custom">
                                <h3 class="card-title h5">Área Gastronómica</h3>
                                <p class="card-text">Disfruta de una amplia variedad de restaurantes con los mejores sabores locales e internacionales para toda la familia.</p>
                                <a href="descuentoTabla.php?categoria=Gastronomia" class="btn btn-custom">Explorar Promociones de Gastronomía</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card card-custom h-100">
                            <div class="card-img-placeholder" role="img" aria-label="Fotografía de sala de cine" style="background-image: url('https://images.pexels.com/photos/34766298/pexels-photo-34766298.jpeg'); background-size: cover; background-position: center;">
                                <p aria-hidden="true">🎬 ENTRETENIMIENTO </p>
                            </div>
                            <div class="card-body card-body-custom">
                                <h3 class="card-title h5">Entretenimiento</h3>
                                <p class="card-text">Vive experiencias únicas en nuestro cine, zona de juegos y espacios de entretenimiento para todas las edades.</p>
                                <a href="descuentoTabla.php?categoria=Entretenimiento" class="btn btn-custom">Explorar Promociones de Entretenimiento</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="card card-custom h-100">
                            <div class="card-img-placeholder" role="img" aria-label="Fotografía de dispositivos electrónicos" style="background-image: url('../Footage/Tecnologia.png'); background-size: cover; background-position: center;">
                                <p aria-hidden="true">TECNOLOGÍA</p>
                            </div>
                            <div class="card-body card-body-custom">
                                <h3 class="card-title h5">Tecnología</h3>
                                <p class="card-text">Notebooks, smartphones y todo lo ultimo en tecnologia que te podes imaginar!</p>
                                <a href="descuentoTabla.php?categoria=Tecnologia" class="btn btn-custom">Explorar Promociones de Tecnología</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="card card-custom h-100">
                            <div class="card-img-placeholder" role="img" aria-label="Fotografía de artículos deportivos" style="background-image: url('https://images.pexels.com/photos/1325724/pexels-photo-1325724.jpeg'); background-size: cover; background-position: center;">
                                <p aria-hidden="true">DEPORTE</p>
                            </div>
                            <div class="card-body card-body-custom">
                                <h3 class="card-title h5">Deporte</h3>
                                <p class="card-text">Veni y adquiri todo lo ultimo de tu deporte favorito!!</p>
                                <a href="descuentoTabla.php?categoria=Deporte" class="btn btn-custom">Explorar Promociones de Deporte</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="card card-custom h-100">
                            <div class="card-img-placeholder" role="img" aria-label="Fotografía variada de compras" style="background-image: url('https://images.pexels.com/photos/1050244/pexels-photo-1050244.jpeg'); background-size: cover; background-position: center;">
                                <p aria-hidden="true">🎉 OTROS</p>
                            </div>
                            <div class="card-body card-body-custom">
                                <h3 class="card-title h5">Otros</h3>
                                <p class="card-text">💄 Salud, Belleza y Cuidado Persona; 💎 Hogar y Decoración; 💼 Servicios...</p>
                                <a href="descuentoTabla.php?categoria=Otros" class="btn btn-custom">Explorar Promociones de Otras categorías</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <section class="container my-5" aria-labelledby="novedades-header">
            <h2 id="novedades-header" class="section-title text-center mb-4">Novedades del Shopping</h2>
            
            <?php
                include_once("funciones.php"); 
                // ... (Lógica PHP sin cambios) ...
                $categoriaUsuario = '';
                $tipoUsuario = '';
                
                if (isset($_SESSION['usuario'])) {
                    $tipoUsuario = $_SESSION['tipoUsuario'];
                    if ($tipoUsuario == 'cliente') {
                        $usuarioActual = $_SESSION['usuario'];
                        $consultaCategoria = "SELECT categoriaCliente FROM usuarios WHERE nombreUsuario = '$usuarioActual'";
                        $resultadoCategoria = consultaSQL($consultaCategoria);
                        if ($rowCategoria = mysqli_fetch_assoc($resultadoCategoria)) {
                            $categoriaUsuario = $rowCategoria['categoriaCliente'];
                        }
                    }
                }
                
                if ($tipoUsuario == 'dueno de local' || $tipoUsuario == 'administrador') {
                    $consulta = "SELECT * FROM novedades ORDER BY fechaDesdeNovedad DESC";
                } else if ($tipoUsuario == 'cliente') {
                    switch ($categoriaUsuario) {
                        case 'Premium':
                            $consulta = "SELECT * FROM novedades WHERE tipoCliente IN ('Premium', 'Medium', 'Inicial') ORDER BY fechaDesdeNovedad DESC";
                            break;
                        case 'Medium':
                            $consulta = "SELECT * FROM novedades WHERE tipoCliente IN ('Medium', 'Inicial') ORDER BY fechaDesdeNovedad DESC";
                            break;
                        case 'Inicial':
                        default:
                            $consulta = "SELECT * FROM novedades WHERE tipoCliente = 'Inicial' ORDER BY fechaDesdeNovedad DESC";
                            break;
                    }
                } else {
                    $consulta = "SELECT * FROM novedades WHERE tipoCliente = 'No' ORDER BY fechaDesdeNovedad DESC";
                }
                
                $resultado = consultaSQL($consulta);
                $novedades = [];
                while ($row = mysqli_fetch_assoc($resultado)) {
                    $novedades[] = $row;
                }
                $totalNovedades = count($novedades);
            ?>
            
            <?php if ($totalNovedades > 0): ?>
            <div id="carouselNovedades" class="carousel slide" data-bs-ride="carousel" aria-label="Galería de últimas novedades">

                <div class="carousel-indicators">
                    <?php for ($i = 0; $i < $totalNovedades; $i++): ?>
                        <button type="button" 
                                data-bs-target="#carouselNovedades" 
                                data-bs-slide-to="<?php echo $i; ?>" 
                                <?php echo $i === 0 ? 'class="active" aria-current="true"' : ''; ?>
                                aria-label="Ver novedad <?php echo $i + 1; ?>">
                        </button>
                    <?php endfor; ?>
                </div>

                <div class="carousel-inner rounded-3 shadow-sm">
                    <?php 
                    $activeSet = false;
                    foreach ($novedades as $row):
                        $activeClass = '';
                        if (!$activeSet) {
                            $activeClass = 'active';
                            $activeSet = true;
                        }
                        
                        // Lógica de imágenes
                        if (!empty($row['imagen'])) {
                            $rutaImagen = "../imagenes/novedades/" . $row['imagen'];
                        } else {
                            $categoriaLower = strtolower($row['categoria']);
                            $mapaImagenes = [
                                'gastronomia' => '../Footage/PatioComida2.png',
                                'entretenimiento' => '../Footage/Entretenimiento.jpg',
                                'deporte' => '../Footage/Deportes.jpg',
                                'tecnologia' => '../Footage/Tecnologia.jpg',
                                'indumentaria' => '../Footage/Indumentaria.jpg',
                                'infraestructura' => '../Footage/Infraestructura.jpg'
                            ];
                            $rutaImagen = isset($mapaImagenes[$categoriaLower]) ? $mapaImagenes[$categoriaLower] : '../Footage/Paseo 2.jpeg';
                        }
                    ?>
                        <div class="carousel-item <?php echo $activeClass; ?>">
                            <img src="<?php echo $rutaImagen; ?>" 
                                class="d-block w-100" 
                                alt="Novedad: <?php echo htmlspecialchars($row['nombre']); ?>"
                                style="max-height: 500px; object-fit: cover;">
                            <div class="carousel-caption bg-dark bg-opacity-50 rounded p-3">
                                <h5><?php echo htmlspecialchars($row['nombre']); ?></h5>
                                <p><?php echo htmlspecialchars($row['descripcionNovedad']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <button class="carousel-control-prev" type="button" data-bs-target="#carouselNovedades" data-bs-slide="prev" aria-label="Ver novedad anterior">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselNovedades" data-bs-slide="next" aria-label="Ver novedad siguiente">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Siguiente</span>
                </button>
            </div>
            <?php else: ?>
                <div class="alert alert-info text-center" role="alert">
                    <i class="bi bi-info-circle" aria-hidden="true"></i> No hay novedades disponibles para tu categoría en este momento.
                </div>
            <?php endif; ?>
            
        </section>
    </main>

    <footer>
        <?php include 'footer.php'; ?>
    </footer>
 
</body>   
</html>