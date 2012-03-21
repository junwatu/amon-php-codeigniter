Amon PHP Client For CodeIgniter
===============================

Install
------
Put Amon.php and Amon folder into CodeIgniter application/libraries folder and put amonconfig.php 
into application/config folder.

How To Use It
-----------
Edit amonconfig.php to change amon url, port or application key the default value is
	
	$config['amon_host'] = 'http://localhost';
	$config['amon_port'] = '2464';
	$config['amon_application_key'] = '';
Load amonconfig.php into codeigniter system with

	$this->load-config('amonconfig');
 
or use config autoload feature from codeigniter with editing file autoload.php in application/config folder 

	$autoload['config'] = array('amonconfig');

Load amon library like any others CodeIgniter libraries.
Here's some example code:

	$this->load->library('amon');
	$this->amon->config(array('host' => $this->config->item('amon_host'),
		'port' => $this->config->item('amon_port'),
		'application_key' => $this->config->item('amon_application_key'));
	$this->amon->setup_exception_handler();
	error_reporting(E_ALL);

	// Logging
	$this->amon->log("your message is here");`

Note:
This libs only tested with codeigniter 2.x

