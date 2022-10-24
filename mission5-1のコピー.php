<!DOCTYPE html>
<html lang="ja"></html>
<head>
    <meta charset="UTF-8">
    <title>mission5-01</title>
</head>
<body>
    
    <?php  
    //データベースに接続
    $dsn = 'mysql:host=localhost;dbname=********';
    $user = '*********';
    $password = '********';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    //テーブルを作成
    $sql = "CREATE TABLE IF NOT EXISTS mission5(
        id int(11) AUTO_INCREMENT PRIMARY KEY, 
        name varchar(20), 
        comment TEXT, 
        uptime timestamp not null default current_timestamp on update current_timestamp,
        pw TEXT)";
    $stmt = $pdo->query($sql);
    
    //編集対象番号を取得して、その番号の行にある名前とコメントを取得する処理
    $value_name = ""; 
    $value_comment = ""; 
    $value_handan = ""; 

    $sql = "SELECT*FROM mission5";
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    $num_rows = $stmt->rowCOUNT();
    if($num_rows>=1){ //既にテーブルにレコードが存在していれば
        if(isset($_POST["edinum"], $_POST["edipw"]) && $_POST["edinum"]!='' && $_POST["edipw"]!=''){ 
            //編集対象番号、パスワード欄に何か入力されていれば
            foreach($results as $row){
                    if($row['id'] == $_POST["edinum"] && $row['pw']==$_POST["edipw"]){ //番号、PWが一致したら
                            $value_name = $row['name']; //編集対象番号の行の名前を、ブラウザの名前欄の初期値に設定
                            $value_comment = $row['comment']; //編集対象番号の行のコメントを、ブラウザのコメント欄の初期値に設定
                            $value_pw = $row['pw'];
                            $value_flag = $_POST["edinum"]; //新規か編集か判断するための番号を代入
                            break;
                    }
            }
        }
    }
    ?>
    <form action="" method=POST>
    <p>名前：<input type="text" name="name" value="<?php if(isset($value_name)){echo $value_name;}?>" placeholder="名前"></p>
    <p>コメント：<input type="text" name="comment" value="<?php if(isset($value_comment)){echo $value_comment;}?>" placeholder="コメント"></p>
    <p>パスワード：<input type="text" name="pw" value="<?php if(isset($value_pw)){echo $value_pw;}?>"placeholder="パスワード"></p>
    <P><input type="submit" name="submit" value="送信する"></p>
    <p>削除対象番号：<input type="text" name="ominum" placeholder="削対象除番号"></p>
    <p>パスワードを入力：<input type="text" name="omipw" placeholder="パスワード"></p>
    <p><input type="submit" name="omit" value="削除"></p>
    <p>編集対象番号：<input type="text" name="edinum" placeholder="編集対象番号"></p>
    <p>パスワードを入力：<input type="text" name="edipw" placeholder="パスワード"></p>
    <p><input type="submit" name="edit" value="編集"></p>
    <p><input type="text" name="flag" name="flag" value="<?php if(isset($value_flag)){echo $value_flag;}?>"></p>
    
    
    <?php
    if(isset($_POST["name"])){$name = $_POST["name"];}
    if(isset($_POST["comment"])){$comment = $_POST["comment"];}
    if(isset($_POST["pw"])){$pw = $_POST["pw"];}
    if(isset($_POST["ominum"])){$ominum = $_POST["ominum"];}
    if(isset($_POST["omipw"])){$omipw = $_POST["omipw"];}
    
    //書き込み、編集の処理
    if(isset($name, $comment, $pw) && $name!="" && $comment!="" && $pw!=""){ //名前とコメント欄に何か入力されていれば
        if($_POST["flag"]==""){ //新規の場合の処理
            $sql = "INSERT INTO mission5
                    (name, comment, pw)
                    VALUES
                    (:name, :comment, :pw)";
            $stmt = $pdo->prepare($sql);
            $stmt -> bindParam(':name', $name, PDO::PARAM_STR);
            $stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt -> bindParam(':pw', $pw, PDO::PARAM_STR);
            $stmt -> execute();
        }else{ //編集の場合の処理
            $sql = "UPDATE mission5
                    SET
                    name = :name, 
                    comment = :comment, 
                    pw = :pw  
                    WHERE
                    id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt -> bindParam(':name', $name, PDO::PARAM_STR);
            $stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt -> bindParam(':pw', $pw, PDO::PARAM_STR);
            $stmt -> bindParam(':id', $_POST["flag"], PDO::PARAM_INT);
            $stmt -> execute();
        }
    }
    
    //削除処理
    $sql = "SELECT*FROM mission5";
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    $num_rows = $stmt->rowCOUNT();
    if($num_rows >= 1){ //テーブルに既にレコードがあれば
        if(isset($ominum)){ //削除対象番号に何か入力されていれば
            foreach($results as $row){
                if($row{'id'}==$ominum && $row['pw']==$omipw){
                    $sql = "DELETE FROM mission5
                            WHERE id = :id";
                    $stmt = $pdo->prepare($sql);
                    $stmt -> bindParam(':id', $ominum, PDO::PARAM_INT);
                    $stmt -> execute();

                    $sql1 = "ALTER TABLE mission5
                            drop column id";      
                    $sql2 = "ALTER TABLE mission5
                            add id int(11) AUTO_INCREMENT PRIMARY KEY first";              
                    $sql3 = "ALTER TABLE mission5
                            AUTO_INCREMENT =1";
                    $stmt = $pdo->query($sql1);
                    $stmt = $pdo->query($sql2);
                    $stmt = $pdo->query($sql3);

                    break;
                }
            }
        }
    }
   
    //ブラウザに表示させる処理
    $sql = "SELECT*FROM mission5";
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach($results as $row){
        echo $row['id'].'<>';
        echo $row['name'].'<>';
        echo $row['comment'].'<>';
        echo $row['uptime'].'<br>';
    }
    ?>
</body>