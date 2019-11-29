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
                
                $zona = $_POST['zona'];
                $imagem = $_POST['imagem'];
                $lingua = $_POST['lingua'];
                $ts = $_POST['ts'];
                $descricao = $_POST['descricao'];

                if(!strstr($imagem, "\x")) {
                    throw new Exception("Imagem tem de ser um número hexadecimal \x...!");
                }
                
                try {
                    $db->beginTransaction();
                
                    $sql = "INSERT INTO anomalia(zona, imagem, lingua, ts, descricao, tem_anomalia_redacao) VALUES (:zona, :imagem, :lingua, :ts, :descricao, True);";
                    
                    $result = $db->prepare($sql);
                    $ret = $result->execute([':zona' => $zona, ':imagem' => $imagem, ':lingua' => $lingua, ':ts' => $ts, ':descricao' => $descricao]);
                    
                    if($ret) {
                        echo("<p>Anomalia inserida!</p>");
                        $db->commit();
                    } else {
                        echo("<p>Erro a inserir anomalia!</p>");
                        $db->rollBack();
                    }

                } catch(PDOException $e) {
                    $msg = $e->getMessage();
                    if(strstr($msg, "type box")) {
                        echo("<p>Zona1 tem de ser do tipo box (x1,y1),(x2,y2)</p>");
                    
                    } else if(strstr($msg, "datetime format")){
                        echo("<p>Timestamp tem de ser do tipo datedite YYYY-MM-DD HH:MM:SS</p>");
                    
                    } else if(strstr($msg, "odd number of digits") || strstr($msg, "invalid hexadecimal")){
                        echo("<p>Valor hexadecimal tem de ter digitos em número par e com caracteres entre 0..f</p>");
                    
                    } else if(strstr($msg, "value too long")){
                        echo("<p>Descrição, Língua não podem ter mais de 255 chars</p>");
                    } else {
                        echo("<p>ERROR; {$e->getMessage()}</p>");
                    }
                    $db->rollBack();
                }
                
                $db = null;
            }

        } catch (PDOException $e) {
            echo("<p>ERROR; {$e->getMessage()}</p>");
        } catch(Exception $e) {
            echo("<p>Imagem tem de ser um número hexadecimal \x...</p>");
        }
    
    ?>
    <form action="" method="post">
        <p>Zona1: <input type="text" name="zona" required></p>
        <p>Imagem: <input type="text" name="imagem" required></p>
        <p>Lingua: <input type="text" name="lingua" required></p>
        <p>Timestamp: <input type="datetime-local" name="ts" required></p>
        <p>Descrição: <input type="text" name="descricao" required></p>
        <p><input type="submit" value="Submit"></p>
    </form>
    <a href="index.html">Voltar</a>
</body>
