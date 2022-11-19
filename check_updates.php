<?php
	/*===========================================
	* @author Giulio Bellone <bellonegiulio@gmail.com>
	* @copyright 2022 Giulio Bellone
	*
	* When a person finds the bot and lauch it, it sends automatically the message
	*		'/start' to the bot. The APIs of telegram allow to read all messages sent to
			the bot with all the details about the telegram user that sent it.
	*
	*	In few words this page add the chat_id to the chat_ids' file.
	*	 (automatically add only new chat_id)
	*
	*	OUTPUT:
	*	 the page print the list of all the chat ids and the new one inserted now.
	*/
	
	/*------------------
	*	If it isn't the first message sent to the bot, offset will contain the next update id.
	*	When you call the telegram API with the offset, it automatically clear all the update before that one specified
	*/
	$offset = "";
	if( filesize("last_update_id.txt")>0 ){
		$f = fopen("last_update_id.txt", "r");
		$offset = "?offset=".((int)fread($f, filesize("last_update_id.txt")))+1;
		fclose($f);
	}
	$last_update_id = NULL;

	$TOKEN = "1234567890:FakeTokenFakeTokenFakeTokenFakeToke";
	$url = "https://api.telegram.org/bot$TOKEN/getUpdates$offset";
	
	$result = json_decode(file_get_contents($url));
	if( $result->ok===false ) die("Errore!");
	
	$chat_ids = array();
	if( filesize("chat_ids.json")>0 ){
		$f = fopen("chat_ids.json", "r");
		$chat_ids = json_decode(fread($f, filesize("chat_ids.json")));
		fclose($f);
	}
	
	$new_ids = array();
	foreach($result->result as $res){
		$last_update_id = $res->update_id;
		
		//Check if there are all the fields that I need to read
		if( !isset($res->message->text) || !isset($res->message->chat->id) || !isset($res->message->from->username) || !isset($res->message->from->first_name) || !isset($res->message->from->last_name) ){
			continue;
		}
		
		if( $res->message->text=="/start" ){
			$flag_already_exists = false;
			
			foreach($chat_ids as $chat_id){
				if( $chat_id->id == $res->message->chat->id ){
					$flag_already_exists = true;
					break;
				}
			}
			
			if( !$flag_already_exists ){
				$new_ids[] = (object)[
					"id" => $res->message->chat->id,
					"username" => $res->message->from->username,
					"name" => $res->message->from->first_name." ".$res->message->from->last_name,
				];
			}
		}
	}
	
	$out = array(
		"new_chat_ids" => $new_ids,
		"already_inserted" => $chat_ids
	);
	
	$chat_ids = array_merge($chat_ids, $new_ids);
	$chat_ids = json_encode($chat_ids);
	
	$f = fopen("chat_ids.json", "w");
	fwrite($f, $chat_ids);
	fclose($f);
	
	$f = fopen("last_update_id.txt", "w");
	fwrite($f, $last_update_id);
	fclose($f);
	
	echo json_encode($out);
?>
