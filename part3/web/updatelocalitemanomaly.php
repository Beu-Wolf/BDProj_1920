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

                } else if($action == "Inserir") {

                }


            }

            /** 
             * TODO: Add item and anomaly
            */
            
            $result = $db->prepare($sql);
            $result->execute([':latitude' => $latitude, ':longitude' => $longitude]);

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