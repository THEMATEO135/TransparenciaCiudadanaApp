<?php include("conexion.php"); ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administrador - Transparencia Ciudadana</title>
</head>
<body>
    <h2>📊 Reportes Recibidos</h2>
    <a href="reporte_ciudadano.php">➕ Nuevo Reporte (prueba)</a>
    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>Ciudadano</th>
            <th>Servicio</th>
            <th>Descripción</th>
            <th>Estado</th>
            <th>Fecha</th>
            <th>Acciones</th>
        </tr>

        <?php
        $sql = $conn->query("SELECT r.*, u.Nombre AS Ciudadano, s.Nombre AS Servicio
                             FROM Reportes r
                             JOIN Usuarios u ON r.Id_Usuario = u.Id_Usuario
                             JOIN Servicios s ON r.Id_Servicio = s.Id_Servicio
                             ORDER BY r.Fecha_Reporte DESC");

        while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>
                    <td>{$row['Id_Reporte']}</td>
                    <td>{$row['Ciudadano']}</td>
                    <td>{$row['Servicio']}</td>
                    <td>{$row['Descripcion']}</td>
                    <td>{$row['Estado']}</td>
                    <td>{$row['Fecha_Reporte']}</td>
                    <td>
                        <a href='editar_reporte.php?id={$row['Id_Reporte']}'>✏️ Editar</a> | 
                        <a href='eliminar_reporte.php?id={$row['Id_Reporte']}' onclick='return confirm(\"¿Seguro que deseas eliminar este reporte?\")'>🗑️ Eliminar</a>
                    </td>
                 </tr>";
        }
        ?>
    </table>
</body>
</html>
