//
//	file: adm/style/acp_attributes.js
//	author: pastisd
//	begin: 11/29/2010
//	version: 0.0.8 - 07/21/2013
//	licence: http://opensource.org/licenses/gpl-license.php GNU Public License
//

var qte = {};

qte.show_hide_remove_link =  function() {
	if ( $('#acp_attributes fieldset.auths').length > 1 ) {
		$('.auth_remove').show();
	} else {
		$('.auth_remove').hide();
	}
};
	
qte.nb = 0;

$(document).ready(function() {

	$('input[name=attr_type]').change(function() {
		$('#img').slideToggle();
	});
	
	qte.nb = $('#acp_attributes fieldset.auths').length;

	$(document).on('click', '.auths_add', function() {
		var clone = $('#acp_attributes fieldset:last').prev().clone().removeAttr("style");

		$('select[id^=allowed_forums]', $(clone)).attr('name', "attr_auths[" + qte.nb + "][forums_ids][]");
		$('select[id^=allowed_groups]', $(clone)).attr('name', "attr_auths[" + qte.nb + "][groups_ids][]");
		$('input[id^=allowed_author]', $(clone)).attr('name', "attr_auths[" + qte.nb + "][author]");

		$('label[for^=allowed_forums]', $(clone)).attr('for', "allowed_forums_" + qte.nb);
		$('select[id^=allowed_forums]', $(clone)).attr('id', "allowed_forums_" + qte.nb);

		$('label[for^=allowed_groups]', $(clone)).attr('for', "allowed_groups_" + qte.nb);
		$('select[id^=allowed_groups]', $(clone)).attr('id', "allowed_groups_" + qte.nb);
		
		$('label[for^=allowed_author]', $(clone)).attr('for', "allowed_author_" + qte.nb);
		$('input[id^=allowed_author]', $(clone)).attr('id', "allowed_author_" + qte.nb);

		$('select option', $(clone)).removeAttr('selected');
		$('input', $(clone)).removeAttr('checked');
		$(clone).css("display", "none");
		$('#acp_attributes fieldset:last').before(clone);
		$(clone).slideToggle();
		qte.nb++;
		qte.show_hide_remove_link();
	});

	$(document).on('click', '.auth_remove a', function() {
		$('.auth_remove a').off('click');
		$(this).parents('fieldset').slideToggle(null, function() {
			$(this).remove();
			qte.show_hide_remove_link();
		});
	});

	qte.show_hide_remove_link();

	$('input[name=set_permissions]').click( function() {
		$.post( qte_u_ajax,
		{ 'action' : 'set_permissions', 'attr_auth_id' : $('select[name=attr_auth_id]').val() },
		function( data ) {
			$('fieldset.auths').remove();
			$('fieldset.attribute').after( data );
			qte.show_hide_remove_link();
		});
	});

});