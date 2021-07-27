<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>
<body>
    <?php
    //データベース接続
    $dsn = 'database name';
    $user = 'user name';
    $password = 'password';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    //tbmission5というテーブル作成
    $sql = "CREATE TABLE IF NOT EXISTS tbmission5"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"
    . "comment TEXT,"
    . "pass TEXT,"
    . "date TEXT"
    .");";
    $stmt = $pdo->query($sql);
    /*テーブルができているか確認
    $sql = 'SHOW CREATE TABLE tbmission5';
    $result = $pdo->query($sql);
    foreach($result as $row){
        echo $row[1];
    }
    echo "<hr>";
    */
    if(isset($_POST["id1"])){
        $name = $_POST["name"];
        $comment = $_POST["comment"];
        $pass = $_POST["pass"];
        $date = date("Y/m/d H:i:s");
        //データ入力
        if(empty($_POST["number"])){//送信のみ
            $sql = $pdo -> prepare("INSERT INTO tbmission5 (name, comment, pass, date) VALUES (:name, :comment, :pass, :date)");
            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
            $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
            $sql -> bindParam(':date', $date, PDO::PARAM_STR);
            $sql -> execute();
        }elseif(isset($_POST["number"])){//修正用
            $id = $_POST["number"];
            $sql = 'UPDATE tbmission5 SET name=:name,comment=:comment,pass=:pass,date=:date WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt -> bindParam(':name', $name, PDO::PARAM_STR);
            $stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt -> bindParam(':pass', $pass, PDO::PARAM_STR);
            $stmt -> bindParam(':date', $date, PDO::PARAM_STR);
            $stmt -> bindParam(':id', $id, PDO::PARAM_INT);
            $stmt -> execute();
        }
    }elseif(isset($_POST["id2"])){//削除用
        //投稿内容を取得
        $id = $_POST["delete"];
        $sql = 'SELECT * FROM tbmission5 WHERE id=:id ';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll();
        //削除
        foreach($results as $row){
            if($row['pass'] == $_POST["passdelete"]){
                $sql = 'delete from tbmission5 where id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt ->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            }elseif($row['pass'] != $_POST["passdelete"]){
                $error = "パスワードが違います";
            }
        }
    }elseif(isset($_POST["id3"])){//編集
        //投稿内容を取得
        $id = $_POST["edit"];
        $sql = 'SELECT * FROM tbmission5 WHERE id=:id ';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll();
        //送信フォームに表示する準備
        foreach ($results as $row){
            if($row['pass'] == $_POST["passedit"]){
                $numberedit = $row['id'];
                $nameedit = $row['name'];
                $commentedit = $row['comment'];
            }elseif($row['pass'] != $_POST["passdelete"]){
                $error = "パスワードが違います";
            }    
        }
    }
    ?>
    <h3>【投稿フォーム】</h3>
    <form action = "" method = "post">
    <table>
    <input type = "hidden" name = "number" value = "<?php if(isset($numberedit)){echo $numberedit;}?>">
        <tr>
        <th>名前：</th>
        <td><input type = "text" name = "name"
        value = "<?php if(isset($nameedit)){echo $nameedit;}?>"></td>
        </tr>
        <tr>
        <th>コメント：</th>
        <td><input type = "text" name = "comment"
        value = "<?php if(isset($commentedit)){echo $commentedit;}?>"></td>
        </tr>
        <tr>
        <th>パスワード：</th>
        <td><input type = "text" name = "pass"></td>
        </tr>
    <input type = "hidden" name ="id1">
    <tr>
    <td><input type = "submit" name = "submit" value = "送信"></td>
    </tr>
    </table>
    </form>
    <h3>【削除フォーム】</h3>
    <form action = "" method = "post">
    <table>
        <tr>
        <th>投稿番号：</th>
        <td><input type = "number" name = "delete"></td>
        </tr>
        <tr>
        <th>パスワード：</th>
        <td><input type = "text" name = "passdelete"></td>
        </tr>
        <input type = "hidden" name = "id2">
        <tr>
        <td><input type = "submit" name = "submit" value = "削除"></td>
        </tr>
    </table>
    </form>
    <h3>【編集フォーム】</h3>
    <form action = "" method = "post">
    <table>
        <tr>
        <th>投稿番号：</th>
        <td><input type = "number" name = "edit"></td>
        </tr>
        <tr>
        <th>パスワード：</th>
        <td><input type = "text" name = "passedit"></td>
        </tr>
        <input type = "hidden" name = "id3">
        <tr>
        <td><input type = "submit" name = "submit" value = "編集"></td>
        </tr>
    </table>
    </form>
    
    <?php
    if(isset($error)){
        echo "------------------------------<br>".$error."<br>";
    }
    ?>
    ------------------------------<br>
    【投稿一覧】<br>
    <?php //投稿内容表示
    $sql = 'SELECT * FROM tbmission5';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach($results as $row){
        echo $row['id'].',';
        echo $row['name'].',';
        echo $row['comment'].',';
        echo $row['date'].'<br>';
        echo "<hr>";
    }
    ?>

</body>
</html>