<html>
<body>
    <?php
        try {

            $host = "localhost";
            $user = getenv("POSTGRES_USER");
            $password = getenv("POSTGRES_PASS");
            $dbname = "translateRight";

            $db = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


            $db->beginTransaction();


            $type = $_REQUEST['type'];

            if($type == 'local') {
                $latitude = $_REQUEST['latitude'];
                $longitude = $_REQUEST['longitude'];
                $local = $_REQUEST['local'];
                $action = $_REQUEST['action'];

                if($action == "Remover") {
                    $sql = "DELETE FROM local_publico WHERE latitude = :latitude AND longitude = :longitude;";

                    $result = $db->prepare($sql);
                    $result->execute([':latitude' => $latitude, ':longitude' => $longitude]);
                    echo("<p>Successfully removed location</p>");
                } else if($action == "Inserir") {
                    $sql = "INSERT INTO local_publico (latitude, longitude, nome) VALUES (:latitude, :longitude, :nome);";
                    $result = $db->prepare($sql);
                    $result->execute([':latitude' => $latitude, ':longitude' => $longitude, ':nome' => $local]);
                    echo("<p>Successfully added location</p>");
                }


            }

            /** 
             * TODO: Add item and anomaly
            */
            
            

            $db->commit();
            $db = null;

        } catch (PDOException $e) {
            $db->rollback();
            echo("<p>ERROR: {$e->getMessage()}</p>");

        }

        











    ?>
    <a href="localedit.html">Voltar</a>
</body>
</html>