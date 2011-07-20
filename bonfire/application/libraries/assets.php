<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
	Class: Assets Class
	
	The Assets class works with the Template class to provide powerful theme/
	template functionality.
	
	Version: 3.0a
 */
class Assets {

	/*
		Var: $debug
		
		Whether or not debug messages should be displayed.
	*/
	private static $debug = false;

	/*
		Var: $ci
		
		An instance of the CI app
	*/
	protected static $ci;
	
	/*
		Var: $asset_base
		
		The base folder (relative to the template.site_root config setting)
		that all of the assets are stored in.
	*/
	private static $asset_base		= 'assets/';
	
	/*
		Var: $asset_folders
		
		The names of the folders for the various assets.
		These are set in the assets config file, and 
		default to 'js', 'css', and 'images'.
	*/
	private static $asset_folders 	= array(
										'css'		=> 'css',
										'js'		=> 'js',
										'images'	=> 'images'
									);

	/*
		Var: $asset_cache_folder
		
		The name of the cache folders for the various generated assets.
	*/
	private static $asset_cache_folder 	= 'cache';

	/*
		Var: $inline_scripts
		
		An array of inline scripts to be placed at the 
		end of the page.
	*/
	protected static $inline_scripts		= array();
	
	/*
		Var: $external_scripts
		
		An array of external (linked) javascript files
		to be called at the end of the page.
	*/
	protected static $external_scripts 	= array();
	
	/*
		Var: $module_scripts
		
		An array of module js code used to combined into one js file
		to be called at the end of the page.
	*/
	protected static $module_scripts 	= array();
	
	/*
		Var: $styles
		
		An array of css files to be placed at the
		beginning of the file.
	*/
	private static $styles				= array();	

	/*
		Var: $module_styles
		
		An array of module css files to be placed at the
		beginning of the file.
	*/
	private static $module_styles				= array();	

	//--------------------------------------------------------------------

	/*
		Method: __construct()
		
		This if here solely for CI loading to work. Just calls the init( ) method.
		
		Return: 
			void
	*/
	public function __construct()
	{
		self::$ci =& get_instance();
	
		self::init();
	}

	//--------------------------------------------------------------------
	
	/*
		Method: init()
		
		Load the assets config file, and inserts the base
		css and js into our array for later use. This ensures
		that these files will be processed first, in the order
		the user is expecting, prior to and later-added files.
		
		Return: 
			void
	 */
	public static function init() 
	{		
		
		/*
			It is recommended to combine as many config files as sensible into
			a single file for performance reasons. To handle these situations,
			we should check to see if the config file is already loaded before 
			loading it ourself.
		*/
		if (config_item('assets.base_folder') === false)
		{
			self::$ci->config->load('assets');
		}
	
		// Store our settings
		self::$asset_base		= self::$ci->config->item('assets.base_folder');
		self::$asset_folders	= self::$ci->config->item('assets.asset_folders');

		log_message('debug', 'Assets library loaded.');
	}
	
	//--------------------------------------------------------------------
	
	
	//--------------------------------------------------------------------
	// !STYLESHEET METHODS
	//--------------------------------------------------------------------
	
