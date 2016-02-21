<?
class MM_Input extends CI_Input {
	public function __construct() 
	{ 
		parent::__construct(); 
	}
	public function post($index = null, $xss_clean = TRUE) 
	{
		return parent::post($index, $xss_clean); 
	} 
}
?>