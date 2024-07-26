<?php
include("../Config/conexion.php");

if (isset($_GET['id'])) {
    $codigo = $_GET['id'];
    $query = "SELECT * FROM Producto WHERE Codigo = '$codigo'";
    $result = $conec->query($query);
    $row = $result->fetch_assoc();
} else {
    echo "<script>alert('ID no válido'); window.location='../index.php';</script>";
    exit;
}
$configResult = $conec->query("SELECT * FROM Configuracion WHERE ID_Conf = 1");
$config = $configResult->fetch_assoc();
$ganancia = $config['Ganancia'];
$iva = $config['IVA'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
    <link href="https://fonts.googleapis.com/css?family=Cormorant+Garamond" rel="stylesheet">
    <link rel="stylesheet" href="../ext/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/admintabs.css">
</head>
<body>
    <header>
        <h1>Editar Producto</h1>
        <nav>
            <a href="../index.php" class="btn btn-secondary">Inicio</a>
        </nav>
    </header>

    <main>
        <div class="container">
            <h2>Editar Producto</h2>
            <form action="../model/actualizar.php" method="POST" enctype="multipart/form-data">
                <input type="text" name="codigo" value="<?php echo $row['Codigo']; ?>" readonly>
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" value="<?php echo $row['Nombre']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="marca">Marca</label>
                    <input type="text" id="marca" name="marca" class="form-control" value="<?php echo $row['Marca']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="precio_coste">Precio Coste</label>
                    <input type="number" id="precio_coste" name="precio_coste" value="<?php echo $row['Precio_Coste']; ?>" class="form-control" step="0.01" required onchange="calcularPrecioVenta()">
                </div>
                <div class="form-group">
                    <label for="precio_venta">Precio Venta</label>
                    <input type="number" id="precio_venta" name="precio_venta" value="<?php echo $row['Precio_Venta']; ?>" class="form-control" step="0.01" required readonly>
                </div>
                <div class="form-group">
                    <label for="color">Color</label>
                    <input type="text" id="color" name="color" class="form-control" value="<?php echo $row['Color']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="talla">Talla</label>
                    <select id="talla" name="talla" class="form-control" required>
                    </select>
                </div>
                <div class="form-group" id="tipo_div">
                    <label for="tipo">Tipo de Producto</label>
                    <select id="tipo" name="tipo" class="form-control"></select>
                </div>
                <div class="form-group" id="tipo_cuello_div">
                    <label for="tipo_cuello">Tipo de Cuello</label>
                    <select id="tipo_cuello" name="tipo_cuello" class="form-control">
                        <option value="V">V</option>
                        <option value="Redondo">Redondo</option>
                        <option value="Polo">Polo</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="stock">Stock</label>
                    <input type="number" id="stock" name="stock" class="form-control" value="<?php echo $row['Stock']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="imagen_actual">Imagen Actual</label>
                    <br>
                    <img src="../img/<?php echo $row['Imagen']; ?>" alt="Imagen del Producto" style="max-width: 200px;">
                    <input type="hidden" name="imagen_actual" value="<?php echo $row['Imagen']; ?>">
                </div>
                <div class="form-group">
                    <label for="imagen">Nueva Imagen (opcional)</label>
                    <input type="file" id="imagen" name="imagen" class="custom-file-input" accept=".jpg, .png" onchange="validarArchivo()">
                    <label class="custom-file-label" for="imagen" id="imagen-label">Subir imagen</label>
                    <span id="imagen-text">No se ha cargado ninguna imagen</span>
                </div>
                <script>
                    document.getElementById("imagen").addEventListener("change", function() {
                        var input = this;
                        var file = input.files[0];
                        var label = document.getElementById("imagen-label");
                        var text = document.getElementById("imagen-text");                
                        if (file) {
                            var fileType = file.type;
                            var validTypes = ["image/jpeg", "image/png"];
                            if (!validTypes.includes(fileType)) {
                                alert("Por favor, seleccione un archivo válido. Solo se permiten archivos .jpg y .png.");
                                input.value = "";
                                text.textContent = "No se ha cargado ninguna imagen";
                            } else {
                                label.textContent = "Cambiar imagen";
                                text.textContent = file.name;
                            }
                        } else {
                            text.textContent = "No se ha cargado ninguna imagen";
                        }
                    });

                    function validarArchivo() {
                        var input = document.getElementById("imagen");
                        var file = input.files[0];
                        if (file) {
                            var fileType = file.type;
                            var validTypes = ["image/jpeg", "image/png"];
                            if (!validTypes.includes(fileType)) {
                                alert("Por favor, seleccione un archivo válido. Solo se permiten archivos .jpg y .png.");
                                input.value = ""; 
                                document.getElementById("imagen-text").textContent = "No se ha cargado ninguna imagen";
                            }
                        }
                    }
                </script>
                <button type="submit" class="btn btn-primary">Actualizar Producto</button>
                <button name=cancel class="btn btn-primary" value=1>Cancelar</button>

            </form>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Tienda de Camisetas</p>
    </footer>

    <script src="../ext/jquery/jquery.min.js"></script>
    <script src="../ext/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        const tallasCamisetas = ['S', 'M', 'L', 'XL'];
        const tallasPantalones = ['26', '28', '30', '32', '34', '36', '38'];

        const tiposCamisetas = ['Deportivo', 'Casual'];
        const tiposPantalonesHombre = ['Deportivo'];
        const tiposPantalonesMujer = ['Leggins', 'Capris','Deportivo'];

        function actualizarOpciones() {
            const categoria = "<?php echo $row['Categoria']; ?>";
            const genero = "<?php echo $row['Genero']; ?>";
            const tipoDiv = document.getElementById('tipo_div');
            const tipoSelect = document.getElementById('tipo');
            const tallaSelect = document.getElementById('talla');
            const tipoCuelloDiv = document.getElementById('tipo_cuello_div');

            tipoSelect.innerHTML = '';
            tallaSelect.innerHTML = '';

            if (categoria == 'camisetas') {
                tipoCuelloDiv.style.display = 'block';
                tallasCamisetas.forEach(talla => {
                    const option = document.createElement('option');
                    option.value = talla;
                    option.textContent = talla;
                    tallaSelect.appendChild(option);
                });
                tiposCamisetas.forEach(tipo => {
                    const option = document.createElement('option');
                    option.value = tipo;
                    option.textContent = tipo;
                    tipoSelect.appendChild(option);
                });
            } else {
                tipoCuelloDiv.style.display = 'none';
                tallasPantalones.forEach(talla => {
                    const option = document.createElement('option');
                    option.value = talla;
                    option.textContent = talla;
                    tallaSelect.appendChild(option);
                });
                if (genero == 'Hombre') {
                    tiposPantalonesHombre.forEach(tipo => {
                        const option = document.createElement('option');
                        option.value = tipo;
                        option.textContent = tipo;
                        tipoSelect.appendChild(option);
                    });
                } else {
                    tiposPantalonesMujer.forEach(tipo => {
                        const option = document.createElement('option');
                        option.value = tipo;
                        option.textContent = tipo;
                        tipoSelect.appendChild(option);
                    });
                }
            }

            document.getElementById('talla').value = "<?php echo $row['Talla']; ?>";
            document.getElementById('tipo').value = "<?php echo $row['Tipo']; ?>";
            document.getElementById('tipo_cuello').value = "<?php echo $row['Tipo_Cuello']; ?>";
        }
        function calcularPrecioVenta() {
            const precioCoste = parseFloat(document.getElementById('precio_coste').value);
            if (!isNaN(precioCoste)) {
                const ganancia = <?php echo $ganancia; ?>;
                const iva = <?php echo $iva; ?>;
                const precioVenta = precioCoste * (ganancia / 100) + precioCoste + (precioCoste * (ganancia / 100) + precioCoste) * iva / 100;
                document.getElementById('precio_venta').value = precioVenta.toFixed(2);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            actualizarOpciones();
        });
    </script>
</body>
</html>
