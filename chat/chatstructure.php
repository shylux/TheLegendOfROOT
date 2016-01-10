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

<style>

/*html{ 
	height: 100%;
	padding: 0;
	margin: 0;
}

body {
	overflow:hidden;
	padding: 0;
	margin: 0;
	height: 100%;
	background-color:#e9e3ee;
} */

#chatTitle {
	float:left;
	width:70px;
}

#chatControl {
	float:right;
	width:90px;font-style: italic;
	padding-right:7px;
	padding-top:3px;
	font-size: 8pt;
	text-align: right; 
}

#chatControl:hover {
	text-decoration:underline;
	cursor:pointer;
}

#chatPrison {   
	padding: 0;
	margin-bottom: -30px;
	position:fixed;
	display:block;  
	height: 100%; 
	left:0%;
	width:100%;
	z-index:1;
}

#chatBuddyBar {
	position:fixed;
	height:140px;
	border:3px dashed;
	border-color:#c2c7c4;
	background-image:url("puthere.png");
	background-repeat:no-repeat;
	background-position:center; 
	bottom:0px;
	right:212px;
	width:100%;
}

.barReady {
	float:right !important;
	margin-top:-264px;
	left:0px !important;
	top:0px !important;
	z-index:200 !important;  
}

.chatBuddyBarHidden {
	border:none !important;
	background-image:none !important;
	padding:3px !important;
}

#chatBuddyBar > ul {
	position:fixed;
	bottom:0px;
	right:217px;
	list-style-type: none; 
}

#chatBuddyBar > ul > li {
	display: inline;
	width:330px !important;
	margin-right:10px;
	margin-left:10px; 
} 

 
.glowing {
	background-color:#fcfe96;
	border-color:#fca749 !important;
}

.hidden {
	display:none;
}

.chatBuddy {
	cursor:pointer; 
}

.newMessage {
	background-color:#FB8E01 !important;
}

.chatwindow, .chatBuddy {
	font-family:arial;
}

.chatwindow {
	margin-bottom:5px;
	margin-left:5px;
	margin-right:5px;
}

.chatwindow > ul.chat {
	list-style-type: none;
	margin:0px;
	padding:0px;
	width:100%;
}
.maximized {
	width:330px !important;
	height:400px !important;
	border:1px solid;
}
.minimized { 
	width:330px;
	height: 34px !important;
	border: 1px solid;
}
.minimizedBar {
	width:330px;
	height: 34px !important;
	border: 1px solid;
	margin-top:102px !important;
}
.chatHeader {
	cursor:pointer;
	height:30px;
	background-color:#0246B6;
	color:#ffffff;
	font-weight:bold;
	padding-top:5px;
	padding-left:4px;
	text-align:left;
}
.chatContent {
	background-color:#ffffff;
	height:300px;
	font-size:10pt; 
	border-bottom:1px solid;
}
.chatSending {
	height:68px !important; 
}
.chatButton {
	margin:0px;
	padding:0px;
	width:55px !important;
	height:55px !important;
	margin-top:1px;
	background-color:#ffffff;
	border:1px solid #c6c7c8;
}
.chatButton:hover {
	background-color:#f7fbcd;
	cursor:pointer;
}
.chatMessage {
	width:260px !important;
	height:100%;
	padding:0px;
	margin:0px;
	border:0px;
}
.chatMessage:focus {
	border:none;
}
.chatList {
	width:200px;
	list-style-type: none;
	background-color:#ffffff;
	font-size:9pt; 
	color:#000000;
	margin:0px;
	padding:0px;
	max-height: 600px;
	overflow-y: scroll;
}
.chatList > li {
	height:20px;
	padding-top:4px;
	padding-left:3px; 
}
.chatList > li:hover {
	background-color:#fbfcb8;
}
#chatListContainer {
	z-index:600;
	border:1px solid;
	width:200px;
	position: fixed;
    right: 5px;;
    bottom: 5px;

}
.chatMinimize, .chatUndock {
	font-style:italic;
	font-size:8pt;
	float:right;
}
.chatMinimize:hover, .chatUndock:hover {
	text-decoration:underline;
}
.chatClose {
	padding-left:7px;
	padding-right:7px;
}
.chatClose:hover {
	background-color:#fd0006;
}
.chatStructure {
	width:330px !important;
	padding:0px;
	margin:0px;
	background-color:#ffffff;
}
.chatStructure  > tbody > tr {
	border:collapse;
}
.chatBuddyName {
	width:265px;
}
.chatBuddyMinimize {
	width:20px;
	padding-right:4px;
}
#chatListTitle {
	background-color:#0246B6;
	color:#ffffff;
	height:28px;
	padding-top:7px;
	padding-left:7px;
	font-family:arial;
	font-weight:bold;
}
</style>

