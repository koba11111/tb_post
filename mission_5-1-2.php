<?php
    //データベース接続設定　
    $dsn = 'データべース名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    //編集処理(フォームに初期値を代入）
    if((isset($_POST["edit"]))&&(isset($_POST["edit_num"]))&&(isset($_POST["edit_pass"]))){
        $edit_num = $_POST["edit_num"];
        $edit_pass = $_POST["edit_pass"];
        if((!empty($edit_num))&&(!empty($edit_pass))){
            //データベースに対象番号のデータが存在するか確認
            $sql = ('SELECT * FROM post WHERE id=:id');
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $edit_num, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchALL();
            foreach($results as $row){
                $result = $row['id'];
            }
            if(!isset($result)){
                //対象番号が存在しない
                $edit_id = "";
                $edit_name = "名前";
                $edit_comment = "コメント";
                echo "対象の番号が存在しません";
            }else{
                //対象番号が存在する
                //編集したい投稿の値を$post_ooに代入
                $id = $edit_num;
                $sql = 'SELECT * FROM post WHERE id = :id ';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $results = $stmt->fetchALL();
                foreach ($results as $row){
                    $post_id = $row['id'];
                    $post_name = $row['name'];
                    $post_comment = $row['comment'];
                    $post_pass = $row['pass'];
                }
                //パスワードが正しいか確認
                if($edit_pass == $post_pass){
                    //正しいとき
                    //フォームの初期値を編集する投稿のデータに置き換える
                    $edit_id = $post_id;
                    $edit_name = $post_name;
                    $edit_comment = $post_comment;
                }elseif($edit_pass != $post_pass){
                    //正しくないとき
                    //フォームの初期値を最初の状態にする
                    $edit_id = "";
                    $edit_name = "名前";
                    $edit_comment = "コメント";
                    echo "パスワードが違います";
                }
            } 
        }
        //編集番号の入力がないとき   
        if(empty($edit_num)){
            //フォームの初期値を最初の状態にする
            $edit_id = "";
            $edit_name = "名前";
            $edit_comment = "コメント";
            echo "編集したい番号を入力してください<br>";
        }
        //パスワードの入力がないとき
        if(empty($edit_pass)){
            //フォームの初期値を最初の状態にする
            $edit_id = "";
            $edit_name = "名前";
            $edit_comment = "コメント";
            echo "パスワードを入力してください<br>";
        }
    }     
