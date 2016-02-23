jQuery(document).ready(function() {
	var cbw_container;
	jQuery(".widget_cbw_imath_widget").css("cursor","pointer");
	jQuery(".widget_cbw_imath_widget h3").click(function(){
		cbw_container = jQuery(this).parents(".widget_cbw_imath_widget").find(".cbw_widget");
		jQuery(this).parents(".widget_cbw_imath_widget").find(".cbw_widget").slideToggle("slow");
	})
	
	jQuery(".cbw-submit").click(function(){
		var message = jQuery(this).parents(".cbw_widget").find(".cbw_wait");
		var button = jQuery(this).parents(".cbw_widget").find(".cbw-submit");
		message.removeClass("cbw-oops");
		message.removeClass("cbw-sent");
		message.html("Envoi en cours...");
		message.show();
		button.hide();
		var fromemail = jQuery(this).parents(".cbw_widget").find("input[name=cbw_from_mail]");
		var textemail = jQuery(this).parents(".cbw_widget").find("textarea[name=cbw_from_message]");
		if(!validateEmail(fromemail.val()) || fromemail.val().length == 0){
			alert("Merci de renseigner un email valide");
			jQuery(this).parents(".cbw_widget").find("input[name=cbw_from_mail]").focus();
			message.hide();
			button.show();
			return false;
		}
		if(textemail.val().length==0){
			alert("Merci de renseigner le corps de votre message !");
			jQuery(this).parents(".cbw_widget").find("textarea[name=cbw_from_message]").focus();
			message.hide();
			button.show();
			return false;
		}
		var data = {
			action: 'cbw_send',
			from:   fromemail.val(),
			txt:    textemail.val(),
			ref:    jQuery(this).parents(".cbw_widget").find("input[name=ref_cbw]").val()
			
		};
		
		jQuery.post(ajaxurl, data, function(response) {
			
			if(response=="ko"){
				message.html("Erreur !");
				message.addClass("cbw-oops");
				message.fadeOut(5000);
			}
			else{
				fromemail.val("");
				textemail.val("");
				message.html("Message envoyé !");
				message.addClass("cbw-sent");
				message.fadeOut(5000, function(){
					cbw_container.slideToggle("slow");
				});
			}
			button.show();
		});
	});
});
function validateEmail(email) {
	var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
	if( !emailReg.test( email ) ) {
		return false;
	} else {
		return true;
	}
}