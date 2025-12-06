<!DOCTYPE html>
<html lang="es"> <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../Estilos/estilos.css">
    
    <style>
     

        :root {
            --color-dorado-fondo: #eac764;
            --color-dorado-btn: #DAB561;
            --color-verde-login: #315c3d;
            --color-foco: #fff4b8; 
        }

        .navbar-custom {
            background-color: var(--color-dorado-fondo); 
            padding: 0.5rem 1rem; 
            height: auto; 
        }

       
        .nav-link {
            color: #333 !important; 
            transition: color 0.2s ease;
        }
        
     
        a:focus, button:focus {
            outline: 3px solid var(--color-foco) !important;
            outline-offset: 2px;
            border-radius: 4px;
        }
        a:link
        {
            color: black
        }
        a:visited{
            color: var(--color-verde-login);
        }

        .user-icon-wrapper {
            background-color: var(--color-verde-login);
            color: #ffffff;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;            
            align-items: center;      
            justify-content: center;   
            transition: all 0.3s ease;
        }

        .user-icon-wrapper i {
            font-size: 1.2rem; 
        }


        .user-icon-wrapper:hover {
            background-color: #2a4b34;
            transform: scale(1.05);
        }
        
        .navbar-toggler {
            background-color: var(--color-dorado-btn);
            border: 1px solid #333;
        }
        
    </style>  
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-custom" aria-label="Navegación Principal">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php" >
            <img src="../Footage/Logo.png" alt="Paseo de la Fortuna - Ir al Inicio" style="height: 50px; width: auto; border:none;">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Abrir menú de navegación">
            <i class="fas fa-bars" style="font-size: 1.5rem; color: #000;"></i>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item">
                    <a class="nav-link px-3 fw-semibold" href="index.php">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 fw-semibold" href="contacto.php">Contacto</a>
                </li>
                
                <li class="nav-item ms-3">
                    <a href="login.php" class="nav-link p-0" aria-label="Iniciar Sesión">
                        <div class="user-icon-wrapper" aria-hidden="true">
                            <i class="fas fa-user"></i> 
                        </div>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>