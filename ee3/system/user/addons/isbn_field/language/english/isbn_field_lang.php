<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$lang = array(
	
	'dynamic_check' 				=> 'Dynamic check of the ISBN',
	'validity_check'				=> 'Validate the ISBN on submit',
	
	//Global Settings page
	'global_settings_page_title' 	=> 'Default settings',
	'glob_set_dyn_check_label'		=> 'Dynamic check of the ISBN',
	'glob_set_dyn_check_desc'		=> 'This will dynamically check the integrity of the ISBN number.',
	'glob_set_val_check_label'		=> 'Validate the ISBN on submit',
	'glob_set_val_check_desc'		=> "This will check the integrity of the ISBN number once the entry is submited. If the ISBN number integrity isn't correct, it will throw an error message on the field.",
	'glob_set_tags'					=> '<strong>Tags</strong><br>
	There are several tags to display the ISBN number :<br>
	- {product_isbn} will display the number as it is saved<br>
	- {product_isbn:isbn10} will display the ISBN 10 number. If you entered an ISBN 13, it will be automatically converted<br>
	- {product_isbn:isbn13} will display the ISBN 13 number. If you entered an ISBN 10, it will be automatically converted<br> 
	<br>
	Options :<br>
	- pretty="yes" will display the ISBN number in a more readable form. {product_isbn:isbn13 pretty="yes"} will display 978-0-534-30510-9',
	
	//Field on Publish Page
	'pub_page_err_mess_length'		=> 'The ISBN number must be 10 or 13 characters long.',
	'pub_page_err_mess_numerical'	=> 'The ISBN number must contain numerical characters only.',
	
	//Error messages when validating the data
	'validate_data_err_mess_lenght'	=> 'The ISBN number must be 10 or 13 characters long.',
	'validate_data_err_mess_numerical' => 'The ISBN number must contain numerical characters only.',
	
	//END
);