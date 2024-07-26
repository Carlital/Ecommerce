<?php 
include("../Config/conexion.php"); 

$configResult = $conec->query("SELECT * FROM Configuracion WHERE ID_Conf = 1");
$config = $configResult->fetch_assoc();
$ganancia = $config['Ganancia'];
$iva = $config['IVA'];
$search = $_GET['search'] ?? '';

$query = "SELECT * FROM Producto";
if ($search != '') {
    $query .= " WHERE Nombre LIKE '%$search%' OR Marca LIKE '%$search%' OR Codigo LIKE '%$search%'";
}
$result = $conec->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador - Tienda de Camisetas</title>
    <link href="https://fonts.googleapis.com/css?family=Cormorant+Garamond" rel="stylesheet">
    <link rel="stylesheet" href="../ext/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/admintabs.css">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/modal.css">
</head>
<body>
    
    <header>
        <h1>Administrador - Tienda de Camisetas</h1>
        <nav>
            <a href="clients.php">Inicio</a>
            <img src="../img/icons/config.png" alt="Configuración" class="config-icon" onclick="document.getElementById('configModal').style.display='block'">
        </nav>
        <a href="../index.php" class="logout-btn">Salir de sesión</a>
    </header>

    <main>
        <div class="container">
            <div class="tabs-container">
                <div class="tabs active" data-tab="productos">Productos Existentes</div>
                <div class="tabs" data-tab="agregar">Agregar Producto</div>
            </div>

            <div class="tab-content active" id="productos">
                <h2 id="h2">Productos Existentes</h2>
                <form action="" method="GET" onsubmit="clearSearch()">
                    <div class="form-group">
                        <label for="search">Buscar Productos:</label>
                        <input type="text" id="search" name="search" class="form-control" placeholder="Nombre, Marca o Código" value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </form>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Marca</th>
                            <th>Precio Venta</th>
                            <th>Color</th>
                            <th>Talla</th>
                            <th>Género</th>
                            <th>Stock</th>
                            <th>Tipo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($row = $result->fetch_assoc()){
                            echo '<tr>';
                            echo '<td>'.$row['Codigo'].'</td>';
                            echo '<td>'.$row['Nombre'].'</td>';
                            echo '<td>'.$row['Marca'].'</td>';
                            echo '<td>'.$row['Precio_Venta'].'</td>';
                            echo '<td>'.$row['Color'].'</td>';
                            echo '<td>'.$row['Talla'].'</td>';
                            echo '<td>'.$row['Genero'].'</td>';
                            echo '<td>'.$row['Stock'].'</td>';
                            echo '<td>'.$row['Tipo'].'</td>';
                            echo '<td><a href="editar_producto.php?id='.$row['Codigo'].'" class="btn btn-warning btn-sm">Editar</a> ';
                            echo '<a href="../model/eliminar.php?id='.$row['Codigo'].'" class="btn btn-danger btn-sm">Eliminar</a></td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="tab-content" id="agregar">
                <h2 id="h2">Agregar Producto</h2>
                <form action="../model/get_products.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="codigo">Código</label>
                        <input type="text" id="codigo" name="codigo" class="form-control" pattern="[A-Z][0-9]{3}" title ="Formato incorrecto, ingrese una letra mayúscula seguida de 3 números" required>
                    </div>
                    <div class="form-group">
                        <label for="genero">Género</label>
                        <select id="genero" name="genero" class="form-control" required onchange="actualizarOpciones()">
                            <option value="Hombre">Hombre</option>
                            <option value="Mujer">Mujer</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="categoria">Categoría</label>
                        <select id="categoria" name="categoria" class="form-control" required onchange="actualizarOpciones()">
                            <option value="camisetas">Camisetas</option>
                            <option value="pantalones">Pantalones</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="marca">Marca</label>
                        <input type="text" id="marca" name="marca" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="precio_coste">Precio Coste</label>
                        <input type="number" id="precio_coste" name="precio_coste" class="form-control" step="0.01" min="1" max="500" title="Ingrese un precio válido (min 1 y máx 500)" required oninput="calcularPrecioVenta()">
                    </div>
                    <div class="form-group">
                        <label for="precio_venta">Precio Venta</label>
                        <input type="number" id="precio_venta" name="precio_venta" class="form-control" step="0.01" required readonly>
                    </div>
                    <div class="form-group">
                        <label for="color">Color</label>
                        <input type="text" id="color" name="color" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="talla">Talla</label>
                        <select id="talla" name="talla" class="form-control" required></select>
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
                        <input type="number" id="stock" name="stock" class="form-control" min="0" max="500" title="Ingrese una cantidad válida (min 0 y máx 500)" required>
                    </div>
                    <div class="form-group">
                        <label for="imagen">Imagen</label>
                        <input type="file" id="imagen" name="imagen" class="custom-file-input" accept=".jpg, .png" required onchange="validarArchivo()">
                        <label id="img-upload" class="custom-file-label" for="imagen" id="imagen-label">Subir imagen</label>
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
                                    input.value = ""; // Limpia el campo de entrada de archivo
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
                                    input.value = ""; // Limpia el campo de entrada de archivo
                                    document.getElementById("imagen-text").textContent = "No se ha cargado ninguna imagen";
                                }
                            }
                        }
                    </script>
                    <button type="submit" class="btn btn-primary">Agregar Producto</button>
                </form>
            </div>
        </div>
    </main>
    <div class="floating-buttons">
        <a href="../model/reporte1.php" title="Reporte 1"><i class="fas fa-file-alt"></i></a>
        <a href="../model/reporte2.php" title="Reporte 2"><i class="fas fa-chart-bar"></i></a>
        <a href="../model/reporte3.php" title="Reporte 3"><i class="fas fa-file-invoice"></i></a>
        <a href="../model/reporte4.php" title="Reporte 4"><i class="fas fa-file-invoice"></i></a>
    </div>
    <div id="configModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('configModal').style.display='none'">&times;</span>
            <h2>Configuración</h2>
            <form id="configForm">
                <div class="form-group">
                    <label for="ganancia">Ganancia (%)</label>
                    <input type="number" id="ganancia" name="ganancia" class="form-control" value="<?php echo $ganancia; ?>" required>
                </div>
                <div class="form-group">
                    <label for="iva">IVA (%)</label>
                    <input type="number" id="iva" name="iva" class="form-control" value="<?php echo $iva; ?>" required>
                </div>
                <div class="form-group">
                    <label for="promocion">Promoción (%)</label>
                    <input type="number" id="promocion" name="promocion" class="form-control" value="<?php echo $promocion; ?>">
                </div>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </form>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Tienda de Camisetas</p>
    </footer>

    <script src="../ext/jquery/jquery.min.js"></script>
    <script src="../ext/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.tabs');
            const tabContents = document.querySelectorAll('.tab-content');

            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    tabs.forEach(t => t.classList.remove('active'));
                    tab.classList.add('active');

                    const tabId = tab.getAttribute('data-tab');
                    tabContents.forEach(tc => {
                        tc.classList.remove('active');
                        if (tc.getAttribute('id') === tabId) {
                            tc.classList.add('active');
                        }
                    });
                });
            });

            actualizarOpciones();
        });

        const tallasCamisetas = ['S', 'M', 'L', 'XL'];
        const tallasPantalones = ['26', '28', '30', '32', '34', '36', '38'];

        const tiposCamisetas = ['Deportivo', 'Casual'];
        const tiposPantalonesHombre = ['Deportivo'];
        const tiposPantalonesMujer = ['Leggins', 'Capris','Deportivo'];

        function actualizarOpciones() {
            const categoria = document.getElementById('categoria').value;
            const genero = document.getElementById('genero').value;
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

        document.getElementById('configForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('../model/update_config.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if (data === 'success') {
                    alert('Configuración actualizada con éxito');
                    location.reload();
                } else {
                    alert('Error al actualizar la configuración');
                }
            })
            .catch(error => console.error('Error:', error));
        });

        window.onclick = function(event) {
            const modal = document.getElementById('configModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>
