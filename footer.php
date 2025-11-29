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
<footer class="footer-custom" style="background-color: #355B38; color: white; padding: 40px 0; margin-top: 10rem;">
    <div class="container">
        <div class="row">
            <!-- Columna 1: Información del Shopping -->
            <div class="col-md-4 mb-4">
                <h5 class="mb-3">Paseo de la Fortuna</h5>
                <p>Tu destino de compras favorito.</p>
                <p class="mb-2"><i class="bi bi-geo-alt-fill"></i> Mitre 650, Rosario</p>
                <p class="mb-2"><i class="bi bi-telephone-fill"></i> (341) 456-7890</p>
                <p class="mb-2"><i class="bi bi-envelope-fill"></i> info@paseodelafortuna.com</p>
            </div>

            <!-- Columna 2: Mapa ubuicacion -->
            
            <div class="col-md-4 mb-4">
                <h5 class="mb-3">Nuestra Ubicación</h5>
                <div class="map-container" style="border-radius: 8px; overflow: hidden; border: 2px solid white;">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3348.8!2d-60.6393!3d-32.9442!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMzLCsDU2JzM5LjEiUyA2MMKwMzgnMjEuNSJX!5e0!3m2!1ses!2sar!4v1234567890" 
                        width="100%" 
                        height="200" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
                <p class="mt-2 small">
                    <a href="https://goo.gl/maps/tuenlace" target="_blank" class="text-white">
                        <i class="bi bi-arrow-up-right-square-fill"></i> Ver mapa más grande
                    </a>
                </p>
            </div>

            <!-- Columna 3:Navegacion -->
            <div class="col-md-4 mb-4">
                <h5 class="mb-3">Navegación</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="index.php" class="text-white text-decoration-none">🏠 Inicio</a></li>
                    <li class="mb-2"><a href="contacto.php" class="text-white text-decoration-none">📧 Contacto</a></li>
                    <?php
                    if (isset($_SESSION['tipoUsuario'])) {
                        if ($_SESSION['tipoUsuario'] === 'cliente') {
                            echo '<li class="mb-2"><a href="cuentaUsuario.php" class="text-white text-decoration-none">👤 Mi Cuenta</a></li>';
                        } elseif ($_SESSION['tipoUsuario'] === 'dueño de local') {
                            echo '<li class="mb-2"><a href="cuentaDueño.php" class="text-white text-decoration-none">👤 Mi Cuenta</a></li>';
                        }
                        else{
                            echo '<li class="mb-2"><a href="duenosAdmin(SDB).php" class="text-white text-decoration-none">👤 Panel de Administración</a></li>';
                        }
                    }
                    else {
                        echo '<li class="mb-2"><a href="login.php" class="text-white text-decoration-none">🔐 Iniciar Sesión</a></li>';
                    }
                    
                    ?>
                   
                    <li class="mb-2"><a href="tablaPromocionesComp.php" class="text-white text-decoration-none">🎁 Promociones</a></li>
                    
                    
                </ul>
            </div>
        </div>

        <!-- Línea divisoria -->
        <hr style="border-color: rgba(255,255,255,0.3); margin: 20px 0;">

        <!-- Copyright -->
        <div class="row">
            <div class="col-md-12 text-center">
                <p class="mb-0">&copy; 2025 Paseo de la Fortuna. Todos los derechos reservados.</p>
            </div>
        </div>
    </div>
</footer>