<?php  
set_time_limit(0);

// http://stackoverflow.com/questions/5645412/parsing-get-request-parameters-in-a-url-that-contains-another-url
// restore the encoded get datas
$get_parameters = array();
if (isset($_SERVER['QUERY_STRING'])) {
  $pairs = explode('&', urldecode($_SERVER['QUERY_STRING']));
  foreach($pairs as $pair) {
    $part = explode('=', $pair);
    $_GET[$part[0]] = sizeof($part)>1 ? $part[1] : "";
    }
 }
 
function getMemoryId() { 
	$memoryKey = ftok("/opt/lampp/htdocs/projects/TheLegendOfROOT/chat/chat.data", 't');
	return shm_attach($memoryKey, 1024*1024*1024, 0666);
}

function getChatId( $a, $b ) { 
	return ( $a > $b ) ? "{$b}_{$a}" : "{$a}_{$b}" ;
} 

function getChatContacts() {
	$chatContacts = json_decode(shm_get_var(getMemoryId(), 1), true);
	return  ( $chatContacts !== null ) ? $chatContacts : array() ;
}

function setChatContacts( $sender, $reciever ) {
	$contactsData = getChatContacts(); 
 
	if ( !array_key_exists($sender, $contactsData) || !in_array(getChatId($sender, $reciever), $contactsData[$sender]) ) { 
		$contactsData[$sender][] = getChatId($sender, $reciever);		
		$contactsData[$reciever][] = getChatId($sender, $reciever);
		shm_put_var(getMemoryId(), 1, json_encode($contactsData)); 
	}
}

function listen( $reciever ) {
	$currentMessages = getMessages(); 
	$currentSize	 = getChatSize();
	$notChanged		 = true; 
	$changed		 = array(); 
 
	while( $notChanged ) { 

		sleep(1);

		$contactsData = getChatContacts(); 
	
		if ( array_key_exists($reciever, $contactsData) ) {

			foreach ( $contactsData[$reciever] as $currentChatId ) {
				if ( array_key_exists($currentChatId, $currentMessages) ) {
					$listen[$currentChatId] = $currentMessages[$currentChatId];
				}
				else {
					$listen[$currentChatId] = array();
				}
			}

			if ( $currentSize < getChatSize() ) {

				$currentMessages	= getMessages();   
				$contactsData		= getChatContacts();

				foreach ( $contactsData[$reciever] as $currentChatId ) {
 
					if ( !isset($listen[$currentChatId]) || count($listen[$currentChatId]) !== count($currentMessages[$currentChatId]) ) {
						if ( !isset($listen[$currentChatId]) ) {
							for ( $i = 0; $i < count($currentMessages[$currentChatId]); $i++ ) {
								$changed[$currentChatId][] = $currentMessages[$currentChatId][$i];
							} 
						}
						else {
							for ( $i = count($listen[$currentChatId]); $i < count($currentMessages[$currentChatId]); $i++ ) {
								$changed[$currentChatId][] = $currentMessages[$currentChatId][$i];
							} 
						}
						$notChanged	= false;
					}
				}  
			}

		}
	}

	echo json_encode($changed);
}

function getChatSize() {
	return strlen(shm_get_var(getMemoryId(), 0));
}

function getMessages() { 
	$messages = json_decode(shm_get_var(getMemoryId(), 0), true);
	return ( $messages ) ? $messages : "" ;
}

function sendMessage( $chatId, $message, $sender, $reciever ) {
	setChatContacts($sender, $reciever);
	$messages = getMessages(); 
	$messages[$chatId][] = array('t' => date("H:i:s", time()), 'm' => $message);
	shm_put_var(getMemoryId(), 0, json_encode($messages)); 
} 

if ( !isset($_GET['f']) ) {
	return;
}

if ( strlen( $_GET['f'] ) < 3 && strlen( $_GET['f'] ) > 1 ) { 
	if ( $_GET['f'] == 'li' ) listen($_GET['s']);
	if ( $_GET['f'] == 'sm' ) sendMessage(getChatId($_GET['s'], $_GET['r']), $_GET['m'], $_GET['s'], $_GET['r']); 
}

?>
