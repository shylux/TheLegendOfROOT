<?php 
 
$currentUserId = (int)$_SESSION["user"]->buddy_id;
$positionData = "[]";  

if (session_status() == PHP_SESSION_NONE) {
    session_start();   
}
else { 
	if ( isset($_SESSION['chat']['position']) ) { 
		$positionData = json_encode($_SESSION['chat']['position']); 
	} 
}
 
$users = array();

$usersData = $GLOBALS["db"]->selectAll("users");
foreach ( $usersData as $nr => $value ) {
	$users[$value['buddy_id']] = $value['name'];
}

?>
<script>
   var MYID='<?php echo $currentUserId; ?>'; 
   var coordinatesData = <?php echo $positionData; ?>; 
</script>

<div id="chatPrison">

	<div id="chatListContainer">
		<div id="chatListTitle">
			<div id="chatTitle">CHAT</div>
			<div id="chatControl">minimize</div>
		</div> 
		<ul class="chatList">
		<?php foreach ($users as $userId => $userName): if ( $userId === $currentUserId ) { continue; } ?> 
			<li>
				<div class="chatBuddy" data-chatId="<?php echo $userId; ?>"><?php echo $userName; ?></div>
			</li>
		<?php endforeach; ?>
		</ul>
	</div> 

	<div id="chatPrison">  
		<?php foreach ($users as $userId => $userName): if ( $userId === $currentUserId ) { continue; } ?> 
		<div id="chatWindow<?php echo $userId; ?>" data-chatId="<?php echo $userId; ?>" class="hidden chatwindow maximized">
			<ul class="chat">
				<li class="chatHeader">
				<table>
					<tr>
						<td class="chatBuddyName"><?php echo $userName; ?></td>
						<td class="chatBuddyUndock"><div class="chatUndock hidden" data-chatId="<?php echo $userId; ?>">undock</div></td>
						<td class="chatBuddyMinimize"><div class="chatMinimize" data-chatId="<?php echo $userId; ?>">minimize</div></td>
						<td class="chatClose" data-chatId="<?php echo $userId; ?>">x</td>
					</tr>
				</table>
				</li>
				<li class="chatContent" id="chatContent<?php echo $userId; ?>"><div></div></li>
				<li class="chatSending">
					<table class="chatStructure">
						<tr>
							<td><textarea class="chatMessage" id="chatMessage<?php echo $userId; ?>"></textarea></td>
							<td><button class="chatButton" id="chatButton<?php echo $userId; ?>">send</button></td>
						</tr>
					</table>
				</li>
			</ul>
		</div> 
		<?php endforeach; ?>
		<div id="chatBuddyBar" class="chatBuddyBarHidden"></div>
	</div>
</div>