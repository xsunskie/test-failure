<?php

class DX_Plugin_Settings {
	
	private $dx_setting;
	/*
	  Construct me
	 */
	public function __construct() {
		$this->dx_setting = get_option( 'dx_setting', '' );
			}
		

	public function dx_settings_callback() {
		echo _e( "Enable me", 'dxbase' );
	}
	
	public function dx_opt_in_callback() {
		$enabled = false;
		$out = ''; 
		$val = false;
		
		// check if checkbox is checked
		if(! empty( $this->dx_setting ) && isset ( $this->dx_setting['dx_opt_in'] ) ) {
			$val = true;
		}
		
		if($val) {
			$out = '<input type="checkbox" id="dx_opt_in" name="dx_setting[dx_opt_in]" CHECKED  />';
		} else {
			$out = '<input type="checkbox" id="dx_opt_in" name="dx_setting[dx_opt_in]" />';
		}
		
		echo $out;
	}		
	/*
	  Validate Settings
	  Filter the submitted data as per your request and return the array
	  @param array $input
	 */
	public function dx_validate_settings( $input ) {
		
		return $input;
	}
}