<!-- CHATJAVASCRIPT -->
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script>

var online = true;
var active;
// get the own userid from the session or DB or something
var MYID='<?php echo $currentUserId; ?>'; 
var chatbuddy = 0; 
  
// from stackoverflow
function collision($div1, $div2) {
      var x1 = $div1.offset().left;
      var y1 = $div1.offset().top;
      var h1 = $div1.outerHeight(true);
      var w1 = $div1.outerWidth(true);
      var b1 = y1 + h1;
      var r1 = x1 + w1;
      var x2 = $div2.offset().left;
      var y2 = $div2.offset().top;
      var h2 = $div2.outerHeight(true);
      var w2 = $div2.outerWidth(true);
      var b2 = y2 + h2;
      var r2 = x2 + w2;

      if (b1 < y2 || y1 > b2 || r1 < x2 || x1 > r2) return false;
      return true;
}


function startListening() {

	var data = encodeURIComponent('f=li&s='+MYID);

	$.ajax({
	url: 'chat/chathandler.php',
	type: 'GET',
	async: true,
	data: data,
	success: function(data) { 

		var successObj = jQuery.parseJSON(data);

		for (var chatServerId in successObj) { 
			
			var tmp = chatServerId.split('_');
			var target = ( MYID == tmp[0] ) ? tmp[1] : tmp[0] ;

			for (var messageObj in successObj[chatServerId]) {  
				$("#chatContent" + target).html($("#chatContent" + target).html() + "<br>" + successObj[chatServerId][messageObj]['t']  +" : "+ successObj[chatServerId][messageObj]['m']);

				if ( !($("#chatMessage" + target).is(":focus") || $("#chatButton" + target).is(":focus")) ) {
					$("#chatWindow" + target).children().find('.chatHeader').addClass("newMessage");
				} 

				if ( !($("#chatMessage" + target)).is(':visible') ) { 
					var positions = getPositions(); 
					$("#chatWindow" + target).toggle(true);  

					setPositions(positions);     
					setDefaultPosition($("#chatWindow" + target)); 
		
					if ( !$("#chatWindow" + target).hasClass('ui-draggable') ) { 
						$("#chatWindow" + target).draggable({ containment: "#chatPrison", scroll: false });
						$("#chatWindow" + target).on("dragstop",function(ev,ui){
							saveCoordinates();
						});
					}
 
				}

				

			}
		}


		startListening();
	},
	error: function(e) { 
	}
	});  
 
}

function restoreCoordinates() { 
	var coordinatesData = <?php echo $positionData; ?>; 
	   
	$(".chatwindow").each(function() {  

		
		$(this).draggable({ containment: "#chatPrison", scroll: false });
		$(this).on("dragstop",function(ev,ui){
			saveCoordinates();
		});

		var settings = coordinatesData[$(this).attr('data-chatId')];

		if ( settings == null ) {
			return;
		} 

		$(this).toggle(settings.visible);  

		if ( settings.inBar ) {
			
				putToChatBar($(this));
				
				if ( settings.minimized ) {  
					minimize($(this)); 
				} 
		}
		else { 
			$(this).offset({ top: settings.top, left: settings.left }); 
  
		} 


	});
}


function getPositions() {
	var positionData = new Array();
 
	$(".chatwindow").each(function() {
 
		var minimized = ($(this).hasClass("minimized") || $(this).hasClass("minimizedBar"));
		var inBar = ($(this).hasClass("barReady")); 

		var settings = new Object({visible: $(this).is(":visible"), minimized: minimized, inBar: inBar, left: $(this).offset().left, top: $(this).offset().top });
		positionData[$(this).attr('data-chatId')] = settings; 
	});
	return positionData;
}
 
function setPositions( positionData ) {
	$(".chatwindow").each(function() {
		$(this).offset({ top: positionData[$(this).attr('data-chatId')].top, left: positionData[$(this).attr('data-chatId')].left });
	});	
} 

var checkBuddyCollision = false;
var clickedDiv;
var buddiesInBar = new Array();

