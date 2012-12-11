function setConfirmUnload(on) {
     window.onbeforeunload = (on) ? unloadMessage : null;
}

function unloadMessage() {
     return '<?php echo $alert_message; ?>';
}

jQuery(document).ready(function($){
	
	$("#<?php echo $form_id; ?>").submit(function() {
		window.onbeforeunload = null;
	});
	
	$(':input',document.<?php echo $form_id; ?>).bind("change", function() {
		setConfirmUnload(true);
	});	
	
});