	/*
		Method: css()

		Renders links to stylesheets, with the $asset_url prepended. 
		If a single filename is passed, it will only create a single link
		for that file, otherwise, it will include any styles that have
		been added with add_css below. If no style is passed it will default
		to the theme's style.css file.
		
		When passing a filename, the filepath should be relative to the site
		root (where index.php resides).
		
		Parameters:		
			$style	- The style(s) to have links rendered for.
			$media	- The media to assign to the style(s) being passed in.
			$bypass_inheritance	- If true, will skip the check for parent theme styles.

		Return: 
		   A string containing all necessary links.
	*/
	public static function css($style=null, $media='screen', $bypass_inheritance=false) 
	{
		$styles = array();
		$return = '';
	
		// If no style(s) has been passed in, use all that have been added.
		if (empty($style))
		{
			// Make sure to include a file based on media type.
			$styles[] = array(
				'file'	=> $media,
				'media'	=> $media
			);
						
			$styles = array_merge($styles, self::$styles);
		} 
		// If an array has been passed, merge it with any added styles.
		else if (is_array($style))
		{	
			$styles = array_merge($style, self::$styles);
		}
		// If a single style has been passed in, render it only.
		else 
		{
			$styles[] = array(
				'file'	=> $style,
				'media'	=> $media
			);
		}
		
		// Add a style named for the controller so it will be looked for.
		$styles[] = self::$ci->router->class;

		$styles = self::find_files($styles, 'css', $bypass_inheritance);

		// Loop through the styles, spitting out links for each one.
		foreach ($styles as $s)
		{
			if (is_array($s))
			{
				if (substr($s['file'], -4) != '.css') 
				{ 
					$s['file'] .= '.css'; 
				}
			} else
			{
				if (substr($s, -4) != '.css') 
				{ 
					$s .= '.css'; 
				}
			}
		
			$attr = array(
				'rel'	=> 'stylesheet',
				'type'	=> 'text/css',
				'href'	=> is_array($s) ? $s['file'] : $s,
				'media'	=> !empty($s['media']) ? $s['media'] : $media
			);
			
			$return .= '<link'. self::attributes($attr) ." />\n";
		}
		$return = '';

		// add the combined css
		$return .= self::combine_css($media);
		
		$return .= self::combine_css($media, 'module');

		return $return;
	}
	
	//--------------------------------------------------------------------

	/*
		Method: combine_css()
		
		Does the actual work of generating the combined css code. All code is 
		wrapped by open and close tags specified in the config file, so that 
		you can modify it to use your favorite js library.
		
		It is called by the js() method.
		
		Parameters:
			$media	- The media to assign to the style(s) being passed in.
			$type	- either a string 'module' or empty - defines which scripts are being combined

		Return: 
			void
	 */
	public static function combine_css($media='screen', $type = '') 
	{
		// Are there any scripts to include? 
		if ($type == 'module' AND count(self::$module_styles) == 0)
		{
			return;
		}
		elseif (count(self::$styles) == 0)
		{
			return;
		}

		$output = '';
		
		// Add the theme name to the filename 
		// to account for different frontend/backend themes.
		$theme = Template::get('active_theme');
		$theme = empty($theme) ? Template::get('default_theme') : $theme;
		
		$file_name = $theme .'_combined';
		
		if ($type == 'module')
		{
			$file_name = implode('~', str_replace("/", "-", self::$module_styles[$media]));
		}
		
		$min = '';
		if (config_item('assets.css_minify'))
		{
			$min .= ".min";
		}

		// Create our style link
		$attr = array(
			'rel'	=> 'stylesheet',
			'type'	=> 'text/css',
			'href'	=> site_url(self::$asset_base . '/' . self::$asset_cache_folder . '/' . $file_name.$min.".css"),
			'media'	=> $media
		);

		if (self::generate_file($file_name, 'css', $type)) {
			$output .= '<link'. self::attributes($attr) ." />\n";
		}
		return $output;
	}
	
	//--------------------------------------------------------------------
	

	/*
		Method: add_css()
	
		Adds a file to be the CSS queue to be rendered out.
		
		Parameters:
			$style	- The style(s) to be added
			$media	- The type of media the stylesheet styles.
		
		Return:	
			void
	*/
	public static function add_css($style=null, $media='screen') 
	{
		if (empty($style)) return;

		// Add a string
		if (is_string($style))
		{
			self::$styles[] = array(
				'file'	=> $style,
				'media'	=> $media
			);
		} 
		// Add an array
		else if (is_array($style) && count($style))
		{
			foreach ($style as $s)
			{
				self::$styles[] = array(
					'file'	=> $s,
					'media'	=> $media
				);
			}
		}
	}
	
	//--------------------------------------------------------------------
	
	/*
		Method: add_module_css()
	
		Adds a module css file to the CSS queue to be rendered out.
		
		Parameters:
			$file_path	- Module path to the css file
			$media		- The type of media the stylesheet styles.
		
		Return:	
			void
	*/
	public static function add_module_css($file_path=null, $media='screen') 
	{
		if (empty($file_path)) return;
		
		// Add a string
		if (is_string($file_path))
		{
			self::$module_styles[$media][] = $file_path;
		} 
		// Add an array
		else if (is_array($file_path) && count($file_path))
		{
			foreach ($file_path as $s)
			{
				self::$module_styles[$media][] = $s;
			}
		}
	}
	
