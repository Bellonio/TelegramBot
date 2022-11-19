<?php
	/*===========================================
	* @author Giulio Bellone <bellonegiulio@gmail.com>
	* @copyright 2022 Giulio Bellone
	*
	* GET Parameters
	* chat_id 			(default: first in chat_ids' file)
	* username
	* text
	* photo
	* parse_mode 		(default: 'html')
	*
	* OUTPUT:
	*	 the page print a json encode string with the fields:
	* 	'success' 				(bool)
	* 	'message' 				(string)
	* 	'message_sent' 		(json string returned by the telegram API)
	============================================*/

	$TOKEN = "1234567890:FakeTokenFakeTokenFakeTokenFakeToke";
	
	//Read the chat ids' file (if the file is not empty)
	$chat_ids = array();
	if( filesize("chat_ids.json")>0 ){
		$f = fopen("chat_ids.json", "r");
		$chat_ids = json_decode(fread($f, filesize("chat_ids.json")));
		fclose($f);
	}
	
	//Check if the bot has at least 1 member to send the message
	if( count($chat_ids)==0 ){
		echo json_encode(array("success"=>false, "message"=>"There are not any member yet"));
		exit;
	}
	
	//Check if it is passed the `chat_id` and if it is in the chat ids' file
	if( isset($_GET["chat_id"]) && is_numeric(trim($_GET["chat_id"])) ){
		$x = array_filter($chat_ids, function($item){
			return $item->id == $_GET["chat_id"];
		});
		if( count($x)==1 ){
			$chat_id = trim($_GET["chat_id"]);
		}else{
			echo json_encode(array("success"=>false, "message"=>"Chat ID not recognized"));
			exit;
		}
	}else
		//Check if it is passed the `username` and if it is in the chat ids' file
		if( isset($_GET["username"]) && trim($_GET["username"])!="" ){
		$x = array_filter($chat_ids, function($item){
			return $item->username == trim($_GET["username"]);
		});
		if( count($x)==1 ){
			$chat_id = $x[0]->id;
		}else{
			echo json_encode(array("success"=>false, "message"=>"Username not recognized"));
			exit;
		}
	}else{
		//If it is not passed `chat_id` and `username`, i will use the first chat_id that I found
		$chat_id = $chat_ids[0]->id;
	}
	
	$text = NULL;
	if( isset($_GET["text"]) && trim($_GET["text"])!="" ){
		$text = trim($_GET["text"]);
	}
	
	$photo = NULL;
	if( isset($_GET["photo"]) && trim($_GET["photo"])!="" ){
		$photo = trim($_GET["photo"]);
	}
	
	//Check if there is something to send
	if( is_null($text) && is_null($photo) ){
		echo json_encode(array("success"=>false, "message"=>"You must provide the 'text' or 'photo' parameter."));
		exit;
	}
	
	
	$parse_mode = "html";
	if( isset($_GET["parse_mode"]) && trim($_GET["parse_mode"])!="" ){
		$parse_mode = trim($_GET["parse_mode"]);
	}
	if( $parse_mode=="html" && !is_null($text) ) $text = str_replace("<br>", "\n", $text);
	if( !is_null($text) ) $text = urlencode($text);
	


	if( !is_null($photo) ){
		$url = "https://api.telegram.org/bot$TOKEN/sendPhoto?chat_id=$chat_id&photo=$photo";
	}else{
		$url = "https://api.telegram.org/bot$TOKEN/sendMessage?chat_id=$chat_id&text=$text&parse_mode=$parse_mode";
	}
	$result = json_decode(file_get_contents($url));


	if($result->ok){
		echo json_encode(array("success"=>true, "message"=>"Message successfully sent.", "message_sent"=>$result->result));
	}else{
		echo json_encode(array("success"=>false, "message"=>"An error occured", "message_sent"=>$result));
	}
?>
