<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página web</title>
    <link href="https://fonts.googleapis.com/css?family=Cormorant+Garamond" rel="stylesheet">
    <link rel="stylesheet" href="ext/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/keyframes.css">
    <link rel="stylesheet" href="css/banner.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/modal_search.css">
    <link rel="stylesheet" href="css/admin.css">

</head>
<body>
    <header>
        <h1>Tienda de Camisetas</h1>
        <nav>
            <a href="Controller/controller.php?opcion=1">Iniciar sesión</a>
            <a href="Controller/controller.php?opcion=2">Registrarse</a>
        </nav>
    </header>

    <main>
        <div class="container">
            <form id="searchForm" method="GET" action="">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Buscar producto por nombre" name="search" id="search" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="button" onclick="openModal()">Buscar</button>
                    </div>
                </div>
            </form>
            <ul class="nav nav-tabs" id="categoryTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link" id="camisetas-tab" href="#camisetas" data-toggle="tab" data-category="camisetas">Camisetas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pantalones-tab" href="#pantalones" data-toggle="tab" data-category="pantalones">Pantalones</a>
                </li>
            </ul>

            <div class="tab-content" id="categoryTabsContent">
                <div class="tab-pane fade" id="camisetas" role="tabpanel" aria-labelledby="camisetas-tab">
                    <?php include("model/camisetas_sin_precio.php"); ?>
                </div>
                <div class="tab-pane fade" id="pantalones" role="tabpanel" aria-labelledby="pantalones-tab">
                    <?php include("model/pantalones_sin_precios.php"); ?>
                </div>
            </div>
        </div>
    </main>
    <div id="searchModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2>Resultados de Búsqueda</h2>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Resultados de la búsqueda -->
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal()">Cerrar</button>
            </div>
        </div>
    </div>
    <footer>
        <p>&copy; 2024 Tienda de Camisetas</p>
    </footer>

    <script src="ext/jquery/jquery.min.js"></script>
    <script src="ext/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const category = urlParams.get('categoria') || 'camisetas';
            const tab = document.querySelector(`#categoryTabs .nav-link[data-category="${category}"]`);
            
            if (tab) {
                tab.click();
            }
        });

        document.querySelectorAll('#categoryTabs .nav-link').forEach(tab => {
            tab.addEventListener('click', function(event) {
                event.preventDefault();
                const category = tab.getAttribute('data-category');
                const url = new URL(window.location.href);
                url.searchParams.set('categoria', category);
                window.history.replaceState({}, '', url);
                
                document.querySelectorAll('#categoryTabs .nav-link').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-pane').forEach(tc => tc.classList.remove('show', 'active'));

                tab.classList.add('active');
                document.querySelector(`#${tab.getAttribute('href').substring(1)}`).classList.add('show', 'active');
            });
        });
        function openModal() {
            const search = document.getElementById('search').value;
            const category = document.querySelector('#categoryTabs .nav-link.active').getAttribute('data-category');
            const modalBody = document.getElementById('modalBody');
            const modal = document.getElementById('searchModal');

            fetch(`model/searchproducts.php?categoria=${category}&search=${search}`)
                .then(response => response.text())
                .then(data => {
                    modalBody.innerHTML = data;
                    modal.style.display = "block";
                });
        }

        function closeModal() {
            document.getElementById('searchModal').style.display = "none";
        }
    </script>
</body>
</html>
