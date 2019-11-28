<!DOCTYPE html>
<html>
    <head>
        <title>Remover Anomalia</title>
    </head>
    <body>
    <h1>Remover Anomalia Tradução</h1>
    <?php
        require 'db.php';
        try {
            $db = connect_db();

            if ($_SERVER["REQUEST_METHOD"] == 'POST') {

                try {
                   $db->beginTransaction();

                    $sql = "DELETE FROM anomalia_traducao WHERE id = :id";
                    $result = $db->prepare($sql);
                    if($result->execute([':id' => $_POST["id"]])) {

                        $sql = "SELECT tem_anomalia_redacao FROM anomalia WHERE id = :id;";
                        $result = $db->prepare($sql);
                        if($result->execute([':id' => $_POST["id"]])) {
                            if(($result->fetch())['tem_anomalia_redacao']) {
                                echo("<p>Anomalia {$_POST["id"]} removida!</p>");
                                $db->commit();
                            } else {
                            
                                $sql = "DELETE FROM anomalia WHERE id = :id;";
                                $result = $db->prepare($sql);
                            
                                if($result->execute([':id' => $_POST["id"]])) {
                                    echo("<p>Anomalia {$_POST["id"]} removida!</p>");
                                    $db->commit();
                                } else {
                                    echo("<p>Erro a remover anomalia!</p>");
                                    $db->rollBack();
                                }
                            }
                        } else {
                            echo("<p>Erro a remover anomalia!</p>");
                            $db->rollBack();
                        }
                    } else {
                        echo("<p>Erro a remover anomalia!</p>");
                        $db->rollBack();
                    } 
                } catch(PDOEsception $e) {
                    echo("<p>ERROR; {$e->getMessage()}</p>");
                }
                
            }

            $sql = "SELECT id, zona2, lingua2 FROM anomalia_traducao;";
            $result = $db->prepare($sql);
            $result->execute();

            echo("<table>\n");
            echo('<th scope="col">ID</th>');
            echo('<th scope="col">Zona2</th>');
            echo('<th scope="col">Lingua2</th>');
            echo('<th></th>');
            foreach($result as $row) {
                $id = $row['id'];
                $ts = $row['zona2'];
                $type = $row['lingua2'];

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
