<?php

require_once "config/database.php";

$db = new Database();

$conn = $db->connect();


$sql = "SELECT * FROM rol";

$result = $conn->query($sql);


while($row = $result->fetch(PDO::FETCH_ASSOC))
{
    echo "Rol ID: " . $row["rol_id"] . "<br>";
    echo "Rol Adı: " . $row["rol_adi"] . "<br>";
    echo "Açıklama: " . $row["aciklama"] . "<hr>";
}

?>