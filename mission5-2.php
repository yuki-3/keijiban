<!DOCTYPE html>
<html lang="ja">
    <head>
    <meta charset="UTF-8">
    <title>mission_5-2</title>
    </head>
<body>
    <h3>好きなスポーツを教えてください</h3>
    <?php
     // DB接続設定
    $dsn = 'mysql:dbname=データベース名;host=localhost';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    $sql = "CREATE TABLE IF NOT EXISTS keijiban"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"
    . "comment TEXT,"
    . "date DATETIME,"
    . "pass TEXT"
    .");";
    $stmt = $pdo->query($sql);

    if (!empty($_POST['submit'])){
        if (!empty($_POST['name'])){
            if(!empty($_POST['comment'])){
                if(!empty($_POST['pass'])){
                    // 以下、新規投稿機能
                    if (empty($_POST['editNO'])) {
                        $sql = $pdo -> prepare("INSERT INTO keijiban (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)");
                        $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                        $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                        $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                        $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
                        //入力データの受け取りを変数に代入
                        $name = $_POST['name'];
                        $comment = $_POST['comment'];
                        $date = date('Y-m-d H:i:s');
                        $pass = $_POST['pass'];
                        $sql -> execute();
                    }   else {
                            // 以下編集機能
                            //入力データの受け取りを変数に代入
                            $editNO = $_POST['editNO'];
                            $name = $_POST["name"];
                            $comment = $_POST["comment"];
                            $date = date('Y-m-d H:i:s');
                            $pass = $_POST["pass"];
                            $sql = 'UPDATE keijiban SET name=:name,comment=:comment,date=:date,pass=:pass WHERE id=:id';
                            $stmt = $pdo->prepare($sql);
                            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                            $stmt->bindParam(':id', $editNO, PDO::PARAM_INT);
                            $stmt->bindParam(':date', $date, PDO::PARAM_STR);
                            $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
                            $stmt ->execute();// ←SQLを実行
                        }
                }else {echo "*「パスワード」を数字で入力してください";}
            }else {echo "*「コメント」を入力してください";}
        }else {echo "*「名前」を入力してください";}
    }

    //削除機能
    //削除フォームの送信の有無で処理を分岐
    if (!empty($_POST['delete'])){
        if (!empty($_POST["dnum"])){
            if (!empty($_POST["delpass"])){
                //入力データの受け取りを変数に代入
                $dnum = $_POST["dnum"];
                $delpass = $_POST["delpass"];
                //削除するか判定
                $sql = "SELECT * FROM keijiban";
                $stmt = $pdo->query($sql);
                $results = $stmt->fetchAll();
                foreach ($results as $row){
                    if($_POST["delpass"] == $row['pass']){//パスワードが一致したら削除
                        //カラム削除
                        $sql = "delete from keijiban where id=:id"; //投稿番号が一致したら
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':id', $dnum, PDO::PARAM_INT);
                        $stmt->execute();
                    }
                }
            }else {echo "*「パスワード」を数字で入力してください";}
        }else {echo "*「投稿番号」を入力してください";}
    }

     //編集選択機能
    //編集フォームの送信の有無で処理を分岐
    if (!empty($_POST['editsubmit'])){
        if (!empty($_POST['edit'])){
            if (!empty($_POST["editpass"])){
                //入力データの受け取りを変数に代入
                $edit = $_POST['edit'];
                $editpass = $_POST['editpass'];
                $sql = 'SELECT * FROM keijiban where id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $edit, PDO::PARAM_INT);
                $stmt->execute();
                $results = $stmt->fetchAll();
                foreach ($results as $row){
                    //パスワードが一致していたら
                    if($_POST["editpass"] == $row['pass']){
                        $editnumber = $row['id'];
                        $editname = $row['name'];
                        $editcomment = $row['comment'];
                    }else {echo "*「パスワード」が違います";}
                }
            }else {echo "*「パスワード」を数字で入力してください";}
        }else {echo "*「投稿番号」を入力してください";}
    }

    ?>
    <form action="" method="post"><br>
    名前：　　　<input type="text" name="name" value="<?php if(isset($editname)) {echo $editname;} ?>"><br>
    コメント：　<input type="text" name="comment" value="<?php if(isset($editcomment)) {echo $editcomment;} ?>"><br>
                <input type="hidden" name="editNO" value="<?php if(isset($editnumber)) {echo $editnumber;} ?>">
    パスワード：<input type="password" id="password" name="pass" value="">
                <input type="checkbox" id="password-check">
                <input type="submit" name="submit" value="送信"><br><br>
    </form>

    <form action="" method="post">
      投稿番号：　<input type="number" name="dnum"><br>
      パスワード：<input type="password" id="password-1" name="delpass" value="">
                  <input type="checkbox" id="password-check-1">
                  <input type="submit" name="delete" value="削除"><br><br>
    </form>

    <form action="" method="post">
      投稿番号：　<input type="number" name="edit"><br>
      パスワード：<input type="password" id="password-2" name="editpass" value="">
                  <input type="checkbox" id="password-check-2">
                  <input type="submit" name=editsubmit value="編集"><br><br>
    </form>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        if (document.getElementById("password-check").checked) {
            document.getElementById("password").setAttribute("type", "text");
        }else {
            document.getElementById("password").setAttribute("type", "password");
        }
    });
    document.getElementById("password-check").addEventListener("change", function () {
        if (this.checked) {
            document.getElementById("password").setAttribute("type", "text");
        }else {
            document.getElementById("password").setAttribute("type", "password");
        }
    });
    document.addEventListener("DOMContentLoaded", function () {
        if (document.getElementById("password-check-1").checked) {
            document.getElementById("password-1").setAttribute("type", "text");
        }else {
            document.getElementById("password-1").setAttribute("type", "password");
        }
    });
    document.getElementById("password-check-1").addEventListener("change", function () {
        if (this.checked) {
            document.getElementById("password-1").setAttribute("type", "text");
        }else {
            document.getElementById("password-1").setAttribute("type", "password");
        }
    });

    document.addEventListener("DOMContentLoaded", function () {
        if (document.getElementById("password-check-2").checked) {
            document.getElementById("password-2").setAttribute("type", "text");
        }else {
            document.getElementById("password-2").setAttribute("type", "password");
        }
    });
    document.getElementById("password-check-2").addEventListener("change", function () {
        if (this.checked) {
            document.getElementById("password-2").setAttribute("type", "text");
        }else {
            document.getElementById("password-2").setAttribute("type", "password");
        }
    });
    </script>

    <?php
    echo "-------------------------------------------"."<br>";
    echo "【　投稿一覧　】"."<br>";
    $sql = 'SELECT * FROM keijiban';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            echo $row['id'].',';
            echo $row['name'].',';
            echo $row['comment'].',';
            echo $row['date'].'<br>';
        }
    ?>
</body>
</html>
