<html>
	<head>
		<title>掲示板</title>
	</head>

	<body>
		<?php
			//sqlデータベースの接続
			$dsn = 'データベース名';
			$user = 'ユーザー名';
			$sqlpassword = 'パスワード';
			$pdo = new PDO($dsn, $user, $sqlpassword);

			//テーブルを作成する
			$sql = "CREATE TABLE mission4"
			."("
			."id INT,"
			."name char(32),"
			."comment TEXT,"
			."date TEXT,"
			."password TEXT"
			.");";
			$stmt = $pdo->query($sql);

			echo "掲示板へようこそ!"."<br><br>";

			//削除編集の場合、パスワードを抽出
			if( (!empty($_POST['delete_num']) || !empty($_POST['edit_num'])) && !empty($_POST['pas']) ){
				$sql = 'SELECT * FROM mission4';
				$results = $pdo -> query($sql);
				foreach($results as $row){
				//$rowの中にはテーブルのカラム名が入る
					if($_POST['delete_num'] == $row['id'] || $_POST['edit_num'] == $row['id']){
						$password = $row['password'];
						//編集フォームの編集処理（編集フォームかつパスワードが合っている場合、名前・コメントを抽出）
						if( !empty($_POST['edit_num']) && $password == $_POST['pas']){
							//この処理が楽なら下に入れた方がきれい
							$nam_data = $row['name'];
							$com_data = $row['comment'];
							$pas_change = $row['password'];
							$flag_data = $_POST['edit_num'];
						}
					}
				}
			}
			//パスワード確認用
			//echo $password."<br>";

			//削除編集フォームの場合
			if( (!empty($_POST['delete_num']) || !empty($_POST['edit_num'])) && !empty($_POST['pas'])){
				//パスワードが違う場合「パスワードが違います」
				if($password != $_POST['pas']){
					echo "パスワードが違います。<br>";
				}
				//削除フォームの削除処理
				elseif( !empty($_POST['delete_num']) ){
					$id = $_POST['delete_num'];
					$sql = "delete from mission4 where id=$id";
					$result = $pdo->query($sql);
				}
			}
			/*
			//さらに送信フォームでもない(かつ1つでも入力がある場合)場合「入力が足りません」
			elseif(){
			}
			*/
		?>

		<!-- 送信用フォーム -->
		<form method = "post" action = "mission_4.php">
			<input type = "text" name = "nam" placeholder = "名前" value = "<?php echo $nam_data ?>">
			<br>
			<input type = "text" name = "com" placeholder = "コメント" value = "<?php echo $com_data ?>">
			<br>
			<input type = "text" name = "pas" placeholder = "パスワード設定" value = "<?php echo $pas_change ?>">
			<input type = "hidden" name = "flag" placeholder = "編集対象番号（後で隠す）" value = "<?php echo $flag_data ?>">
			<input type = "submit" value = "送信">
		</form>

		<!-- 削除用フォーム -->
		<form method = "post" action = "mission_4.php">
			<input type = "text" name = "delete_num" placeholder = "削除対象番号">
			<br>
			<input type = "text" name = "pas" placeholder = "パスワード">
			<input type = "submit" value = "削除">
		</form>

		<!-- 削除用フォーム -->
		<form method = "post" action = "mission_4.php">
			<input type = "text" name = "edit_num" placeholder = "編集対象番号">
			<br>
			<input type = "text" name = "pas" placeholder = "パスワード">
			<input type = "submit" value = "編集">
		</form>
		<hr>
	</body>
</html>

<?php

	$now = date("Y/m/d H:i:s");
	//送信フォームによる編集処理
	if( !empty($_POST['nam']) && !empty($_POST['com']) && !empty($_POST['pas']) && !empty($_POST['flag']) ){
		$id = $_POST['flag'];
		$nm = $_POST['nam'];
		$cm = $_POST['com'];
		$ps = $_POST['pas'];
		$sql = "update mission4 set name='$nm', comment='$cm', password='$ps', date='$now' where id=$id";
		$result = $pdo->query($sql);
	}
	//送信フォームによる送信処理
	elseif( !empty($_POST['nam']) && !empty($_POST['com']) && !empty($_POST['pas']) ){
		//投稿番号作成
		$sql = 'SELECT * FROM mission4';
		$results = $pdo -> query($sql);
		$nextid = 0;
		foreach($results as $row){
			if($nextid < $row['id']){
				$nextid = $row['id'];
			}
		}
		$nextid++;

		//情報の挿入
		$sql = $pdo -> prepare("INSERT INTO mission4(id,name,comment,date,password) VALUES (:id,:name,:comment,:date,:password)");
		$sql -> bindParam(':id', $nextid, PDO::PARAM_STR);
		$sql -> bindParam(':name', $_POST['nam'], PDO::PARAM_STR);
		$sql -> bindParam(':comment', $_POST['com'], PDO::PARAM_STR);
		$sql -> bindParam(':date', $now, PDO::PARAM_STR);
		$sql -> bindParam(':password', $_POST['pas'], PDO::PARAM_STR);
		$sql -> execute();
	}

	//ファイル内容出力処理
	$sql = 'SELECT * FROM mission4';
	$results = $pdo -> query($sql);
	foreach($results as $row){
		echo $row['id'].' '.$row['name'].' '.$row['comment'].' '.$row['date'].'<br>';
	}
?>