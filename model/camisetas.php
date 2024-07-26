<?php
include("../Config/conexion.php");

$categoria = 'camisetas';
$genero = $_GET['genero'] ?? '';
$marca = $_GET['marca'] ?? '';
$color = $_GET['color'] ?? '';
$talla = $_GET['talla'] ?? '';
$tipo_cuello = $_GET['tipo_cuello'] ?? '';
$precioMin = $_GET['precioMin'] ?? 0;
$precioMax = $_GET['precioMax'] ?? 100;
$tipo = $_GET['tipo'] ?? '';

$queryMinPrecio = "SELECT MIN(Precio_Venta) as MinPrecio FROM Producto WHERE Categoria='$categoria'";
$queryMaxPrecio = "SELECT MAX(Precio_Venta) as MaxPrecio FROM Producto WHERE Categoria='$categoria'";
$resultMinPrecio = $conec->query($queryMinPrecio);
$resultMaxPrecio = $conec->query($queryMaxPrecio);

$minPrecio = $resultMinPrecio->fetch_assoc()['MinPrecio'] ?? 0;
$maxPrecio = $resultMaxPrecio->fetch_assoc()['MaxPrecio'] ?? 100;

$query = "SELECT * FROM Producto WHERE Categoria='$categoria' AND Precio_Venta BETWEEN $precioMin AND $precioMax";

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
if ($tipo_cuello != '') {
    $query .= " AND Tipo_Cuello='$tipo_cuello'";
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
    <link rel="stylesheet" href="../ext/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/styles.css">
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

                        <div class="form-group">
                            <label for="tipo_cuello">Tipo de Cuello:</label>
                            <select id="tipo_cuello" name="tipo_cuello" class="form-control">
                                <option value="">Todos</option>
                                <?php
                                $resultTipoCuello = $conec->query("SELECT DISTINCT Tipo_Cuello FROM Producto WHERE Categoria='$categoria'");
                                while ($rowTipoCuello = $resultTipoCuello->fetch_assoc()) {
                                    echo '<option value="'.$rowTipoCuello['Tipo_Cuello'].'">'.$rowTipoCuello['Tipo_Cuello'].'</option>';
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

                        <div class="form-group range-slider">
                        <label for="precio">Precio:</label>
                        <input type="range" id="precioMin" name="precioMin" min="<?php echo $minPrecio; ?>" max="<?php echo $maxPrecio; ?>" step="1" value="<?php echo $precioMin; ?>" oninput="updatePriceValues()">
                        <input type="range" id="precioMax" name="precioMax" min="<?php echo $minPrecio; ?>" max="<?php echo $maxPrecio; ?>" step="1" value="<?php echo $precioMax; ?>" oninput="updatePriceValues()">
                        <div class="slider-track"></div>
                        <div class="slider-track-active"></div>
                        <div class="range-values">
                            <span id="precioMin-value">$<?php echo $precioMin; ?></span>
                            <span id="precioMax-value">$<?php echo $precioMax; ?></span>
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
                                    echo '<img src="../img/'.$row['Imagen'].'" class="card-img-top" alt="'.$row['Nombre'].'">';
                                }
                                echo '<div class="card-body">';
                                echo '<h5 class="card-title">'.$row['Nombre'].'</h5>';
                                echo '<p class="card-text">Precio: $'.$row['Precio_Venta'].'</p>';
                                echo '<a href="#" class="btn btn-primary add-to-cart" data-id="'.$row['Codigo'].'">Agregar al carrito</a>';
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

    <script src="ext/jquery/jquery.min.js"></script>
<script src="ext/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
    function updatePriceValues() {
        const minInput = document.getElementById('precioMin');
        const maxInput = document.getElementById('precioMax');
        const minValueDisplay = document.getElementById('precioMin-value');
        const maxValueDisplay = document.getElementById('precioMax-value');

        if (parseInt(minInput.value) > parseInt(maxInput.value)) {
            const tempValue = minInput.value;
            minInput.value = maxInput.value;
            maxInput.value = tempValue;
        }
        minValueDisplay.textContent = '$' + minInput.value;
        maxValueDisplay.textContent = '$' + maxInput.value;
        const min = parseInt(minInput.min);
        const max = parseInt(maxInput.max);
        const rangeMin = parseInt(minInput.value);
        const rangeMax = parseInt(maxInput.value);

        const trackActive = document.querySelector('.slider-track-active');
        const track = document.querySelector('.slider-track');

        const trackWidth = track.offsetWidth;
        const leftPercent = ((rangeMin - min) / (max - min)) * 100;
        const rightPercent = ((rangeMax - min) / (max - min)) * 100;

        trackActive.style.left = leftPercent + '%';
        trackActive.style.width = (rightPercent - leftPercent) + '%';
    }

     window.addEventListener('DOMContentLoaded', () => {
        updatePriceValues();
    });
</script>
</body>
</html>
