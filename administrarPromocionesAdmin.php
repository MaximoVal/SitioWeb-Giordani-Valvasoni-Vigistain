

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Administrador</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="../Estilos/adiministraDuenoEstilos.css">
    <link rel="stylesheet" href="../Estilos/estilos.css">

    <!-- Íconos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<style>
    .list-group-item.active
        {
            background-color: #DAB561 !important;
            border-color: #DAB561 !important;
            color: #000000 !important;
        }   
        
</style>
<body >
    <!-- HEADER -->
    <?php include 'navAdmin.php'; ?>
    <!-- CONTENEDOR PRINCIPAL -->
   <main class="container-fluid my-4">
    <div class="row">
        <!-- LADO IZQUIERDO - VÍNCULOS -->
        <aside class="col-12 col-md-3 mb-3">
        <div class="card sidebar-links">
            <div class="card-body d-flex flex-column justify-content-start">
                
                <button class="btn btn-primary w-100 d-md-none mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#adminMenu" aria-expanded="false" aria-controls="adminMenu">
                    <i class="bi bi-menu-button-wide me-2"></i>Panel administrador
                </button>
                
             
                <h3 class="card-title title d-none d-md-block">Panel administrador</h3>
                
                
                <div class="collapse d-md-block" id="adminMenu">
                    <div class="list-group">
                        <a href="duenosAdmin(SDB).php" class="list-group-item list-group-item-action">Administrar dueños</a>
                        <a href="administraLocalAdmin.php" class="list-group-item list-group-item-action ">Administrar locales</a>
                        <a href="administrarPromocionesAdmin.php" class="list-group-item list-group-item-action active">Administrar promociones</a>
                        <a href="creaLocalAdmin.php" class="list-group-item list-group-item-action">Crear local</a>
                        <a href="crearNovedad.php" class="list-group-item list-group-item-action">Crear novedad</a>
                        <a href="eliminaLocalAdmin.php" class="list-group-item list-group-item-action">Eliminar local</a>   
                    </div>
                </div>
            </div>
        </div>
    </aside>

        <!-- CONTENIDO PRINCIPAL -->
        <section class="col-md-8 col-lg-9">
            <div class="p-4 bg-white shadow rounded-3 mb-4">
                <h4 class="mb-4" style="color: var(--color-negro); font-weight:600;">Solicitudes de promociones</h4>
                <?php include "verificarPromocionesAdmin.php"; ?>
            </div>
            
            <div class="p-4 bg-white shadow rounded-3">
                <h4 class="mb-4" style="color: var(--color-negro); font-weight:600;">Reportes de promociones</h4>
                <?php include "verReportesAdmin.php"; ?>
            </div>
        </section>
    </div>
</main>

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
.sidebar-fixed {
    position: sticky;
    top: 20px; 
    align-self: flex-start;
}

.list-group-item.active {
    background-color: var(--color-dorado) !important;
    border-color: var(--color-dorado) !important;
    color: var(--color-negro) !important;
    font-weight: 600;
}

.list-group-item:hover {
    background-color: rgba(218, 181, 97, 0.2);
    transition: all 0.3s ease;
}

@media (max-width: 768px) {
    .sidebar-fixed {
        position: relative;
        top: 0;
    }
}
</style>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    

</body>
</html>
