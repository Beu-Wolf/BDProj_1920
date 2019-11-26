<!DOCTYPE html>
<html>
    <head>
        <title>Inserir Proposta de Correção</title>
    </head>
    <body>
        <h2>Inserir Proposta de Correção</h2>
        <?php
            require 'db.php';
            try {
                $db = connect_db();

                try {
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $email = $_POST['email'];
                        $text = $_POST['text'];
                        $datetime = $_POST['datetime'];

                        $sql = "SELECT MAX(nro) from proposta_de_correcao where email = :email;";
                        $result = $db->prepare($sql);
                        $result->execute([':email' => $email]);

                        $nro = $result->fetch()[0];

                        if (is_null($nro)) {
                            $nro = 1;
                        } else {
                            $nro++;
                        }

                        $sql = "INSERT INTO proposta_de_correcao (email, nro, data_hora, texto)
                            VALUES (:email, :nro, :data_hora, :texto);";
                        $db->beginTransaction();

                        $result = $db->prepare($sql);
                        $ret = $result->execute([":email" => $email, ":nro" => $nro,
                                                ":data_hora" => $datetime, ":texto" => $text]);
                        if ($ret) {
                            $db->commit();
                        } else {
                            echo("<p>Erro a inserir proposta de correção!</p>");
                            $db->rollback();
                        }

                        echo("<p>Proposta de correção de {$email} inserida!<p>");
                    }
                } catch (PDOException $e) {
                    $msg = $e->getMessage();
                    if (strstr($msg, "invalid input syntax for type timestamp")) {
                        echo('<p>Formato de data-hora inválido!</p>');
                    } else {
                        echo("<p>ERROR: {$e->getMessage()}</p>");
                    }
                    $db->rollBack();
                }

                echo('<form action="" method="POST">');

                $sql = "SELECT email from utilizador_qualificado;";
                $result = $db->prepare($sql);
                $result->execute();

                echo('Utilizador Qualificado: <select name="email">');
                foreach($result as $row) {
                    $email = $row['email'];
                    echo("<option value=\"{$email}\">{$email}</option>");
                }
                echo('</select>');
                echo('<br>');

                echo('Data-hora: <input type="datetime-local" name="datetime">');

                echo('<br>');
                echo('<textarea name="text" rows="10" cols="45" placeholder="Texto da proposta..."></textarea>');
                echo('<br>');
                echo('<input type="submit" value="Inserir">');
                echo('</form>');


            } catch (PDOException $e) {
                echo("<p>ERROR: {$e->getMessage()}</p>");
            }
        ?>
        <br>
        <a href="index.html">Voltar</a>
    </body>
</html>
