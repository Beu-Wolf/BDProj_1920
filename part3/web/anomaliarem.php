<!DOCTYPE html>
<html>
    <head>
        <title>Remover Anomalia</title>
    </head>
    <body>
    <h1>Remover Anomalia</h1>
    <?php
        require 'db.php';
        try {
            $db = connect_db();

            if ($_SERVER["REQUEST_METHOD"] == 'POST') {

                $db->beginTransaction();

                $sql = "DELETE FROM anomalia WHERE id = :id";
                $result = $db->prepare($sql);
                if($result->execute([':id' => $_POST["id"]])) {
                    echo("<p>Anomalia {$_POST["id"]} removida!</p>");
                    $db->commit();
                } else {
                    echo("<p>Erro a remover anomalia!</p>");
                    $db->rollBack();
                }
            }

            $sql = "SELECT id, ts, tem_anomalia_redacao FROM anomalia;";
            $result = $db->prepare($sql);
            $result->execute();

            echo("<table>\n");
            echo('<th scope="col">ID</th>');
            echo('<th scope="col">Tipo</th>');
            echo('<th scope="col">Timestamp</th>');
            echo('<th></th>');
            foreach($result as $row) {
                $id = $row['id'];
                $ts = $row['ts'];
                $type = $row['tem_anomalia_redacao'] ? 'Redação' : 'Tradução';

                echo("<tr>\n");
                echo("<td>{$id}</td>\n");
                echo("<td>${type}</td>");
                echo("<td>{$ts}</td>\n");
                echo("<td>");
                echo("<form action=\"\" method=\"POST\">");
                echo("<input type=\"hidden\" name=\"id\" value=\"{$id}\">");
                echo("<input type=\"submit\" value=\"Remover\">");
                echo('</form>');
                echo("</td>");
                echo("</tr>\n");
            }
            echo("</table>");
        } catch (PDOException $e) { 
            echo("<p>ERROR: {$e->getMessage()}</p>");
        }
    ?>
    <a href="index.html">Voltar</a>
    </body>
</html>