function startCollisionDetecting() {

	if ( collision(clickedDiv, $("#chatBuddyBar")) )
	{
		if ( !checkBuddyCollision )
		{ 
			$("#chatBuddyBar").removeClass("glowing");
		}
		else {
			$("#chatBuddyBar").addClass("glowing");
		}
	}
	else {
		$("#chatBuddyBar").removeClass("glowing");
	} 

	if (checkBuddyCollision) {  
		setTimeout("startCollisionDetecting()", 100);  
	}
 
}

function setDefaultPosition( $chatWindow ) { 
		$chatWindow.offset({ top: 50, left: 50 });
}

function putToChatBar( $chatBuddy ) {
 
	var chatBuddyToBar = $chatBuddy.attr('data-chatId'); 

	if ( buddiesInBar.indexOf(chatBuddyToBar) == -1 ) {
		buddiesInBar.push(chatBuddyToBar);  
		
		$("#chatWindow" + chatBuddyToBar).draggable("disable");
		$("#chatWindow" + chatBuddyToBar).draggable( "destroy" );

		var clonedChat = $("#chatWindow" + chatBuddyToBar).wrap('<div>').parent().clone(true, true); 
	
		$("#chatWindow" + chatBuddyToBar).unwrap().remove(); 

		clonedChat.appendTo("#chatBuddyBar"); 

		if ( $("#chatWindow" + chatBuddyToBar).hasClass('minimized') ) {
			$("#chatWindow" + chatBuddyToBar).switchClass('minimized', 'minimizedBar');
		}

		$("#chatWindow" + chatBuddyToBar).addClass('barReady'); 
		$("#chatWindow" + chatBuddyToBar).find('.chatUndock').each(function() {
			$(this).removeClass('hidden');
		}); 

 
	}

	saveCoordinates();

}

function removeFromChatBar( $chatBuddy ) {
	
	var chatBuddyFromBar = $chatBuddy.attr('data-chatId');  
	var clonedChat = $("#chatWindow" + chatBuddyFromBar).wrap('<div>').parent().clone(true, true);

	$("#chatWindow" + chatBuddyFromBar).remove();

	clonedChat.insertBefore("#chatBuddyBar");
	 
	$("#chatWindow" + chatBuddyFromBar).removeClass('barReady');

	if ( $("#chatWindow" + chatBuddyFromBar).hasClass('minimizedBar') ) {
		$("#chatWindow" + chatBuddyFromBar).switchClass('minimizedBar', 'minimized', 0);
	}


	$("#chatWindow" + chatBuddyFromBar).find('.chatUndock').each(function() {
		$(this).addClass('hidden');
	}); 

	buddiesInBar.splice(buddiesInBar.indexOf(chatBuddyFromBar), 1);

	$("#chatWindow" + chatBuddyFromBar).unwrap();  	 

	setDefaultPosition($("#chatWindow" + chatBuddyFromBar));
 
	$("#chatWindow" + chatBuddyFromBar).removeClass("ui-draggable ui-draggable-handle ui-draggable-dragging"); 
	$("#chatWindow" + chatBuddyFromBar).draggable({ containment: "#chatPrison", scroll: false });

	saveCoordinates();
}


function minimize( $buddyWindow ) {
	var positions = getPositions();

	$buddyWindow.html( ($buddyWindow.html() == "maximize" ) ? 'minimize' : 'maximize' ); 
	$("#chatWindow" + $buddyWindow.attr('data-chatId')).children().find(".chatContent, .chatSending").toggle(($buddyWindow.html() == "maximize" ) ? false : true); 
	if ( !$("#chatWindow" + $buddyWindow.attr('data-chatId')).hasClass('barReady') )
	{  
		$("#chatWindow" + $buddyWindow.attr('data-chatId')).switchClass(($buddyWindow.html() == "maximize" ) ? 'maximized' : 'minimized', ($buddyWindow.html() == "maximize" ) ? 'minimized' : 'maximized', 0);
	}
	else {   
		$("#chatWindow" + $buddyWindow.attr('data-chatId')).switchClass(($buddyWindow.html() == "maximize" ) ? 'maximized' : 'minimizedBar', ($buddyWindow.html() == "maximize" ) ? 'minimizedBar' : 'maximized', 0);
	}
	
	setPositions(positions);

	if ( !$buddyWindow.hasClass('barReady') ) {
		putToChatBar($buddyWindow);
	}
}


function saveCoordinates() {
	
	var data = encodeURIComponent('f=sc&d='+JSON.stringify(getPositions()));

	$.ajax({
		url: 'chat/coordinates.php',
		type: 'GET',
		data: data,
		success: function(data) { 
		},
		error: function(e) { 
		}
	});
	
}
 