	//--------------------------------------------------------------------
	
	
	//--------------------------------------------------------------------
	// !JAVASCRIPT METHODS
	//--------------------------------------------------------------------
	
	/*
		Method: add_js()
		
		Adds scripts to the array to be served with the js() method, below.
		
		Parameters:
			$script		- The script(s) to be added to the queue.
			$type		- Either 'external' or 'inline'
		
		Return:	
			void
	*/
	public static function add_js($script=null, $type='external', $prepend=false) 
	{
		if (empty($script)) return;

		$type .= '_scripts';
		
		if (is_string($script))
		{
			if (!isset(self::$$type[$script]))
			{
				if ($prepend)
				{
					array_unshift(self::${$type}, $script);
				}
				else
				{
					array_push(self::${$type}, $script);
				}
			}
		}
		else if (is_array($script))
		{
			$temp = array();
		
			// Remove any potential duplicates
			foreach ($script as $s)
			{
				if (!isset(self::$$type[$s]))
				{
					$temp[] = $s;
				}
			}
			
			if ($prepend)
			{
				self::${$type} = array_merge($temp, self::${$type});
			}
			else
			{
				self::${$type} = array_merge(self::${$type}, $temp);
			}
		}
	}
	
	//--------------------------------------------------------------------
	
	/*
		Method: js()
		
		Renders links to stylesheets, with the $asset_url prepended. 
		If a single filename is passed, it will only create a single link
		for that file, otherwise, it will include any styles that have
		been added with add_css below. If no style is passed it will default
		to the theme's style.css file.
		
		When passing a filename, the filepath should be relative to the site
		root (where index.php resides).
		
		Paremeters:
			$script	- The name of the script to link to (optional)
			$type	- Whether the script should be linked to externally or rendered inline.
					  Acceptable values: 'external' or 'inline'
					
		Return: 
			void
	*/
	public static function js($script=null, $type='external') 
	{
		$type .= '_scripts';
		$output = '';
		
		// If a string is passed, it's a single script, so override
		// any that are already set
		if (!empty($script))
		{
			self::external_js((string)$script);
			return;
		}
		// If an array was passed, loop through them, adding each as we go.
		else if (is_array($script))
		{
			foreach ($script as $s)
			{
				self::${$type}[] = $s;
			}
		}
		
		// Render out the scripts/links
		$output  = self::external_js();
		$output .=self::inline_js();
		
		return $output;
	}
	
	//--------------------------------------------------------------------
	
	/*
		Method: external_js()
		
		Does the actual work of generating the links to the js files. 
		It is called by the js() method, but can be used on it's own.
		
		If no script are passed into the first parameter, links are created for
		all scripts within the self::$external_scripts array. If one or 
		more scripts are passed in the first parameter, only these script files
		will be used to create links with, and any stored in self::$external_scripts
		will be ignored.
		
		Note that links will not be rendered for files that cannot be found, though
		scripts will full urls are not checked, but are simply included.
		
		Parameters:
			$new_js		- either a string or an array containing the names of files to link to.
			$list		- if true, will echo out a list of scriptnames, enclosed in quotes and 
							comma separated. Convenient for using with third-party js loaders.

		Return: 
			void
	*/
	public static function external_js($new_js=null, $list=false) 
	{
		$return = '';
		$scripts = array();
		
		// If scripts were passed, they override all other scripts.
		if (!empty($new_js))
		{
			if (is_string($new_js))
			{
				$scripts[] = $new_js;
			} else if (is_array($new_js))
			{
				$scripts = $new_js;
			}
		} else 
		{
			$scripts = self::$external_scripts;
			
			// Make sure we check for a 'global.js' file.
			$scripts[] = 'global';
			
			// Add a style named for the controller so it will be looked for.
			$scripts[] = self::$ci->router->class;
		}

		
		// Try to find them
		$scripts = array();//self::find_files($scripts, 'js');
		
		$scripts[] = self::combine_js();

		$module_file = self::combine_js('module');
		if ($module_file)
		{
			$scripts[] = $module_file;
		}

		foreach ($scripts as $script)
		{
			if (substr($script, -3) != '.js') 
			{ 
				$script .= '.js'; 
			}
		
			$attr = array(
				'src'	=> strpos($script, 'http:') !== false ?
					
					// It has a full url built in, so leave it alone
					$script :
					
					// Otherwise, build the full url
					base_url() . self::$asset_base . self::$asset_folders['js'] .'/'. $script,
						'type'=>'text/javascript'
			);
			
			if ($list)
			{
				$return .= '"'. $attr['src'] .'", ';
			}
			else 
			{
				$return .= '<script'. self::attributes($attr) ." ></script>\n";
			}
		}
		
		return trim($return, ', ');
	}
	