?>
<DOCTYPE html>
    <html lang = "ja">
        <head>
            <meta chrset = "UTF-8">
            <title>mission_5-1</title>
        </head>
        <body>
            <form action = "" method = "post">
                <!-- 投稿機能 -->
                <input type = "text" name = "name" placeholder = "<?php 
                                                                if(isset($_POST["edit"]) && !empty($_POST["edit_num"])){
                                                                    echo $edit_name;
                                                                }else{
                                                                    echo "名前";
                                                                }
                                                            ?>">
                <input type = "text" name = "comment" placeholder = "<?php 
                                                                if(isset($_POST["edit"]) && !empty($_POST["edit_num"])){ 
                                                                    echo $edit_comment;
                                                                }else{
                                                                    echo "コメント";
                                                                }
                                                            ?>">
                <input type = "password" name = "pass" placeholder = "パスワード">
                <input type = "hidden" name = "edit_check_num" value = "<?php if(isset($_POST["edit"]) && !empty($_POST["edit_num"])){
                                                                                echo $edit_id;
                                                                             }                                                        
                                                                        ?>">
                <input type = "submit" name = "post"><br>
                <!-- 削除機能-->
                <input type = "number" name = "del_num" placeholder = "番号を入力">
                <input type = "password" name = "del_pass" placeholder = "パスワード">
                <input type = "submit" name = "delete" value = "削除"><br>
                <!--編集機能-->
                <input type = "number" name = "edit_num" placeholder = "番号を入力">
                <input type = "password" name = "edit_pass" placeholder = "パスワード">
                <input type = "submit" name = "edit" value = "編集">
            </form>
            <?php
            //テーブルの作成
            $sql = "CREATE TABLE IF NOT EXISTS post"
            ." ("
            . "id INT AUTO_INCREMENT PRIMARY KEY,"
            . "name char(32),"
            . "comment TEXT,"
            . "pass TEXT,"
            . "dt date"
            .");";
            $stmt = $pdo->query($sql);

            //投稿or編集
            if(isset($_POST["post"])){
                $edit_check_num = $_POST["edit_check_num"];
                if(empty($edit_check_num)){
                    //投稿処理
                    if((isset($_POST["name"]))&&(isset($_POST["comment"]))&&(isset($_POST["pass"]))){
                        $name = $_POST["name"];
                        $comment = $_POST["comment"];
                        $pass = $_POST["pass"];
                        $dt = date("Y-m-d");
                        if((!empty($name))&&(!empty($comment))&&(!empty($pass))){
                //データベースに収納
                            $sql = $pdo->prepare("INSERT INTO post (name, comment, pass, dt) VALUES (:name, :comment, :pass, :dt)");
                            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                            $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                            $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
                            $sql -> bindParam(':dt', $dt, PDO::PARAM_STR);
                            $sql -> execute();
                //内容を表示        
                            $sql = 'SELECT * FROM post';
                            $stmt = $pdo ->query($sql);
                            $results = $stmt->fetchALL();
                            foreach ($results as $row){
                                echo $row['id'].'<br>';
                                echo $row['name'].'<br>';
                                echo $row['comment'].'<br>';
                                echo $row['dt'].'<br>';
                                echo "<hr>"; 
                            }   
                        }
                        //名前の入力がないとき
                        if(empty($name)){
                            echo "名前を入力してください<br>";
                        }
                        //コメントの入力がないとき
                        if(empty($comment)){
                            echo "コメントを入力してください<br>";
                        }
                        //パスワードの入力がないとき
                        if(empty($pass)){
                            echo "パスワードを入力してください<br>";
                        }
                    }
                }elseif(!empty($edit_check_num)){
                    //編集処理
                    $id = $edit_check_num;
                    $name = $_POST["name"];
                    $comment = $_POST["comment"];
                    $pass = $_POST["pass"];
                    $dt = date("Y-m-d");
                    if((!empty($name))&&(!empty($comment))){
                        //データベースの値を更新
                        $sql = 'UPDATE post SET name=:name,comment=:comment,pass=:pass, dt=:dt WHERE id=:id';
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                        $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
                        $stmt->bindParam(':dt', $dt, PDO::PARAM_STR);
                        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                        $stmt->execute();
                        //編集後の投稿を表示
                        $sql = 'SELECT * FROM post';
                        $stmt = $pdo ->query($sql);
                        $results = $stmt->fetchALL();
                        foreach ($results as $row){
                            echo $row['id'].'<br>';
                            echo $row['name'].'<br>';
                            echo $row['comment'].'<br>';
                            echo "<hr>";
                        }
                    echo "投稿を編集しました<br>";
                    }
                    //名前の入力がないとき
                    if(empty($name)){
                        echo "名前を入力してください<br>";
                    }
                    //コメントの入力がないとき
                    if(empty($comment)){
                        echo "コメントを入力してください<br>";
                    }
                    //パスワードの入力がないとき
                    if(empty($pass)){
                        echo "パスワードを入力してください<br>";
                    }
                }        
            }elseif(isset($_POST["delete"])){
                //削除処理
                if((isset($_POST["del_num"]))&&(isset($_POST["del_pass"]))){
                    $del_num = $_POST["del_num"];
                    $del_pass = $_POST["del_pass"];
                    $id = $del_num;
                    if((!empty($del_num))&&(!empty($del_pass))){
                        //データベースに対象番号のデータが存在するか確認
                        $sql = ('SELECT * FROM post WHERE id=:id');
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                        $stmt->execute();
                        $results = $stmt->fetchALL();
                        foreach($results as $row){
                            $result = $row['id'];
                        }
                        //対処番号が存在しないとき
                        if(!isset($result)){
                            echo "対象の番号が存在しません";
                        }else{
                        //対象番号が存在するとき
                        //データベースのidをpostsに代入
                            $sql = 'SELECT * FROM post WHERE id=:id';
                            $stmt = $pdo ->prepare($sql);
                            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                            $stmt->execute();
                            $results = $stmt->fetchALL();
                            foreach ($results as $row){
                                $posts_pass = $row['pass']; 
                            }
                            //入力されたパスワードが正しいとき
                            if($del_pass == $posts_pass){
                                //対象番号の投稿を削除
                                $id = $del_num;
                                $sql = 'DELETE FROM post WHERE id=:id';
                                $stmt = $pdo->prepare($sql);
                                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                                $stmt->execute();
                                echo "投稿を削除しました<br>";
                                echo "<hr>";
                                //投稿を表示
                                $sql = 'SELECT * FROM post';
                                $stmt = $pdo ->query($sql);
                                $results = $stmt->fetchALL();
                                foreach ($results as $row){
                                    echo $row['id'].'<br>';
                                    echo $row['name'].'<br>';
                                    echo $row['comment'].'<br>';
                                    echo $row['dt'].'<br>';
                                    echo "<hr>"; 
                                }
                            }
                            //パスワードが間違っているとき   
                            if($del_pass != $posts_pass){
                                echo "パスワードが違います<br>";
                            }
                        }
                    }
                    //番号の入力がないとき     
                    if(empty($del_num)){
                        echo "削除したい番号を入力して下さい<br>";
                    }
                    //パスワードの入力がないとき
                    if(empty($del_pass)){
                        echo "パスワードを入力してください<br>";
                    }         
                }
            }
        ?>
        </body>
    </html>
</DOCTYPE>