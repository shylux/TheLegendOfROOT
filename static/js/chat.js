var online = true;
var active; 
// var MYID='<?php echo $currentUserId; ?>'; 
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
						$("#chatWindow" + target).draggable({ containment: "body", scroll: false });
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
	// var coordinatesData = <?php echo $positionData; ?>; 
	   
	$(".chatwindow").each(function() {  

		
		$(this).draggable({ containment: "body", scroll: false });
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
	$("#chatWindow" + chatBuddyFromBar).draggable({ containment: "body", scroll: false });

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

		if ( $(this).html() == 'minimize')
		{
			switchmode = 'off';
			online = false;
		}
		else {
			online = true;
		}

		$(this).html( ( switchmode == 'off' ) ? 'maximize' : 'minimize' );
		$(".chatList").toggle(( switchmode != 'off' ));

  
	 
		$("#chatMessage" + chatbuddy).val("");
   
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
			$("#chatWindow" + $(this).attr('data-chatId')).draggable({ containment: "body", scroll: false });
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