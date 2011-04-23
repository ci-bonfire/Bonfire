<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	Copyright (c) 2011 Lonnie Ezell

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:
	
	The above copyright notice and this permission notice shall be included in
	all copies or substantial portions of the Software.
	
	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	THE SOFTWARE.
*/

/*
	Logs Developer file
*/
class Developer extends Admin_Controller {
	
	//--------------------------------------------------------------------
	
	public function __construct() 
	{
		parent::__construct();
		
		$this->auth->restrict('Site.Developer.View');
		$this->auth->restrict('Bonfire.Logs.View');
		
		Template::set('toolbar_title', 'System Logs');
		
		// Logging enabled?
		Template::set('log_threshold', $this->config->item('log_threshold'));
		
		Assets::add_js($this->load->view('developer/logs_js', null, true), 'inline');
		
		$this->lang->load('logs');
	}
	
	//--------------------------------------------------------------------
	
	/*
		Method: index()
		
		Lists all log files and allows you to change the log_threshold.
	*/
	public function index() 
	{
		$this->load->helper('file');
		
		// Log Files
		Template::set('logs', get_filenames($this->config->item('log_path')));
	
		Template::render();
	}
	
	//--------------------------------------------------------------------
	
	/*
		Method: enable()
		
		Saves the logging threshold value.
	*/
	public function enable() 
	{
		$this->auth->restrict('Bonfire.Logs.Manage');
	
		if ($this->input->post('submit'))
		{
			$this->load->helper('config_file');
			
			if (write_config('config', array('log_threshold' => $_POST['log_threshold'])))
			{
				Template::set_message('Log settings successfully saved.', 'success');
			} else
			{
				Template::set_message('Unable to save log settings. Check the write permissions on <b>application/config.php</b> and try again.', 'error');
			}
		}
	
		redirect('admin/developer/logs');
	}
	
	//--------------------------------------------------------------------
	
	/*
		Method: view()
		
		Shows the contents of a single log file.
		
		Parameter: 
			$file	- the full name of the file to view (including extension).
	*/
	public function view($file='') 
	{
		if (empty($file))
		{
			Template::set_message('No log file provided.', 'error');
			redirect('admin/settings/developer/logs');
		}
				
		Template::set('log_file', $file);
		Template::set('log_content', file($this->config->item('log_path') . $file));
		Template::render();
	}
	
	//--------------------------------------------------------------------
	
	/*
		Method: purge()
		
		Deletes all existing log files.
	*/
	public function purge() 
	{
		$this->auth->restrict('Bonfire.Logs.Manage');
	
		$this->load->helper('file');
		
		delete_files($this->config->item('log_path'));
	
		redirect('admin/developer/logs');
	}
	
	//--------------------------------------------------------------------
	
}