	//--------------------------------------------------------------------
	
	/*
		Method: inline_js()
		
		Does the actual work of generating the inline js code. All code is 
		wrapped by open and close tags specified in the config file, so that 
		you can modify it to use your favorite js library.
		
		It is called by the js() method.
		
		Return: 
			void
	 */
	public static function inline_js() 
	{
		// Are there any scripts to include? 
		if (count(self::$inline_scripts) == 0)
		{
			return;
		}
	
		$output = '';
		
		// Create our shell opening
		$output .= '<script type="text/javascript">' . "\n";
		$output .= self::$ci->config->item('assets.js_opener') ."\n\n";
		
		// Loop through all available scripts
		// inserting them inside the shell.
		foreach(self::$inline_scripts as $script)
		{
			$output .= $script . "\n";
		}
		
		// Close the shell.
		$output .= "\n" . self::$ci->config->item('assets.js_closer') . "\n";
		$output .= '</script>' . "\n";
		
		return $output;
	}
	
	//--------------------------------------------------------------------
	
	/*
		Method: combine_js()
		
		Does the actual work of generating the combined js code. All code is 
		wrapped by open and close tags specified in the config file, so that 
		you can modify it to use your favorite js library.
		
		It is called by the js() method.
		
		Parameters:
			$type		- either a string 'module' or empty - defines which scripts are being combined

		Return: 
			void
	 */
	public static function combine_js($type = '') 
	{
		// Are there any scripts to include? 

		if ($type == 'module' AND count(self::$module_scripts) == 0)
		{
			return;
		}
		elseif (count(self::$external_scripts) == 0)
		{
			return;
		}
	
		$theme = Template::get('active_theme');
		$theme = empty($theme) ? Template::get('default_theme') : $theme;
		$file_name = $theme .'_combined';
		
		if ($type == 'module')
		{
			$file_name = implode('~', str_replace("/", "-", self::$module_scripts));
		}
		
		$output = '';

		// Create our shell opening
		if (self::generate_file($file_name, 'js', $type)) {
			$output .= site_url(self::$asset_base . '/' . self::$asset_cache_folder . '/' . $file_name.".js");
		}
		
		return $output;
	}
	
	//--------------------------------------------------------------------
	
	
	//--------------------------------------------------------------------
	// !IMAGE METHODS
	//--------------------------------------------------------------------
	
	/*
		Method: image()
		
		A simple helper to build image tags.
		
		Parameters:
			$image			- The name of the image file
			$extra_attrs	- An of key/value pairs that are attributes that should be added to the tag, such as height, width, class, etc.
			
		Return: 
			A string containing the image tag.
	*/
	public static function image($image=null, $extra_attrs=array()) 
	{
		if (empty($image)) return '';
		
		$attrs = array(
			'src'	=> $image,
			'alt'	=> isset($extra_attrs['alt']) ? $extra_attrs['alt'] : ''
		);
		
		unset($extra_attrs['alt']);
		
		$attrs = array_merge($attrs, $extra_attrs);
		
		return '<img'. self::attributes($attrs) ." />\n";
	}
	
	//--------------------------------------------------------------------
	
	
	//--------------------------------------------------------------------
	// !PRIVATE METHODS
	//--------------------------------------------------------------------
	
	/*
		Method: attributes()
		
		Converts an array of attribute into a string
		
		Author:
			Dan Horrigan (Stuff library)
		
		Parameters:
			$attributes	- An array of key/value pairs representing the attributes.
			
		Return: 
			A string containing the rendered attributes.
	*/
	private static function attributes($attributes=null) 
	{
		if (empty($attributes))
		{
			return '';
		}

		$final = '';
		foreach ($attributes as $key => $value)
		{
			if ($value === NULL)
			{
				continue;
			}

			$final .= ' '.$key.'="'.htmlspecialchars($value, ENT_QUOTES).'"';
		}

		return $final;
	}
	
	
	//--------------------------------------------------------------------
	
