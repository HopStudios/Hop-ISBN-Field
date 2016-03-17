<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Isbn_field_ft extends EE_Fieldtype {

    var $info = array(
        'name'      => 'ISBN Field',
        'version'   => '0.1'
    );
	
	/**
	 * Install Fieldtype
	 *
	 * @access	public
	 * @return	default global settings
	 *
	 */
	function install()
	{
		return array(
			'dynamic_check'		=> 'yes',
			'validity_check'	=> 'no'
		);
	}

	/*
	 * Generates/displays a Global Settings page for the field
	 */
    function display_global_settings()
	{
		ee()->lang->loadfile('isbn_field');
		
		$val = array_merge($this->settings, $_POST);
		
		$form = '';
		$form .= '<h3>'.lang('global_settings_page_title').'</h3>';
		
		$form .= form_label(lang('glob_set_dyn_check_label'), 'dynamic_check').NBS.form_checkbox('dynamic_check', 'yes', $val["dynamic_check"] == 'yes').NBS.NBS.NBS.' ';
		$form .= '<p>'.lang('glob_set_dyn_check_desc').'</p>';
		
		$form .= form_label(lang('glob_set_val_check_label'), 'validity_check').NBS.form_checkbox('validity_check', 'yes', $val["validity_check"] == 'yes').NBS.NBS.NBS.' ';
		$form .= '<p>'.lang('glob_set_val_check_desc').'</p>';
		
		$form .= '<p>'.lang('glob_set_tags').'</p>';
		
		return $form;
	}
	
	/*
	 * Save the Global Settings of the field
	 */
	function save_global_settings()
	{
		$settings = $this->settings;
		
		$settings['dynamic_check'] = ee()->input->post('dynamic_check')=='yes' ? 'yes' : 'no';
		
		$settings['validity_check'] = ee()->input->post('validity_check')=='yes' ? 'yes' : 'no';
		
		return $settings;
	}
	
	/*
	 * Display settings on the Custom Fields add/edit page
	 */
	function display_settings($data)
	{
		ee()->lang->loadfile('isbn_field');
		$dynamic_check_str	= isset($data['dynamic_check']) ? $data['dynamic_check'] : $this->settings['dynamic_check'];
		
		$validity_check_str	= isset($data['validity_check']) ? $data['validity_check'] : $this->settings['validity_check'];
		
		ee()->table->add_row(
			lang('dynamic_check', 'dynamic_check'),
			form_checkbox('dynamic_check', 'yes', $dynamic_check_str == 'yes')
		);
		
		ee()->table->add_row(
			lang('validity_check', 'validity_check'),
			form_checkbox('validity_check', 'yes', $validity_check_str == 'yes')
		);
	}
	
	/*
	 * Save settings of that particular custom field
	 */
	function save_settings($data)
	{
		//$settings = $this->settings;
		$settings = array();
		$settings['dynamic_check'] = ee()->input->post('dynamic_check')=='yes' ? 'yes' : 'no';
		
		$settings['validity_check'] = ee()->input->post('validity_check')=='yes' ? 'yes' : 'no';
		
		return $settings;
	}

	/*
	 * Display the field on the publish page
	 *
	 * @param $data Current field data, blank for new entries
	 * @return String the field to display on Publish page
	 */
    function display_field($data)
    {
		ee()->lang->loadfile('isbn_field');
		
		$form = form_input(array(
            'name'  => $this->field_name,
            'id'    => $this->field_id,
            'value' => $data
        ));
		
		if ($this->settings['dynamic_check'] == "yes")
		{
			ee()->javascript->output('
				window["isbn_'.$this->field_id.'"] = "";
				function verify_isbn_'.$this->field_id.'()
				{
					if ($("#'.$this->field_id.'").val() != window["isbn_'.$this->field_id.'"])
					{
						window["isbn_'.$this->field_id.'"] = $("#'.$this->field_id.'").val();
						console.log("ISBN has changed : " + window["isbn_'.$this->field_id.'"]);
						var valid = true;
						if (window["isbn_'.$this->field_id.'"].length != 10 && window["isbn_'.$this->field_id.'"].length != 13)
						{
							$("#isbn_dyn_check_'.$this->field_id.'_length").show();
							valid = false;
						}
						else
						{
							$("#isbn_dyn_check_'.$this->field_id.'_length").hide();
						}
						if (! /^\d+$/.test(window["isbn_'.$this->field_id.'"]) && window["isbn_'.$this->field_id.'"].length != 0)
						{
							$("#isbn_dyn_check_'.$this->field_id.'_numerical").show();
							valid = false;
						}
						else
						{
							$("#isbn_dyn_check_'.$this->field_id.'_numerical").hide();
						}
						if (valid)
						{
							$("#isbn_search_link_'.$this->field_id.'").attr("href", "http://www.google.com/search?q=ISBN+"+window["isbn_'.$this->field_id.'"]);
							$("#isbn_search_'.$this->field_id.'").show();
							$("#isbn_search_link_'.$this->field_id.'").html("ISBN "+window["isbn_'.$this->field_id.'"]);
						}
						else
						{
							$("#isbn_search_'.$this->field_id.'").hide();
						}
					}
				}
				$("#'.$this->field_id.'").bind("input change", function(){
					verify_isbn_'.$this->field_id.'();
				});
				$(function() {
					verify_isbn_'.$this->field_id.'();
				});
			');
			$form .= '
			<div id="isbn_dyn_check_'.$this->field_id.'">
				<p id="isbn_dyn_check_'.$this->field_id.'_length" style="display:none;color:red;">'.lang('pub_page_err_mess_length').'</p>
				<p id="isbn_dyn_check_'.$this->field_id.'_numerical" style="display:none;color:red;">'.lang('pub_page_err_mess_numerical').'</p>
				<p id="isbn_search_'.$this->field_id.'" style="display:none;">Search for <a href="#" id="isbn_search_link_'.$this->field_id.'" target="blank">ISBN ...</a></p>
			</div>';
		}
		
        return $form;
    }
	
	/*
	 * Replace the field tag on a template
	 */
	function replace_tag($data, $params = array(), $tagdata = FALSE)
	{
		return $this->_process_tag_isbn($data, $params);
	}
	
	function replace_isbn13($data, $params = array(), $tagdata = FALSE)
	{
		//if it's too short, we need te recalculate it
		if (strlen($data)==10 && preg_match("/^[0-9xX]*$/", $data))
		{
			$data = $this->_convert_isbn10_to_isbn13($data);
		}
		
		return $this->_process_tag_isbn($data, $params);
	}
	
	function replace_isbn10($data, $params = array(), $tagdata = FALSE)
	{
		if (strlen($data)==13)
		{
			$data = $this->_convert_isbn13_to_isbn10($data);
		}
		
		return $this->_process_tag_isbn($data, $params);
	}
	
	/*
	 * Process the ISBN number with params placed on the tag
	 * @return String the ISBN number modified
	 */
	function _process_tag_isbn($isbn, $params)
	{
		if (is_array($params) && array_key_exists('pretty', $params) && $params["pretty"]=="yes")
		{
			$isbn = $this->pretty_isbn_format($isbn);
		}
		return $isbn;
	}
	
	function pretty_isbn_format($isbn)
	{
		if (strlen($isbn) == 10)
		{
			return substr($isbn, 0, 1).'-'.substr($isbn, 1, 3).'-'.substr($isbn, 4, 5).'-'.substr($isbn, 9, 1);
		}
		else if (strlen($isbn) == 13)
		{
			return substr($isbn, 0, 3).'-'.substr($isbn, 3, 1).'-'.substr($isbn, 4, 3).'-'.substr($isbn, 7, 5).'-'.substr($isbn, 12, 1);
		}
		return $isbn;
	}
	
	function _convert_isbn10_to_isbn13($isbn)
	{
		if (strlen($isbn)==10 && preg_match("/^[0-9xX]*$/", $isbn))
		{
			//according to http://en.wikipedia.org/wiki/International_Standard_Book_Number#ISBN-10_to_ISBN-13_conversion
			// we just need to add 978 in front of the ISBN number
			// and calculate the new checksum to replace the last digit
			$isbn = '978'.substr($isbn, 0, -1);
			$sum = 0;
			for ($i = 0; $i<strlen($isbn); $i++)
			{
				if ($isbn[$i] == 'X' || $isbn[$i] == 'x')
				{
					$val = 10;
				}
				else
				{
					$val = intval($isbn[$i]);
				}
				if ($i%2)
				{
					$sum += ($val * 3);
				}
				else
				{
					$sum += $val;
				}
			}
			$rem = $sum%10;
			if ($rem==0)
			{
				$checksum = 0;
			}
			else
			{
				$checksum = 10 - $rem;
			}
			$isbn .= $checksum;
		}
		return $isbn;
	}
	
	function _convert_isbn13_to_isbn10($isbn)
	{
		if (strlen($isbn)==13)
		{
			$isbn = substr($isbn, 3);
			
			//recalculate the checksum to replace the last digit
			//Note : the last digit can be '10', in that case, it's replaced by an 'X'
			$isbn = substr($isbn, 0, -1);
			$sum = 0;
			for ($i = 0; $i<strlen($isbn); $i++)
			{
				$sum += (intval($isbn[$i]) * (10-$i));
			}
			$sum = 11 - ($sum%11);
			$checksum = $sum%11;
			if ($checksum == 10) $checksum = "X";
			$isbn .= $checksum;
		}
		return $isbn;
	}
	
	/*
	 * Validate the data once the entry is submited
	 *
	 * @param String $data Current field data, blank for new entries
	 * @return mixed TRUE if OK | String error message
	 */
	function validate($data)
	{
		ee()->lang->loadfile('isbn_field');
		
		//if we need to validate the entered value
		if ($this->settings['validity_check'] == "yes")
		{
			$errorMessage = "";
			if ( !preg_match("/^[0-9]*$/", $data) )
			{
				$errorMessage .= lang('validate_data_err_mess_numerical')." ";
			}
			if (strlen($data) != 10 && strlen($data) != 13)
			{
				$errorMessage .= lang('validate_data_err_mess_lenght')." ";
			}
			if ($errorMessage != "")return $errorMessage;
		}
		return TRUE;
	}
}
// END class