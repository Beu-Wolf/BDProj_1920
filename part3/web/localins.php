<!DOCTYPE html>
<html>
    <head>
        <title>Inserir Local</title>
    </head>
    <body>
        <?php
            require "db.php";
            try {

                if($_SERVER["REQUEST_METHOD"] == 'POST') {

                    $db = connect_db();
                    $latitude = $_REQUEST['latitude'];
                    $longitude = $_REQUEST['longitude'];

                    $local = $_REQUEST['local'];

                    $db->beginTransaction();

                    $sql = "INSERT INTO local_publico (latitude, longitude, nome) VALUES (:latitude, :longitude, :nome);";
                    $result = $db->prepare($sql);
                    $result->execute([':latitude' => $latitude, ':longitude' => $longitude, ':nome' => $local]);
                    echo("<p>Successfully added location</p>");

                    $db->commit();
                    $db = null;
                }
            } catch (PDOException $e) {
                $msg = $e->getMessage();

                if(strstr($msg, "already exists")) {
                    echo("<p>There already is a location at ({$latitude}, {$longitude})</p>");
                }

                $db->rollback();
                echo("<p>ERROR: {$msg}</p>");
            }
        ?>
        <form action="" method="post">
            <p>Latitude: <input type="number" step="0.000001" name="latitude" required></p>
            <p>Longitude: <input type="number" step="0.000001" name="longitude" required></p>
            <p>Nome do local: <input type="text" name="local" required></p>
            <p><input type="submit" value="Submit"></p>
        </form>
        <a href="index.html">Voltar</a>
    </body>
</html>