<?php
if(isset($_GET['id'])){

$user_id=htmlspecialchars($_GET['id'], ENT_QUOTES, "utf-8");
//echo $user_id;

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

	 // ユーザー本文
	  $stmt = $db->query("select * from tools_replace_user where user_id = '".$user_id."'");
	  $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

	 // 単語
	  $stmt = $db->query("select * from `tools_replace_word` where user_id ='".$user_id."'order by id asc");
	  $replace_words = $stmt->fetchAll(PDO::FETCH_ASSOC);


	  // disconnect
	  $db = null;

	} catch (PDOException $e) {
	  echo $e->getMessage();
	  exit;
	}
}
//var_dump($replace_words);
?>
<?php if($users[0][text]=="" && count($replace_words)==0){
	 $user_id = "guestuser";
	 }
	 ?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>文字列変換ツール</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
<div id="wrap">
  <div class="container" style="padding:20px 0">
    <h1>文字列変換ツール</h1>
    <div class="col-sm-12" style="margin-bottom:50px;" >
    <div  class="col-sm-8" >
      <p>文字列の中の変換させたい単語を一括で変換することができます。<br>
        単語を追加したい場合は「+追加」ボタンを押してください。<br>
        「設定を保存する」ボタンを押すとURLが発行されるので、次回から設定を呼び出すことができます。
	 </p>
    </div>
    <div  class="col-sm-4" style="te:right;" >
      <p style="text-align:right;"><?php if(isset($user_id)){ echo "User ID : ".$user_id;} ?></p>
    </div>
    </div>
    <h2>変換させたい文字列</h2>
    <form class="form-horizontal" style="margin-bottom:15px;">
      <div class="form-group">
        <div class="col-sm-12">
          <textarea id="transtext" class="form-control" style="height:150px;" rows="4" cols="40"><?php echo $users[0][text]; ?></textarea>
        </div>
      </div>
    </form>
    <h2>変換する文字</h2>
    <form class="form-horizontal" style="margin-bottom:15px;">
    <?php

	if(count($replace_words)!=0){

		$words_count = 1;//単語カウント
		foreach ($replace_words as $words) {
		?>
		<div class="form-group trans">
			<label class="control-label col-sm-1"><?php echo $words_count;?></label>
			<div class="col-sm-3">
			  <input type="text" id="transA<?php echo $words_count;?>" class="form-control" value="<?php echo $words["texta"];?>">
			</div>
			<label class="control-label col-sm-1" for="email" style="text-align:center;"><i class="glyphicon glyphicon-arrow-right"></i></label>
			<div class="col-sm-3">
			  <input type="text" id="transB<?php echo $words_count;?>" class="form-control" value="<?php echo $words["textb"];?>">
			</div>
		  </div>

		<?php
		$words_count++;
		}
	}else{

	for ($i=1;$i<=3;$i+=1){?>
		<div class="form-group trans">
			<label class="control-label col-sm-1"><?php echo $i;?></label>
			<div class="col-sm-3">
			  <input type="text" id="transA<?php echo $i;?>" class="form-control">
			</div>
			<label class="control-label col-sm-1" for="email" style="text-align:center;"><i class="glyphicon glyphicon-arrow-right"></i></label>
			<div class="col-sm-3">
			  <input type="text" id="transB<?php echo $i;?>" class="form-control">
			</div>
		  </div>
		<?php
		}
		}
	?>


    </form>
    <div class="form-group">
      <div class="col-sm-12">
        <input type="button" value="+ 追加" id="add" class="btn btn-info">
      </div>
    </div>
    <div class="col-sm-12" style="text-align:center;">
      <input type="button" value="変換" class="btn btn-primary" style="width:200px; height:50px;" id="do">
    </div>
    <h2 style="margin-top:20px;">結果</h2>
    <div class="col-sm-12" style="clear:both;">
      <pre><p id="result_txt" style="padding:10px;"></p>
</pre>
    </div>
    <div class="form-group" style="clear:both;text-align:center; padding:30px 0;">
      <div class="col-sm-12">
      <a data-toggle="modal" href="#myModal" class="btn btn-warning" style="width:200px; height:50px; padding-top: 14px;" id="save">設定を保存する</a>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="myModal">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">保存が完了しました</h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
            </div>
          </div>
        </div>
      </div>
<script src="http://code.jquery.com/jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript">
$(function () {
	//最初にテキストをリザルトに反映（）
	$("#result_txt").text($('#transtext').val());
	//変換テキストに入力するたびリザルトに反映
	$('#transtext').bind('keydown keyup keypress change',function(){
		$("#result_txt").text($('#transtext').val());
    });

  $('#do').click(function() {//実行ボタン
	  $("#result_txt").html($('#transtext').val());

	  var trans_num = $('.trans').length;//変換する数

	  for (var i = 0; i < trans_num; i++) {//変換実施
	  	var trans_text = $('#result_txt').html();
		//alert(trans_text);
			if($('#transA'+(i+1)).val() !==""){
				var texta = $('#transA'+(i+1)).val();
				var textb = '<span class="text-danger">'+$('#transB'+(i+1)).val()+'</span>';

				trans_text=trans_text.replace(new RegExp(texta, 'g'),textb);

				$("#result_txt").html(trans_text);
			}

		}

  });
  $('#add').click(function() {
		var trans_num = $('.trans').length;//変換する数

		$(".trans:last").after('<div class="form-group trans" style="display:none"><label class="control-label col-sm-1">'+(trans_num+1)+'</label><div class="col-sm-3"><input type="text" id="transA'+(trans_num+1)+'" class="form-control"></div><label class="control-label col-sm-1" for="email" style="text-align:center;"><i class="glyphicon glyphicon-arrow-right"></i></label><div class="col-sm-3"><input type="text" id="transB'+(trans_num+1)+'" class="form-control"></div></div>')
			//alert(trans_num);
		  $(".trans:last").fadeIn();
	  });


  $('#save').click(function() {
	  var trans_num = $('.trans').length;//変換する数

	  words_array = new Array(trans_num);

	  for (var i = 0; i < trans_num; i++) {//変換実施
		//alert(trans_text);
			if($('#transA'+(i+1)).val() !=="" && $('#transB'+(i+1)).val() !==""){

				words_array[i]=[$('#transA'+(i+1)).val(), $('#transB'+(i+1)).val()];
				//alert(words_array[i]);

			}

		}

                $.get('save.php', {
					user_id: '<?php echo $user_id ?>',
                    transtext: $('#transtext').val(),
					'words_array[]':words_array
                }, function(data) {
					//alert(data.transtext);
					//alert(data.length);
					//alert("保存が完了しました。あなたのIDは"+data.user_id+"です。");
					//location.href="http://flatworks.info/tools/replace/?id="+data.user_id+"";
					$(".modal-body").html("<p>こちらのURLにアクセスすることで、設定を読み込むことができます。</p><a href='http://flatworks.info/tools/replace/?id="+data.user_id+"'>http://flatworks.info/tools/replace/?id="+data.user_id+"</a>");

                });

	  });

});

</script>
<style>
    .form-group{
		overflow: hidden;
	}
	h2{
		margin:37px 0 15px;
   		 font-size: 22px;
	}
	.remove_btn{
		}

    </style>
<?php if($users[0][text]=="" && count($replace_words)==0 && isset($_GET['id'])){

	 ?>
     <script type="text/javascript">
	 alert("このIDでの登録はありません。gestuserとしてご利用ください。");
	 </script>
     <?php
	 }
	 ?>
</body>
</html>
