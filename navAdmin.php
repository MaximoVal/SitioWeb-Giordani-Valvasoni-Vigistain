<?php

?>
<head>
    <title>Panel de Administración - Paseo de la Fortuna</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../Estilos/estilos.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />
    
</head>
<style>

    :root {
        --color-dorado: #EED284; 
        --color-dorado-oscuro: #DAB561; 
        --color-negro: #333333;
        --color-blanco: #FFFFFF;
        --color-enfoque: #fff4b8; 
    }

    #boton-nav {
        background-color: var(--color-dorado-oscuro);
        transition: transform 0.2s ease, background-color 0.2s ease;
        color: var(--color-negro);
    }
    
    #boton-nav:hover {
        background-color: #c29f4e;
        transform: scale(1.1);
        color: white; 
    }
    

    #boton-nav:focus {
        outline: 3px solid var(--color-enfoque);
        outline-offset: 2px;
    }
    
    .dropdown-item:active {

        background-color: var(--color-dorado) !important; 
        color: #000000 !important;

    }
    
    .dropdown-item:focus {
        outline: 2px solid var(--color-enfoque);
    }

    .btn-primary {
            background: linear-gradient(135deg, var(--color-dorado) 0%, var(--color-dorado-oscuro) 100%);
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
            color: #333;
        }
    .btn-primary:hover{
            background: var(--color-dorado-oscuro);

        }
    
    
    .btn-primary:focus {
        outline: 3px solid var(--color-enfoque);
        outline-offset: 2px;
    }
    

    .page-link {
        color: var(--color-negro);
    }
    
    .page-link:focus {
        outline: 2px solid var(--color-enfoque);
    }
    
    .active>.page-link, .page-link.active {
        background-color: var(--color-dorado);
        border-color: var(--color-dorado);
        color: var(--color-negro) !important; 
    }
    body {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }
    main {
        flex-grow: 1;
    }
</style>

<body lang="es">

<nav class="navbar navbar-expand-lg navbar-custom" aria-label="Navegación Principal del Sitio">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">
            <img src="../Footage/Logo.png" alt="Logo de Paseo de la Fortuna, ir a Inicio" style="margin=0;border:none;">
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" 
                aria-controls="navbarSupportedContent" aria-expanded="false" 
                aria-label="Alternar Navegación Principal" 
                style="background-color: #DAB561; border: none;">
            <span class="navbar-toggler-icon"></span>
            <span class="visually-hidden">Menú</span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Inicio</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarCuenta" role="button" data-bs-toggle="dropdown" 
                       aria-expanded="false" aria-haspopup="true">
                        <i class="fas fa-user-circle me-1" aria-hidden="true"></i>
                        Panel de Control
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarCuenta">
                        <li>
                            <a class="dropdown-item" href="duenosAdmin(SDB).php">Administrar Dueños</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="administraLocalAdmin.php">Administrar Locales</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="administrarPromocionesAdmin.php">Administrar Promociones</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="creaLocalAdmin.php">Crear local</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="crearNovedad.php">Crear novedad</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="eliminaLocalAdmin.php">Eliminar local</a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="cerrar_sesion.php">Cerrar Sesión</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contacto.php">Contacto</a>
                </li>
            </ul>
            
            <form class="d-flex flex-column flex-sm-row mt-3 mt-lg-0 gap-2 position-relative" action="tablaPromocionesXDueno.php" method="POST" style="width: 100%; max-width: 400px;">
                <div class="position-relative flex-grow-1">
                    <input id="buscar" class="form-control" type="search" placeholder="Buscar tiendas..." aria-label="Search" name="nombreLocal">
                    <div id="resultado" class="list-group position-absolute w-100 mt-1" style="z-index: 1050; max-height: 300px; overflow-y: auto;"></div>
                </div>
                <button class="btn" id="boton-nav" type="submit" style="white-space: nowrap;">Buscar</button>
            </form>
        </div>
    </div>
</nav>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
    $("#buscar").on("keyup", function(){
        let texto = $(this).val();
        
        if(texto.length >= 1){
            $.post("buscarLocal.php", { query: texto }, function(data){
                $("#resultado").html(data).show();
            });
        } else {
            $("#resultado").hide();
        }
    });


    $(document).on("click", ".item", function(){
        $("#buscar").val($(this).text());
        $("#resultado").hide();
    });
    

    $(document).on("click", function(e){
        if(!$(e.target).closest("#buscar, #resultado").length){
            $("#resultado").hide();
        }
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>