<?php include("conexion.php"); ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Reportes - Transparencia Ciudadana</title>
</head>
<body>
    <h2>Listado de Reportes</h2>
    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>Usuario</th>
            <th>Servicio</th>
            <th>Descripción</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>

        <?php
        $sql = $conn->query("SELECT r.*, u.Nombre AS Usuario, s.Nombre AS Servicio
                             FROM Reportes r
                             JOIN Usuarios u ON r.Id_Usuario = u.Id_Usuario
                             JOIN Servicios s ON r.Id_Servicio = s.Id_Servicio
                             ORDER BY r.Fecha_Reporte DESC");

        while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>
                    <td>{$row['Id_Reporte']}</td>
                    <td>{$row['Usuario']}</td>
                    <td>{$row['Servicio']}</td>
                    <td>{$row['Descripcion']}</td>
                    <td>{$row['Estado']}</td>
                    <td>
                        <a href='editar_reporte.php?id={$row['Id_Reporte']}'>Editar</a> | 
                        <a href='eliminar_reporte.php?id={$row['Id_Reporte']}'>Eliminar</a>
                    </td>
                 </tr>";
        }
        ?>
    </table>
</body>
</html>
