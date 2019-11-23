<!DOCTYPE html>
<html>
    <head>
        <title>Registar Incidencia</title>
    </head>
    <body>
        <?php
        require 'db.php';

        try {
            if($_SERVER["REQUEST_METHOD"] == 'POST') {
                $db = connect_db();

                $anomalia = $_REQUEST['anomalia'];
                $item = $_REQUEST['item'];
                $email = $_REQUEST['email'];

                $db->beginTransaction();

                $sql = "INSERT INTO incidencia(anomalia_id, item_id, email) VALUES (:anomalia, :item, :email);";
                $result = $db->prepare($sql);

                $result->execute([':anomalia' => $anomalia, ':item' => $item, ':email' => $email]);

                echo("<p>Successfully added incident</p>");
                $db->commit();
                $db = null;

            }

        } catch (PDOException $e) {
            $msg = $e->getMessage();

            

            if(strstr($msg, "duplicate key")) {
                echo("<p>anomalia {$anomalia} already exists</p>");
            } else if(strstr($msg, "not present in table \"anomalia\"")) {
                echo("<p>anomalia {$anomalia} doesn't exist</p>");
            } else if(strstr($msg, "not present in table \"item\"")) {
                echo("<p>item {$item} doesn't exist</p>");
            } else if(strstr($msg, "not present in table \"utilizador\"")) {
                echo("<p>user {$email} doesn't exist</p>");
            } else {
                echo("<p>ERROR: {$msg}</p>");
            }

            $db->rollback();
        }
        
        
        ?>
        <form action="" method="post">
            <p>ID Anomalia: <input type="number" name="anomalia" required></p>
            <p>ID Item: <input type="number" name="item" required></p>
            <p>email: <input type="text" name="email" required></p>
            <p><input type="submit" value="Submit"></p>
        </form>
        <a href="index.html">Voltar</a>
    </body>
</html>