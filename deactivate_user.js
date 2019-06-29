function add_value_switch() {

	var user_account_status = jQuery('#user_account_status').is(":checked");

	if (user_account_status) {
		jQuery('#user_account_status').val(1);
	} else {
		jQuery('#user_account_status').val(2);
	}

} // end of add_value_switch function