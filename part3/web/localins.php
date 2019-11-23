<!DOCTYPE html>
<html>
    <head>
        <title>Inserir Local</title>
    </head>
    <body>
        <?php
            require "db.php";
            try {
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

            } catch (PDOException $e) {
                $db->rollback();
                echo("<p>ERROR: {$e->getMessage()}</p>");
            }
        ?>
        <a href="index.html">Voltar</a>
    </body>
</html>