<html>
<body>
    <h3>Users</h3>
    <?php
        try {
            $host = "localhost";
            $user = "daniel";
            $password = "BDProj";
            $dbname = "translateRight";

            $db = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "SELECT email, password FROM utilizador;";

            $result = $db->prepare($sql);
            $result->execute();

            echo("<table border=\"1\" cellspacing=\"7\">\n");
            foreach($result as $row) {
                echo("<tr>\n");
                echo("<td>{$row['email']}</td>\n");
                echo("<td>{$row['password']}</td>\n");
                echo("</tr>\n");
            }
            echo("</table>\n");

            $db = null;
        } catch(PDOException $e) {
            echo("<p>ERROR: {$e->getMessage()}</p>");
        }

    ?>        
    <a href="index.html">Voltar</a>
</body>
</html>
