/**
* @package Quick Title Edition
* @copyright (c) 2016 ErnadoO
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/

(function ($) {  // Avoid conflicts with other libraries

	phpbb.addAjaxCallback('qte.attr_apply', function(data) {

		var new_attribute = data.NEW_ATTRIBUTE;
		var parent = $('h2.topic-title .qte-attr');

		if(new_attribute) {

			if ( parent.length ) {
				parent.replaceWith(new_attribute);
			}
			else {
				$('h2.topic-title').prepend(new_attribute + '&nbsp;');
			}
		}
		else {
			$('h2.topic-title').html($('h2.topic-title a'));
		}

		phpbb.closeDarkenWrapper(3000);
	});

})(jQuery); // Avoid conflicts with other libraries
