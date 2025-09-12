<?php
include("Conexion.php");

// --- OBTENER REPORTE POR ID ---
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM Reportes WHERE Id_Reporte = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $reporte = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reporte) {
        die("⚠️ Reporte no encontrado.");
    }
} else {
    die("⚠️ ID no proporcionado.");
}

// --- ACTUALIZAR REPORTE ---
if (isset($_POST['actualizar'])) {
    $descripcion = $_POST['descripcion'];
    $estado = $_POST['estado'];

    $sql = "UPDATE Reportes SET Descripcion = :descripcion, Estado = :estado WHERE Id_Reporte = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':estado', $estado);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    header("Location: Panel_admin.php?msg=Reporte actualizado");
    exit();
}

// --- ELIMINAR REPORTE ---
if (isset($_POST['eliminar'])) {
    $sql = "DELETE FROM Reportes WHERE Id_Reporte = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    header("Location: Panel_admin.php?msg=Reporte eliminado");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Reporte</title>
</head>
<body>
    <h2>✏️ Editar Reporte</h2>

    <form method="POST">
        <label>Descripción:</label><br>
        <textarea name="descripcion" required><?= htmlspecialchars($reporte['Descripcion']) ?></textarea><br><br>

        <label>Estado:</label><br>
        <select name="estado" required>
            <option value="Pendiente" <?= $reporte['Estado'] == "Pendiente" ? "selected" : "" ?>>Pendiente</option>
            <option value="En Proceso" <?= $reporte['Estado'] == "En Proceso" ? "selected" : "" ?>>En Proceso</option>
            <option value="Resuelto" <?= $reporte['Estado'] == "Resuelto" ? "selected" : "" ?>>Resuelto</option>
        </select><br><br>

        <button type="submit" name="actualizar">💾 Actualizar</button>
        <button type="submit" name="eliminar" onclick="return confirm('¿Seguro que deseas eliminar este reporte?')">🗑️ Eliminar</button>
    </form>

    <br>
    <a href="Panel_admin.php">⬅️ Volver al Panel</a>
</body>
</html>


