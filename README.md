Amon PHP Client For CodeIgniter
===============================

Install
------
Put Amon.php and Amon folder into CodeIgniter application/libraries folder.

How To Use It
-----------
Load amon library like any others CodeIgniter libraries.
Here's some example code:

	$this->load->library('amon');
	$this->amon->config(array('host' => 'http://127.0.0.1',
		'port' => 2464,
		'application_key' => ''));
	$this->amon->setup_exception_handler();
	error_reporting(E_ALL);

	// Logging
	$this->amon->log("your message is here");`

Note:
This libs only tested with codeigniter 2.x

