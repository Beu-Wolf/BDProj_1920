<!DOCTYPE html>
<head>
    <title>Inserir Anomalia Redação</title>
</head>
<body>
    <?php
        require 'db.php';
        try {
            if($_SERVER["REQUEST_METHOD"] == 'POST') {

                $db = connect_db();
                
                $id = $_POST['id'];
                $zona = $_POST['zona'];
                $imagem = $_POST['imagem'];
                $lingua = $_POST['lingua'];
                $ts = $_POST['ts'];
                $descricao = $_POST['descricao'];
              
                try {
                    $db->beginTransaction();
                
                    $sql = "INSERT INTO anomalia(id, zona, imagem, lingua, ts, descricao, tem_anomalia_redacao) VALUES (:id, :zona, :imagem, :lingua, :ts, :descricao, False);";
                    
                    $result = $db->prepare($sql);
                    $ret = $result->execute([':id' => $id, ':zona' => $zona, ':imagem' => $imagem, ':lingua' => $lingua, ':ts' => $ts, ':descricao' => $descricao]);
                    
                    if($ret) {
                        echo("<p>Successfully added anomalia</p>");
                        $db->commit();
                    } else {
                        echo("<p>Error inserting anomaly");
                        $db->rollbak();
                    }

                } catch(PDOException $e) {
                    $msg = $e->getMessage();
                    
                    if(strstr($msg, "already exists")) {
                        echo("<p>Anomalia {$id} already exists</p>");
                    }
                    
                    $db->rollback();
                }
               
                
                
                $db = null;
            
            }

        } catch (PDOException $e) {
            echo("<p>ERROR; {$e->getMessage()}</p>");
        }
    
    ?>
    <form action="" method="post">
        <p>id: <input type="number" name="id" required></p>
        <p>Zona1: <input type="text" name="zona" required></p>
        <p>Imagem: <input type="text" name="imagem" required></p>
        <p>Lingua: <input type="text" name="lingua" required></p>
        <p>TS: <input type="datetime-local" name="ts" required></p>
        <p>Descrição: <input type="text" name="descricao" required></p>
        <p><input type="submit" value="Submit"></p>
    </form>
    <a href="index.html">Voltar</a>
</body>