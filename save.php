<?php
$request = isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) : '';
if($request !== 'xmlhttprequest') exit;

//---------------------------------------------------
// DBに接続する
//---------------------------------------------------

    $server   = "";              // 実際の接続値に置き換える
    $user     = "";                           // 実際の接続値に置き換える
    $pass     = "";                           // 実際の接続値に置き換える
    $database = "";                      // 実際の接続値に置き換える
    //-------------------
    //DBに接続
    //-------------------



try {
  // connect
  $db =  new PDO("mysql:host=" . $server . "; dbname=".$database, $user, $pass );
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $db->query("SET NAMES utf8");

  $user_txt=htmlspecialchars($_GET['transtext'], ENT_QUOTES, "utf-8");
  $insert_words=$_GET['words_array'];
  $user_id=htmlspecialchars($_GET['user_id'], ENT_QUOTES, "utf-8");

	if($user_id=="guestuser"){

		while (true) {


		$user_id = substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz'), 0, 8);//ランダムな文字列を作成
		$stmt = $db->query("select * from tools_replace_user where user_id = '".$user_id."'");//登録を確認
		$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt = $db->query("select * from `tools_replace_word` where user_id ='".$user_id."'order by id asc");//登録を確認
		$replace_words = $stmt->fetchAll(PDO::FETCH_ASSOC);

			if($users[0][text]=="" && count($replace_words)==0){

				$stmt = $db->prepare("INSERT INTO tools_replace_user (user_id,text) values (?,?)");
  				$stmt->execute(array($user_id,$user_txt));
				break; //登録が無ければ抜ける

			}
		}




	}else{
			$db->exec("UPDATE tools_replace_user SET text = '".$user_txt."' WHERE user_id = '".$user_id."'");
			$db->exec("DELETE FROM tools_replace_word WHERE user_id = '".$user_id."'");
		}



foreach ($insert_words as $words) {
	$insert=explode(',',$words);

	$stmt = $db->prepare("INSERT INTO tools_replace_word (id, user_id, texta, textb) values (?,?,?,?)");
  	$stmt->execute(array(NULL,$user_id, $insert[0] , $insert[1]));
}

  // disconnect
  $db = null;

} catch (PDOException $e) {
  echo $e->getMessage();
  exit;
}



// echo htmlspecialchars("hi! " . $_GET['name'], ENT_QUOTES, "utf-8");


$rs = array(
    //"transtext" => htmlspecialchars($_GET['transtext'], ENT_QUOTES, "utf-8"),
    //"length" => $_GET['words_array'][0],
	"user_id" => $user_id,

);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($rs);
