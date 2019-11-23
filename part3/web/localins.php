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
                
                echo("<form action=\"\" method=\"post\">");
                echo("<p>Latitude: <input type=\"number\" step=\"0.000001\" name=\"latitude\" required></p>");
                echo("<p>Longitude: <input type=\"number\" step=\"0.000001\" name=\"longitude\" required></p>");
                echo("<p>Nome do local: <input type=\"text\" name=\"local\" required></p>");
                echo("<p><input type=\"submit\" value=\"Submit\"></p>");
                echo('</form>');
                
                

            } catch (PDOException $e) {
                $db->rollback();
                echo("<p>ERROR: {$e->getMessage()}</p>");
            }
        ?>
        <a href="index.html">Voltar</a>
    </body>
</html>