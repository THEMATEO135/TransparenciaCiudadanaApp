<?php include("conexion.php"); ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Reporte - Transparencia Ciudadana</title>
</head>
<body>
    <h2>Registrar un Reporte Ciudadano</h2>
    <form method="POST" action="">
        <label>Usuario (ID):</label><br>
        <input type="number" name="id_usuario" required><br><br>

        <label>Servicio:</label><br>
        <select name="id_servicio" required>
            <?php
            $sql = $conn->query("SELECT * FROM Servicios");
            while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value='{$row['Id_Servicio']}'>{$row['Nombre']}</option>";
            }
            ?>
        </select><br><br>

        <label>Descripción:</label><br>
        <textarea name="descripcion" required></textarea><br><br>

        <label>Latitud:</label><br>
        <input type="text" name="latitud"><br><br>

        <label>Longitud:</label><br>
        <input type="text" name="longitud"><br><br>

        <button type="submit" name="guardar">Enviar Reporte</button>
    </form>

    <?php
    if (isset($_POST['guardar'])) {
        $stmt = $conn->prepare("INSERT INTO Reportes (Id_Usuario, Id_Servicio, Descripcion, Latitud, Longitud) 
                                VALUES (:id_usuario, :id_servicio, :descripcion, :latitud, :longitud)");
        $stmt->execute([
            ':id_usuario' => $_POST['id_usuario'],
            ':id_servicio' => $_POST['id_servicio'],
            ':descripcion' => $_POST['descripcion'],
            ':latitud' => $_POST['latitud'],
            ':longitud' => $_POST['longitud']
        ]);
        echo "<p>✅ Reporte registrado exitosamente</p>";
    }
    ?>
</body>
</html>
