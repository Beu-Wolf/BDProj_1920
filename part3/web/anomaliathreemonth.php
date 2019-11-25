<!DOCTYPE html>
<html>
    <head>
        <title>Anomalias</title>
    </head>
    <body>
        <h3>Anomalias Encontradas:</h3>
        <?php
            require 'db.php';
            try {

                $db = connect_db();

                $now =  date("Y-m-d H:i:s");
                $threemonths = date("Y-m-d H:i:s", strtotime("-3 months"));

                $latitude = $_POST['latitude'];
                $longitude = $_POST['longitude'];
                $dX = $_POST['dX'];
                $dY = $_POST['dY'];


                #sort latitudes and longitudes for query
                if($latitude + $dX > $latitude) {
                    $latitude1 = $latitude;
                    $latitude2 = $latitude + $dX;
                } else {
                    $latitude1 = $latitude + $dX;
                    $latitude2 = $latitude;
                }

                if($longitude + $dY > $longitude) {
                    $longitude1 = $longitude;
                    $longitude2 = $longitude + $dY;
                } else {
                    $longitude1 = $longitude + $dY;
                    $longitude2 = $longitude;
                }

                $sql = "SELECT A.id, A.zona, A.imagem, A.lingua, A.ts, A.descricao, A.tem_anomalia_redacao
                FROM item AS I, incidencia AS B, anomalia AS A
                WHERE A.id = B.anomalia_id AND B.item_id = I.id AND I.latitude >= :latitude1 AND 
                I.latitude <= :latitude2 AND I.longitude >= :longitude1 AND 
                I.longitude <= :longitude2 AND A.ts >= :3months AND A.ts <= :current";

                $result = $db->prepare($sql);
                $result->execute([':latitude1' => $latitude1, ':latitude2' => $latitude2, ':longitude1' => $longitude1, ':longitude2' => $longitude2, ':3months' => $threemonths, ':current' => $now]);

                $count = 0;

                echo("<table style=\"border-spacing: 10px;\">\n");
                echo('<th scope="col">Id</th>');
                echo('<th scope="col">Zona</th>');
                echo('<th scope="col">Imagem</th>');
                echo('<th scope="col">Lingua</th>');
                echo('<th scope="col">TS</th>');
                echo('<th scope="col">Descricao</th>');
                echo('<th scope="col">Anomalia Traducao?</th>');
                foreach($result as $row) {
                    echo("<tr>\n");
                    echo("<td>{$row['id']}</td>");
                    echo("<td>{$row['zona']}</td>");
                    echo("<td>{$row['imagem']}</td>");
                    echo("<td>{$row['lingua']}</td>");
                    echo("<td>{$row['ts']}</td>");
                    echo("<td>{$row['descricao']}</td>");
                    echo("<td>{$row['tem_anomalia_redacao']}</td>");
                    echo("</tr>\n");
                    $count++;
                }
                echo("</table>");

                echo("<p>Encontradas {$count} anomalias</p>");

                $db = null;

            } catch(PDOException $e) {
                echo("<p>ERROR: {$e->getMessage()}</p>");
            }

        
        
        
        ?>
        <p><a href="anomalialatform.html">Voltar</a></p>
    </body>
</html>