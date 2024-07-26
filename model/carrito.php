<?php
session_start();
include("../Config/conexion.php");
if (!isset($_SESSION['ID_Cliente'])) {
    header("Location: login.php");
    exit();
}
$id_cliente = $_SESSION['ID_Cliente'];
$sql = "SELECT carrito.*, Producto.Nombre, Producto.Imagen, Producto.Stock, Producto.Precio_Venta FROM carrito 
        JOIN Producto ON carrito.Cod_Prod = Producto.Codigo 
        WHERE carrito.ID_Cliente='$id_cliente'";
$result = $conec->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <link rel="stylesheet" href="../ext/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <header>
        <h1>Carrito de Compras</h1>
        <a href="../view/clients.php" class="btn btn-primary">Seguir Comprando</a>
        <a href="../Index.php" class="btn btn-secondary">Cerrar Sesión</a>
    </header>
    <main class="container">
        <h2>Productos en el Carrito</h2>
        <form id="carritoForm" action="actualizar_carrito.php" method="POST">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Subtotal</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        $total = 0;
                        while ($row = $result->fetch_assoc()) {
                            $subtotal = $row['Cantidad'] * $row['Precio_Venta'];
                            $total += $subtotal;
                            echo '<tr>';
                            echo '<td>';
                            if (!empty($row['Imagen'])) {
                                echo '<img src="../img/'.$row['Imagen'].'" alt="'.$row['Nombre'].'" style="width: 50px; height: 50px;"> ';
                            }
                            echo $row['Nombre'].'</td>';
                            echo '<td><input type="number" name="cantidad['.$row['Cod_Prod'].']" value="'.$row['Cantidad'].'" min="1" max="'.$row['Stock'].'" class="form-control cantidad" data-precio="'.$row['Precio_Venta'].'"></td>';
                            echo '<td class="precio" data-precio="'.$row['Precio_Venta'].'">$'.$row['Precio_Venta'].'</td>';
                            echo '<td class="subtotal">$'.$subtotal.'</td>';
                            echo '<td><a href="eliminar_carrito.php?id='.$row['ID'].'" class="btn btn-danger">Eliminar</a></td>';
                            echo '</tr>';
                        }
                        echo '<tr>';
                        echo '<td colspan="3" class="text-right"><strong>Total:</strong></td>';
                        echo '<td id="total"><strong>$'.$total.'</strong></td>';
                        echo '</tr>';
                        echo '<tr>';
                        echo '<td colspan="4"></td>';
                        echo '</tr>';
                        echo '<tr>';
                        echo '<td colspan="4"></td>';
                        echo '<td><button type="button" class="btn btn-success" onclick="realizarCompra()">Realizar Compra</button></td>';
                        echo '</tr>';
                    } else {
                        echo '<tr>';
                        echo '<td colspan="5" class="text-center">No hay productos en el carrito.</td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </form>
    </main>
    <footer class="text-center mt-4">
        <p>&copy; 2024 Tienda de Camisetas</p>
    </footer>
    <script src="../ext/jquery/jquery.min.js"></script>
    <script src="../ext/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cantidadInputs = document.querySelectorAll('.cantidad');
            cantidadInputs.forEach(input => {
                input.addEventListener('input', updateSubtotal);
            });
        });
        function updateSubtotal(event) {
            const input = event.target;
            const cantidad = input.value;
            const precio = input.getAttribute('data-precio');
            const subtotal = cantidad * precio;
            const subtotalElement = input.closest('tr').querySelector('.subtotal');
            subtotalElement.textContent = '$' + subtotal.toFixed(2);
            updateTotal();
        }
        function updateTotal() {
            let total = 0;
            const subtotales = document.querySelectorAll('.subtotal');
            subtotales.forEach(subtotal => {
                total += parseFloat(subtotal.textContent.replace('$', ''));
            });
            document.getElementById('total').textContent = '$' + total.toFixed(2);
        }
        function realizarCompra() {
            const total = document.getElementById('total').textContent.replace('$', '');
            const cantidadInputs = document.querySelectorAll('.cantidad');
            let params = `total=${total}`;
            cantidadInputs.forEach(input => {
                const codProd = input.name.match(/\[(.*?)\]/)[1];
                const cantidad = input.value;
                params += `&${codProd}=${cantidad}`;
            });
            window.location.href = `realizar_compra.php?${params}`;
        }
    </script>
</body>
</html>