	/*
		Method: generate_file()
		
		Locates file by looping through the active and default themes, and
		then the assets folder (as specified in the config file). 
		
		Files are searched for in this order...
			1 - active_theme/
			2 - active_theme/type/
			3 - default_theme/
			4 - default_theme/type/
			5 - asset_base/type
			
		Where 'type' is either 'css' or 'js'.
		
		If the file is not found, it is removed from the array. If the file
		is found, a full url is created, using base_path(), unless the path
		already includes 'http' at the beginning of the filename, in which case
		it is simply included in the return files.
		
		For CSS files, if a script of the same name is found in both the 
		default_theme and the active_theme folders (or their type sub-folder),
		they are both returned, with the default_theme linked to first, so that
		active_theme styles can override those in the default_theme without
		having to recreate the entire stylesheet.
		
		Access: 
			private
			
		Parameters:
			$type	- Either 'css' or 'js'.
		
		Return:
			array			The complete list of files with url paths.
	 */
	private function generate_file($file_name, $file_type='css', $type='')
	{

		$cache_path = $_SERVER['DOCUMENT_ROOT'] . '/' . self::$asset_base . '/' . self::$asset_cache_folder . '/';

		// full file path - without the extension
		$file_path = $cache_path.$file_name;

		if ($file_type == 'css')
		{
			$files_array = array();
			$files_array[] = array(
				'file'	=> 'screen',
				'media'	=> 'screen'
			);
			$files_array = array_merge($files_array, self::$styles);
			
			if ($type == 'module')
			{
				$files_array = self::$module_styles['screen'];
			}
			if (config_item('assets.css_minify'))
			{
				$file_path .= ".min";
			}
		}
		else
		{
			$files_array = self::$external_scripts;
			$files_array[] = 'global';
			if ($type == 'module')
			{
				$files_array = self::$module_scripts;
			}

			if (config_item('assets.js_minify'))
			{
				$file_path .= ".min";
			}
		}
		
		$file_path .= ".".$file_type;
		
		
		$modified_time = 0;
		
		$actual_file_time = 0;
		if (is_file($file_path))
		{
			$actual_file_time = filemtime($file_path);
		}

		foreach ($files_array as $key => $file)
		{
			if ($type == 'module')
			{
				//replace chars for folder separation, replace ~ with /
				$file = str_replace("-", "/", $file);

				// if the actual asset file is not specified then assume the file is named as - TYPE.php
				if (count(explode('/', $file)) == 2)
				{
					$file .= '/'.$file_type;
				}

				$mod_file = Modules::find($file, '', 'views/');

				if (count($mod_file))
				{
					$modified_time = max(filemtime($mod_file[0].$mod_file[1].'.php'), $modified_time);
				}
				else
				{
					// no file found - remove from the array
					unset($files_array[$key]);
				}
			}
			else
			{
			
				$scripts = self::find_files(array($file), $file_type, true);

				if ($file_type == 'js')
				{
					if (count($scripts))
					{
						$app_file = $_SERVER['DOCUMENT_ROOT'] . '/'.str_replace(base_url(), '', $scripts[0]);
						$files_array[$key] = $app_file.'.'.$file_type;
					}
				}
				else
				{
					if (count($scripts))
					{
						$app_file = $_SERVER['DOCUMENT_ROOT'] . '/'.str_replace(base_url(), '', $scripts[0]['file']);
						$files_array[$key] = $app_file;
					}
				}

				if ($file == 'global')
				{
					$files_array[$key] = $app_file;
				}

				if (is_file($files_array[$key]))
				{
					
					$modified_time = max(filemtime($files_array[$key]), $modified_time);
				}
				else
				{
					// no file found - remove from the array
					unset($files_array[$key]);
				}
			}
		}

		$asset_output = '';
		if ($actual_file_time < $modified_time)
		{
			// write to the file

			foreach ($files_array as $key => $file)
			{
				if ($type == 'module')
				{
					//replace chars for folder separation, replace ~ with /
					$file = str_replace("-", "/", $file);

					// if the actual asset file is not specified then assume the file is named as - TYPE.php
					if (count(explode('/', $file)) == 2)
					{
						$file .= '/'.$file_type;
					}

					$file_output = self::$ci->load->view($file, null, TRUE);
				}
				else
				{
					$file_output = file_get_contents($file);
				}

				if (!empty($file_output))
				{
					$asset_output .= $file_output."\n";
				}
			}

			switch ($file_type)
			{
				case 'js':
					if (config_item('assets.js_minify'))
					{
						$asset_output = JSMin::minify($asset_output);
					}
					break;
				case 'css':
					if (config_item('assets.css_minify'))
					{
						$asset_output = CSSMin::minify($asset_output);
					}
					break;
				default:
					throw new LoaderException("Unknown file type - $file_type.");
					break;
			}

			self::$ci->load->helper('file');
			
			if ( !is_dir($cache_path))
			{
				@mkdir($cache_path);
			}

			if ( ! write_file($file_path, $asset_output))
			{
				return FALSE;
			}
		}
		elseif ($actual_file_time == 0)
		{
			return FALSE;
		}
		
		return TRUE;

	}

	
	//--------------------------------------------------------------------
	
