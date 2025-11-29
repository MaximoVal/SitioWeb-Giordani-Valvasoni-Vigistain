<?php
    session_set_cookie_params(0);
    session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paseo de la Fortuna - Shopping Center</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="estilos.css">
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
    .overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.25);
}
#carouselMini {
    max-width: 400px;        
    margin: 20px auto;   
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
    height: 180px;
    object-fit: cover;
    transition: transform 0.4s ease, filter 0.4s ease;
}

#carouselMini:hover img {
    transform: scale(1.08); 
    filter: brightness(1.1); 
}
</style>
<body>
    
    <?php 

        if(!isset($_SESSION['usuario'])) {
            include 'navNoRegistrado.php';   
        } else if($_SESSION['tipoUsuario'] == 'cliente') {
            
                include 'navCliente.php';  
            }
            
        else if($_SESSION['tipoUsuario'] == 'dueno de local') {
                include 'navDueño.php';  
            }
        else{
                include 'navAdmin.php';  
            }
    ?>


    <div id="carouselPrincipal" class="carousel slide position-relative" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="../Footage/Portada.png" class="d-block w-100" alt="Paseo de la Fortuna">
                <div class="carousel-caption-custom">
                    <h1>Bienvenido a Paseo de la Fortuna</h1>
                    <p>Tu destino de compras, entretenimiento y gastronomía</p>
                </div>
            </div>
        </div>
    </div>


    
    <section class="servicios-section" id="servicios">
        <div class="container">
            <div id="carouselMini" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <a href="tablaPromocionesComp.php"><img src="../Footage/promocionesFoto.jpeg" class="d-block w-100" style="max-width=299px" alt="Promo"></a>
                    </div>
                </div>
            </div>
            <div class="row">
                <!--Tarjetas de Categorias-->
                <div class="col-lg-4 col-md-6">
                    <div class="card card-custom">
                        <div class="card-img-placeholder imagenesPromoc" style="background-image: url('../Footage/Indumentaria.png'); background-size: cover; background-position: center;">
                          <p style="color: white; -webkit-text-stroke: 0.5px black; font-size: 1.4rem;">INDUMENTARIA</p>
                        </div>
                        <div class="card-body card-body-custom">
                            <h5 class="card-title">Indumentaria</h5>
                            <p class="card-text">Descubre las últimas tendencias en moda, accesorios y mucho más en nuestras exclusivas tiendas de marcas reconocidas.</p>
                            <a href="descuentoTabla(SDB).php?categoria=Indumentaria" class="btn btn-custom">Explorar Promociones</a>
                        </div>
                    </div>
                </div>


                <div class="col-lg-4 col-md-6">
                    <div class="card card-custom">
                        <div class="card-img-placeholder" style="background-image: url('../Footage/PatioComida.png'); background-size: cover; background-position: center;">
                          <p style="color: white; -webkit-text-stroke: 0.5px black; font-size: 1.4rem;">GASTRONOMIA</p>
                        </div>
                        <div class="card-body card-body-custom">
                            <h5 class="card-title">Área Gastronómica</h5>
                            <p class="card-text">Disfruta de una amplia variedad de restaurantes con los mejores sabores locales e internacionales para toda la familia.</p>
                            <a href="descuentoTabla(SDB).php?categoria=Gastronomia" class="btn btn-custom">Explorar Promociones</a>
                        </div>
                    </div>
                </div>


                <div class="col-lg-4 col-md-6">
                    <div class="card card-custom">
                        <div class="card-img-placeholder" style="background-image: url('https://images.pexels.com/photos/34766298/pexels-photo-34766298.jpeg'); background-size: cover; background-position: center;">
                            <p style="color: white; -webkit-text-stroke: 0.5px black; font-size: 1.4rem;">🎬 ENTRETENIMIENTO </p>
                        </div>
                        <div class="card-body card-body-custom">
                            <h5 class="card-title">Entretenimiento</h5>
                            <p class="card-text">Vive experiencias únicas en nuestro cine, zona de juegos y espacios de entretenimiento para todas las edades.</p>
                            <a href="descuentoTabla(SDB).php?categoria=Entretenimiento" class="btn btn-custom">Explorar Promociones</a>
                        </div>
                    </div>
                </div>


                <div class="col-lg-4 col-md-6">
                    <div class="card card-custom">
                        <div class="card-img-placeholder" style="background-image: url('../Footage/Tecnologia.png'); background-size: cover; background-position: center;">
                            <p style="color: white; -webkit-text-stroke: 0.5px black; font-size: 1.4rem;">TECNOLOGIA</p>
                        </div>
                        <div class="card-body card-body-custom">
                            <h5 class="card-title">Tecnologia</h5>
                            <p class="card-text">Notebooks, smartphones y todo lo ultimo en tecnologia que te podes imaginar!</p>
                            <a href="descuentoTabla(SDB).php?categoria=Tecnologia" class="btn btn-custom">Explorar Promociones</a>
                        </div>
                    </div>
                </div>


                <div class="col-lg-4 col-md-6">
                    <div class="card card-custom">
                        <div class="card-img-placeholder" style="background-image: url('https://images.pexels.com/photos/1325724/pexels-photo-1325724.jpeg'); background-size: cover; background-position: center;">
                            <p style="color: white; -webkit-text-stroke: 0.5px black; font-size: 1.6rem;">DEPORTE</p>
                        </div>
                        <div class="card-body card-body-custom">
                            <h5 class="card-title">Deporte</h5>
                            <p class="card-text">Veni y adquiri todo lo ultimo de tu deporte favorito!!</p>
                            <a href="descuentoTabla(SDB).php?categoria=Deporte" class="btn btn-custom">Explorar Promociones</a>
                        </div>
                    </div>
                </div>


                <div class="col-lg-4 col-md-6">
                    <div class="card card-custom">
                        <div class="card-img-placeholder" style="background-image: url('https://images.pexels.com/photos/1050244/pexels-photo-1050244.jpeg'); background-size: cover; background-position: center;">
                            🎉 OTROS
                        </div>
                        <div class="card-body card-body-custom">
                            <h5 class="card-title">Otros</h5>
                            <p class="card-text">💄 Salud, Belleza y Cuidado Persona; 💎 Hogar y Decoración; 💼 Servicios...</p>
                            <a href="descuentoTabla(SDB).php?categoria=Otros" class="btn btn-custom">Explorar Promociones</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <div class="container my-5">
        <h2 class="section-title text-center mb-4">Novedades del Shopping</h2>
        
        <?php
            
            include_once("funciones.php"); 

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

                        $consulta = "SELECT * FROM novedades 
                                    WHERE tipoCliente IN ('Premium', 'Medium', 'Inicial') 
                                    ORDER BY fechaDesdeNovedad DESC";
                        break;
                    case 'Medium':
     
                        $consulta = "SELECT * FROM novedades 
                                    WHERE tipoCliente IN ('Medium', 'Inicial') 
                                    ORDER BY fechaDesdeNovedad DESC";
                        break;
                    case 'Inicial':
                    default:
                
                        $consulta = "SELECT * FROM novedades 
                                    WHERE tipoCliente = 'Inicial' 
                                    ORDER BY fechaDesdeNovedad DESC";
                        break;
                }
            } else {
 
                $consulta = "SELECT * FROM novedades 
                            WHERE tipoCliente = 'No' 
                            ORDER BY fechaDesdeNovedad DESC";
            }
            
            $resultado = consultaSQL($consulta);
            

            $novedades = [];
            while ($row = mysqli_fetch_assoc($resultado)) {
                $novedades[] = $row;
            }
            $totalNovedades = count($novedades);
        ?>
        
        <?php if ($totalNovedades > 0): ?>
        <div id="carouselNovedades" class="carousel slide" data-bs-ride="carousel">

            <div class="carousel-indicators">
                <?php for ($i = 0; $i < $totalNovedades; $i++): ?>
                    <button type="button" 
                            data-bs-target="#carouselNovedades" 
                            data-bs-slide-to="<?php echo $i; ?>" 
                            <?php echo $i === 0 ? 'class="active"' : ''; ?>
                            aria-label="Slide <?php echo $i + 1; ?>">
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
                    
                 
                    if (!empty($row['imagen'])) {
                  
                        $rutaImagen = "../imagenes/novedades/" . $row['imagen'];
                    } else {
                      
                        $categoriaLower = strtolower($row['categoria']);
                        
                        if ($categoriaLower == 'gastronomia') {
                            $rutaImagen = '../Footage/PatioComida2.png';
                        } else if ($categoriaLower == 'entretenimiento') {
                            $rutaImagen = '../Footage/Entretenimiento.jpg';
                        } else if ($categoriaLower == 'deporte') {
                            $rutaImagen = '../Footage/Deportes.jpg';
                        } else if ($categoriaLower == 'tecnologia') {
                            $rutaImagen = '../Footage/Tecnologia.jpg';
                        } else if ($categoriaLower == 'indumentaria') {
                            $rutaImagen = '../Footage/Indumentaria.jpg';
                        } else if ($categoriaLower == 'infraestructura') {
                            $rutaImagen = '../Footage/Infraestructura.jpg';
                        } else {
                            $rutaImagen = '../Footage/Paseo 2.jpeg';
                        }
                    }
                ?>
                    <!-- Novedad: <?php echo ($row['nombre']); ?> -->
                    <div class="carousel-item <?php echo $activeClass; ?>">
                        <img src="<?php echo $rutaImagen; ?>" 
                             class="d-block w-100" 
                             alt="Nombre de la novedad:<?php echo ($row['nombre']); ?>"
                             style="max-height: 500px; object-fit: cover;">
                        <div class="carousel-caption bg-dark bg-opacity-50 rounded p-3">
                            <h5><?php echo ($row['nombre']); ?></h5>
                            <p><?php echo ($row['descripcionNovedad']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Controles -->
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselNovedades" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselNovedades" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Siguiente</span>
            </button>
        </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle"></i> No hay novedades disponibles para tu categoría en este momento.
            </div>
        <?php endif; ?>
        
        
    </div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

 
</body>   
</html>