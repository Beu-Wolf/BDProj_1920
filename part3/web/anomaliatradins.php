<!DOCTYPE html>
<head>
    <title>Inserir Anomalia Tradução</title>
</head>
<body>
    <?php 
        require 'db.php';
        try {
            if($_SERVER["REQUEST_METHOD"] == 'POST') {

                $db = connect_db();
                
                $zona = $_POST['zona'];
                $imagem = $_POST['imagem'];
                $lingua = $_POST['lingua'];
                $ts = $_POST['ts'];
                $descricao = $_POST['descricao'];
                $zona2 = $_POST['zona2'];
                $lingua2 = $_POST['lingua2'];
                if($_POST['red']) {
                    $hasRed = True;
                } else {
                    $hasRed = False;
                }
              
                try {
                    $db->beginTransaction();
                
                    $sql = "INSERT INTO anomalia(zona, imagem, lingua, ts, descricao, tem_anomalia_redacao) VALUES (:zona, :imagem, :lingua, :ts, :descricao, :red) RETURNING id;";
                    
                    $result = $db->prepare($sql);
                    $ret = $result->execute([':zona' => $zona, ':imagem' => $imagem, ':lingua' => $lingua, ':ts' => $ts, ':descricao' => $descricao, ':red' => $hasRed]);
                    $id = $result->fetchAll()[0]['id'];
                    
                    $sql = "INSERT INTO anomalia_traducao(id, zona2, lingua2) VALUES(:id, :zona2, :lingua2);";
                    
                    $result = $db->prepare($sql);
                    $ret2 = $result->execute([':id' => $id, ':zona2' => $zona2, ':lingua2' => $lingua2]);


                    if($ret and $ret2) {
                        echo("<p>Anomalia inserida!</p>");
                        $db->commit();
                    } else {
                        echo("<p>Erro a inserir anomalia!</p>");
                        $db->rollback();
                    }

                } catch(PDOException $e) {
                    echo("<p>ERROR; {$e->getMessage()}</p>");
                    $db->rollback();
                }
                
                $db = null;
            }

        } catch (PDOException $e) {
            echo("<p>ERROR; {$e->getMessage()}</p>");
        }
    ?>


    <form action="" method="post">
        <p>Zona1: <input type="text" name="zona" required></p>
        <p>Imagem: <input type="text" name="imagem" required></p>
        <p>Lingua: <input type="text" name="lingua" required></p>
        <p>Timestamp: <input type="datetime-local" name="ts" required></p>
        <p>Descrição: <input type="text" name="descricao" required ></p>
        <p>Zona2: <input type="text" name="zona2" required></p>
        <p>Lingua2: <input type="text" name="lingua2" required></p>
        <p>Também de redação? <input type="checkbox" name="red"></p>
        <p><input type="submit" value="Submit"></p>
    </form>
    <a href="index.html">Voltar</a>
</body>
