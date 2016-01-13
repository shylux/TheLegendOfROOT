<?php if ( isLoggedIn() ): ?>
	<script src="static/js/jquery-ui/jquery-ui.js"></script>
	<script src="static/js/chat.js"></script>
	<?php include("chat/chatstructure.php");?>
<?php else: ?>
	<script type="text/javascript">window.location="/";</script>
<?php endif; ?>