function sendMessage() { 
	var message		= $("#chatMessage" + chatbuddy).val();
	var data		= 'm=' + message;
	data			+= '&s=' + MYID;
	data			+= '&r=' + chatbuddy;
	data			+= '&f=sm';
 
	$("#chatMessage" + chatbuddy).val("");

	$.ajax({
		url: 'chat/chathandler.php',
		type: 'GET',
		data: encodeURIComponent(data),
		success: function(data) { 
		},
		error: function(e) { 
		}
	});
	
	 
}


$( document ).ready(function() {
	
	restoreCoordinates();
	startListening();

	$("#chatControl").click(function() {
		
		var switchmode;

		if ( $(this).html() == 'go offline')
		{
			switchmode = 'off';
			online = false;
		}
		else {
			online = true;
		}

		$(this).html( ( switchmode == 'off' ) ? 'go online' : 'go offline' );
		$(".chatList").toggle(( switchmode != 'off' ));

  
	 
		$("#chatMessage" + chatbuddy).val("");
  
		// put here ajaxrequest to DB, which is online
		// or shared memory
	});


	$('.chatMessage').bind("enterKey",function(e){
	  sendMessage();
	});
	$('.chatMessage').keyup(function(e){
		if(e.keyCode == 13)
		{
			$(this).trigger("enterKey");
		}
	}); 

	$(".chatwindow, .chat").click(function() { 
		$(this).find('.newMessage').each(function() {
			$(this).removeClass('newMessage');	
		}); 
		chatbuddy = $(this).attr('data-chatId');
		$("#chatMessage" + chatbuddy).focus();
	});

	$(".chatwindow, .chat").dblclick(function() { 
		// console.log('doubleclick');
	});


	$(".chatMinimize").click(function() { 
		minimize($(this));
	});


	$(".chatUndock").click(function() { 
		removeFromChatBar($(this));
	});
 

	$(".chatBuddy").click(function() {
		var positions = getPositions();

		$("#chatWindow" + $(this).attr('data-chatId')).toggle(true);
		
		if ( !$("#chatWindow" + $(this).attr('data-chatId')).hasClass('ui-draggable') ) { 
			$("#chatWindow" + $(this).attr('data-chatId')).draggable({ containment: "#chatPrison", scroll: false });
			$("#chatWindow" + $(this).attr('data-chatId')).on("dragstop",function(ev,ui){
				saveCoordinates();
			});
		}
		
		setPositions(positions);    
		
		setDefaultPosition($("#chatWindow" + $(this).attr('data-chatId')));
	});

	$(".chatClose").click(function() {   
		var positions = getPositions();

		$("#chatWindow" + $(this).attr('data-chatId')).toggle(false);
		
		setPositions(positions); 
		saveCoordinates();
	});

	$(".chatwindow").mousedown(function() {
		$(".chatwindow").each(function(){
			$(this).css('z-index', ""); 
		}); 

		if ( !$(this).hasClass('barReady') )
		{
			$(this).css('z-index', 9999); 
		}


		if ( $("#chatBuddyBar").find("[data-chatId=" + $(this).attr('data-chatId') + "]").length ) { 
			 
		}
		else {
			$("#chatBuddyBar").removeClass("chatBuddyBarHidden"); 
			checkBuddyCollision = true;
			clickedDiv = $(this); 
			startCollisionDetecting();
		}
	});

	$(".chatwindow").mouseup(function() {   
		checkBuddyCollision = false;
		$("#chatBuddyBar").addClass("chatBuddyBarHidden");
		if ( collision($(this), $("#chatBuddyBar")) ) {
			var positions = getPositions();
			putToChatBar($(this));
			setPositions(positions); 
		}
	});


	$(".chatButton").click(function() { 
		sendMessage();
	});
  
}); 

</script>
<!-- END CHATJAVASCRIPT --> 
 
<div id="chatListContainer">
	<div id="chatListTitle">
		<div id="chatTitle">CHAT <?php echo $currentUserId; ?></div>
		<div id="chatControl">go offline</div>
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
	<?php endforeach; 

	//	$memoryKey = ftok("/opt/lampp/htdocs/projects/chat/chat.data", 't');
	//shm_remove(shm_attach($memoryKey, 1024*1024*1024, 0666));

	?>

	<div id="chatBuddyBar" class="chatBuddyBarHidden"></div>
 
</div>