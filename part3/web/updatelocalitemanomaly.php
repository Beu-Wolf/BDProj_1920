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

            /**
             * To keep everything tided, all updates to item, location and anomaly are done here
             */

            if($type == 'local') {
                $latitude = $_REQUEST['latitude'];
                $longitude = $_REQUEST['longitude'];
                $action = $_REQUEST['action'];

                
                // Separate both possible actions for local

                if($action == "Remover") {
                    $sql = "DELETE FROM local_publico WHERE latitude = :latitude AND longitude = :longitude;";

                    $result = $db->prepare($sql);
                    $result->execute([':latitude' => $latitude, ':longitude' => $longitude]);
                    echo("<p>Successfully removed location</p>");
                } else if($action == "Inserir") {
                    $local = $_REQUEST['local'];

                    $sql = "INSERT INTO local_publico (latitude, longitude, nome) VALUES (:latitude, :longitude, :nome);";
                    $result = $db->prepare($sql);
                    $result->execute([':latitude' => $latitude, ':longitude' => $longitude, ':nome' => $local]);
                    echo("<p>Successfully added location</p>");
                }


            } else if($type == 'item') {
                $id = $_REQUEST['id'];
                $action = $_REQUEST['action'];


                if($action == 'Remover') {
                    $sql = "DELETE FROM item WHERE id = :id;";
                    $result = $db->prepare($sql);

                    $result->execute([':id' => $id]);
                    echo("<p>Successfully removed item</p>");


                } else if($action == 'Inserir') {
                    $descricao = $_REQUEST['descricao'];
                    $localizacao = $_REQUEST['localizacao'];
                    $latitude = $_REQUEST['latitude'];
                    $longitude = $_REQUEST['longitude'];

                    $sql = "INSERT INTO item(id, descricao, localizacao, latitude, longitude) VALUES (:id, :descricao, :localizacao, :latitude, :longitude);";

                    $result = $db->prepare($sql);
                    $result->execute([':id' => $id, ':descricao' => $descricao, ':localizacao' => $localizacao, ':latitude' => $latitude, ':longitude' => $longitude]);
                    echo("<p>Successfully added item</p>");
                }
            
            } else if($type == 'anomalia') {
                $id = $_REQUEST['id'];
                $action = $_REQUEST['action'];

                if($action == 'Remover') {
                    $sql = "DELETE FROM anomalia WHERE id = :id;";

                    $result = $db->prepare($sql);

                    $result->execute([':id' => $id]);
                    echo("<p>Successfully removed anomalia</p>");

                } else if($action == 'Inserir') {
                    $zona = $_REQUEST['zona'];
                    $imagem = $_REQUEST['imagem'];
                    $lingua = $_REQUEST['lingua'];
                    $ts = $_REQUEST['ts'];
                    $descricao = $_REQUEST['descricao'];
                    $hasTrad = $_REQUEST['hasTrad'];

                    if($hasTrad == 'False') {
                        $sql = "INSERT INTO anomalia(id, zona, imagem, lingua, ts, descricao, tem_anomalia_redacao) VALUES (:id, :zona, :imagem, :lingua, :ts, :descricao, :hasTrad);";

                        $result = $db->prepare($sql);
                        $result->execute([':id' => $id, ':zona' => $zona, ':imagem' => $imagem, ':lingua' => $lingua, ':ts' => $ts, ':descricao' => $descricao, ':hasTrad' => $hasTrad]);
                        echo("<p>Successfully added anomalia</p>");
                    } else {
                        $zona2 = $_REQUEST['zona2'];
                        $lingua2 = $_REQUEST['lingua2'];
                        
                        $sql = "INSERT INTO anomalia(id, zona, imagem, lingua, ts, descricao, tem_anomalia_redacao) VALUES (:id, :zona, :imagem, :lingua, :ts, :descricao, :hasTrad);";

                        $result = $db->prepare($sql);
                        $result->execute([':id' => $id, ':zona' => $zona, ':imagem' => $imagem, ':lingua' => $lingua, ':ts' => $ts, ':descricao' => $descricao, ':hasTrad' => $hasTrad]);
                        
                        $sql = "INSERT INTO anomalia_traducao(id, zona2, lingua2) VALUES(:id, :zona2, :lingua2);";
                        $result = $db->prepare($sql);
                        $result->execute([':id' => $id, ':zona2' => $zona2, ':lingua2' => $lingua2]);
                        
                        
                        echo("<p>Successfully added anomalia</p>");

                    }

                }
            }

            
            

            $db->commit();
            $db = null;

        } catch (PDOException $e) {
            $db->rollback();
            echo("<p>ERROR: {$e->getMessage()}</p>");

        }

        











    ?>
    <a href="localitemanomalyedit.html">Voltar</a>
</body>
</html>