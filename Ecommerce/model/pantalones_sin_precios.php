<?php
include("Config/conexion.php");

$categoria = 'pantalones';
$genero = $_GET['genero'] ?? '';
$marca = $_GET['marca'] ?? '';
$color = $_GET['color'] ?? '';
$talla = $_GET['talla'] ?? '';
$tipo = $_GET['tipo'] ?? '';


$query = "SELECT * FROM Producto WHERE Categoria='$categoria'";

if ($genero != '') {
    $query .= " AND Genero='$genero'";
}
if ($marca != '') {
    $query .= " AND Marca='$marca'";
}
if ($color != '') {
    $query .= " AND Color='$color'";
}
if ($talla != '') {
    $query .= " AND Talla='$talla'";
}
if ($tipo != '') {
    $query .= " AND Tipo='$tipo'";
}

$result = $conec->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filtrar Productos</title>
    <link rel="stylesheet" href="ext/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

    <main>
        <div class="container">
            <div class="row">
                <aside id="filters" class="col-md-3">
                    <h2>Filtros</h2>
                    <form id="filterForm" method="GET" action="">
                        <input type="hidden" name="categoria" value="<?php echo $categoria; ?>">

                        <div class="form-group">
                            <label for="genero">Género:</label>
                            <select id="genero" name="genero" class="form-control">
                                <option value="">Todos</option>
                                <?php
                                $resultGenero = $conec->query("SELECT DISTINCT Genero FROM Producto WHERE Categoria='$categoria'");
                                while ($rowGenero = $resultGenero->fetch_assoc()) {
                                    echo '<option value="'.$rowGenero['Genero'].'">'.$rowGenero['Genero'].'</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="marca">Marca:</label>
                            <select id="marca" name="marca" class="form-control">
                                <option value="">Todos</option>
                                <?php
                                $resultMarca = $conec->query("SELECT DISTINCT Marca FROM Producto WHERE Categoria='$categoria'");
                                while ($rowMarca = $resultMarca->fetch_assoc()) {
                                    echo '<option value="'.$rowMarca['Marca'].'">'.$rowMarca['Marca'].'</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="color">Color:</label>
                            <select id="color" name="color" class="form-control">
                                <option value="">Todos</option>
                                <?php
                                $resultColor = $conec->query("SELECT DISTINCT Color FROM Producto WHERE Categoria='$categoria'");
                                while ($rowColor = $resultColor->fetch_assoc()) {
                                    echo '<option value="'.$rowColor['Color'].'">'.$rowColor['Color'].'</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="talla">Talla:</label>
                            <select id="talla" name="talla" class="form-control">
                                <option value="">Todos</option>
                                <?php
                                $resultTalla = $conec->query("SELECT DISTINCT Talla FROM Producto WHERE Categoria='$categoria'");
                                while ($rowTalla = $resultTalla->fetch_assoc()) {
                                    echo '<option value="'.$rowTalla['Talla'].'">'.$rowTalla['Talla'].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group" id="tipo_div">
                            <label for="tipo">Tipo:</label>
                            <select id="tipo" name="tipo" class="form-control">
                                <option value="">Todos</option>
                                <?php
                                $queryTipo = "SELECT DISTINCT Tipo FROM Producto WHERE Categoria='$categoria'";
                                if ($genero != '') {
                                    $queryTipo .= " AND Genero='$genero'";
                                }
                                $resultTipo = $conec->query($queryTipo);
                                while ($rowTipo = $resultTipo->fetch_assoc()) {
                                    echo '<option value="'.$rowTipo['Tipo'].'"'.($tipo == $rowTipo['Tipo'] ? ' selected' : '').'>'.$rowTipo['Tipo'].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                        <button type="submit" class="btn btn-primary">Filtrar</button>
                    </form>
                </aside>

                <section id="products" class="col-md-9">
                    <h2>Productos</h2>
                    <div id="product-list" class="row">
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo '<div class="col-md-4 mb-4">';
                                echo '<div class="card">';
                                if (!empty($row['Imagen'])) {
                                    echo '<img src="img/'.$row['Imagen'].'" class="card-img-top" alt="'.$row['Nombre'].'">';
                                }
                                echo '<div class="card-body">';
                                echo '<h5 class="card-title">'.$row['Nombre'].'</h5>';
                                echo '<a href="#" class="btn btn-primary add-to-cart">Agregar al carrito</a>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                            }
                        } else {
                            echo '<p class="col-12">No se encontraron productos.</p>';
                        }
                        ?>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <footer class="text-center mt-4">
        <p>&copy; 2024 Tienda de Camisetas</p>
    </footer>
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Iniciar Sesión</h2>
            <form action="model/login.php" method="post">
                <div class="form-group">
                    <label for="email">Correo Electrónico:</label>
                    <input type="email" id="correo" name="correo" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="contrasena" name="contrasena" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
            </form>
        </div>
    </div>

    <script>
        var loginModal = document.getElementById("loginModal");
        var addToCartButtons = document.querySelectorAll(".add-to-cart");
        var closeSpan = document.getElementsByClassName("close")[0];
        addToCartButtons.forEach(function(button) {
            button.onclick = function() {
                loginModal.style.display = "block";
            }
        });
        closeSpan.onclick = function() {
            loginModal.style.display = "none";
        }
        window.onclick = function(event) {
            if (event.target == loginModal) {
                loginModal.style.display = "none";
            }
        }
    </script>

    <script src="ext/jquery/jquery.min.js"></script>
    <script src="ext/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