	/*
		Method: find_files()
		
		Locates file by looping through the active and default themes, and
		then the assets folder (as specified in the config file). 
		
		Files are searched for in this order...
			1 - active_theme/
			2 - active_theme/type/
			3 - default_theme/
			4 - default_theme/type/
			5 - asset_base/type
			
		Where 'type' is either 'css' or 'js'.
		
		If the file is not found, it is removed from the array. If the file
		is found, a full url is created, using base_path(), unless the path
		already includes 'http' at the beginning of the filename, in which case
		it is simply included in the return files.
		
		For CSS files, if a script of the same name is found in both the 
		default_theme and the active_theme folders (or their type sub-folder),
		they are both returned, with the default_theme linked to first, so that
		active_theme styles can override those in the default_theme without
		having to recreate the entire stylesheet.
		
		Access: 
			private
			
		Parameters:
			$files	- An array of file names to search for.
			$type	- Either 'css' or 'js'.
		
		Return:
			array			The complete list of files with url paths.
	 */
	private function find_files($files=array(), $type='css', $bypass_inheritance=false) 
	{
		// Grab the theme paths from the template library.
		$paths = Template::get('theme_paths');
		$site_path = Template::get('site_path');
		$active_theme = Template::get('active_theme');
		$default_theme = Template::get('default_theme');
		
		$new_files = array();

		if (self::$debug)
		{
			echo "Active Theme = $active_theme<br/>";
			echo "Default Theme = $default_theme<br/>";
			echo 'Site Path = '. $site_path .'<br/>';
			echo 'File(s) to find: '; print_r($files);
		}
		
		foreach ($files as $file)
		{
			// If it's an array, we're dealing with css and it has both 
			// a file and media keys. Store them for later use.
			if (is_array($file))
			{	
				$media = $file['media'];
				$file = $file['file'];
			}
			
			// Strip out the file type for consistency
			$file = str_replace('.'. $type, '', $file);
		
			// If it contains an external URL, we're all done here.
			if (strpos((string)$file, 'http', 0) !== false)
			{
				$new_files[] = $file;
				continue;
			}
			
			$found = false;
		
			// We need to check all of the possible theme_paths
			foreach ($paths as $path)
			{				
				if (self::$debug) { 
					echo '[Assets] Looking in: <ul><li>'. $site_path . $path .'/'. $default_theme . $file .".{$type}</li>"; 
					echo '<li>'. $site_path . $path .'/'. $default_theme . $type .'/'. $file .".{$type}</li>";
					
					if (!empty($active_theme)) 
					{
						echo '<li>'. $site_path . $path .'/'. $active_theme . $file .".{$type}</li>";
						echo '<li>'. $site_path . $path .'/'. $active_theme . $type .'/'. $file .".{$type}</li>";
					}
					
					echo '<li>'. $site_path . self::$asset_base .'/'. $type .'/'. $file .".{$type}</li>";
					
					echo '</ul>';
				}
				
				if (!$bypass_inheritance)
				{
					/*
						DEFAULT THEME
					
						First, check the default theme. Add it to the array. We check here first so that it
						will get overwritten by anything in the active theme.
					*/
					if (is_file($site_path . $path .'/'. $default_theme . $file .".{$type}"))
					{
						$file_path = base_url() . $path .'/'. $default_theme . $file .".{$type}";
						$new_files[] = isset($media) ? array('file'=>$file_path, 'media'=>$media) : $file_path;
						$found = true;
						
						if (self::$debug) echo '[Assets] Found file at: <b>'. $site_path . $path .'/'. $default_theme . $file .".{$type}" ."</b><br/>"; 
					}
					/*
						If it wasn't found in the default theme root folder, look in default_theme/$type/
					*/
					else if (is_file($site_path . $path .'/'. $default_theme . $type .'/'. $file .".{$type}"))
					{
						$file_path = base_url() . $path .'/'. $default_theme . $type .'/'. $file .".$type";
						$new_files[] = isset($media) ? array('file'=>$file_path, 'media'=>$media) : $file_path;
						$found = true;
						
						if (self::$debug) echo '[Assets] Found file at: <b>'. $site_path . $path .'/'. $default_theme . $type .'/'. $file .".{$type}" ."</b><br/>";
					}
				}
				
				/*
					ACTIVE THEME
					
					By grabbing a copy from both the default theme and the active theme, we can
					handle simple CSS-only overrides for a theme, completely changing it's appearance
					through a simple child css file.
				*/ 
				if (!empty($active_theme) && is_file($site_path . $path .'/'. $active_theme . $file .".{$type}"))
				{
					$file_path = base_url() . $path .'/'. $active_theme . $file .".{$type}";
					$new_files[] = isset($media) ? array('file'=>$file_path, 'media'=>$media) : $file_path;
					$found = true;
					
					if (self::$debug) echo '[Assets] Found file at: <b>'. $site_path . $path .'/'. $active_theme . $file .".{$type}" ."</b><br/>";
				} 
				/*
					If it wasn't found in the active theme root folder, look in active_theme/$type/
				*/
				else if (is_file($site_path . $path .'/'. $active_theme . $type .'/'. $file .".{$type}"))
				{
					$file_path = base_url() . $path .'/'. $active_theme . $type .'/'. $file .".$type";
					$new_files[] = isset($media) ? array('file'=>$file_path, 'media'=>$media) : $file_path;
					$found = true;
					
					if (self::$debug) echo '[Assets] Found file at: <b>'. $site_path . $path .'/'. $active_theme . $type .'/'. $file .".{$type}" ."</b><br/>";
				}
				
				/*
					ASSET BASE
					
					If the file hasn't been found, yet, we have one more place to look for it: 
					in the folder specified by 'assets.base_folder', and under the $type sub-folder.
				*/
				if (!$found)
				{
					// Assets/type folder
					if (is_file($site_path . self::$asset_base .'/'. $type .'/'. $file .".{$type}"))
					{
						$file_path = base_url() . self::$asset_base .'/'. $type .'/'. $file .".{$type}";
						$new_files[] = isset($media) ? array('file'=>$file_path, 'media'=>$media) : $file_path;

						if (self::$debug) echo '[Assets] Found file at: <b>'. $site_path . self::$asset_base .'/'. $type .'/'. $file .".{$type}" ."</b><br/>";
					} 
					
					/*
						ASSETS ROOT
						
						Finally, one last check to see if it is simply under assets/. This is useful for
						keeping collections of scripts (say, TinyMCE or MarkItUp together for easy upgrade.
					*/
					else if (is_file($site_path . self::$asset_base .'/'. $file .".{$type}"))
					{
						$file_path = base_url() . self::$asset_base .'/'. $file .".{$type}";
						$new_files[] = isset($media) ? array('file'=>$file_path, 'media'=>$media) : $file_path;

						if (self::$debug) echo '[Assets] Found file at: <b>'. $site_path . self::$asset_base .'/'. $file .".{$type}" ."</b><br/>";
					} 
				}
			}			
		}
		
		return $new_files;
	}
	
	//--------------------------------------------------------------------
			
}


// END Assets class

/* End of file Assets.php */
/* Location: ./application/libraries/Assets.php */