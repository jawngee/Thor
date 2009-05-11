<?
/**
*
* Copyright (c) 2009, Jon Gilkison and Massify LLC.
* All rights reserved.
*
* Redistribution and use in source and binary forms, with or without
* modification, are permitted provided that the following conditions are met:
*
* - Redistributions of source code must retain the above copyright notice,
*   this list of conditions and the following disclaimer.
* - Redistributions in binary form must reproduce the above copyright
*   notice, this list of conditions and the following disclaimer in the
*   documentation and/or other materials provided with the distribution.
*
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
* AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
* IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
* ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
* LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
* CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
* SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
* INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
* CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
* ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
* POSSIBILITY OF SUCH DAMAGE.
*
* This is a modified BSD license (the third clause has been removed).
* The BSD license may be found here:
* 
* http://www.opensource.org/licenses/bsd-license.php
*
*/

uses('sys.app.controller');
uses('sys.app.view');
uses('sys.app.layout');

class Dispatcher
{
	/**
	 * Recursively parses uri segments to find a file match
	 */
	private static function RecurseSegment(& $segments, & $result)
	{
		if ((count($segments) > 0) && (file_exists($result['root'] . $result['path'] . $segments[0] . EXT)))
		{
			$result['controller'] = $segments[0];
			$segments = array_slice($segments, 1);

			if (count($segments) >= 1)
			{
				$result['method'] = str_replace('-','_',$segments[0]);
				$segments = array_slice($segments, 1);
			}

			return true;
		}

		if ((count($segments) > 0) && (is_dir($result['root'] . $result['path'] . $segments[0])))
		{
			// Set the directory and remove it from the segment array
			$result['path'] .= $segments[0] . '/';
			$segments = array_slice($segments, 1);

			if (count($segments) == 0)
				$segments[0] = 'index';

			return self :: RecurseSegment($segments, $result);
		}

		return false;
	}

	/**
	 * Determines path to controller from uri segments
	 */
	private static function ParseURI()
	{
		// fetch the path
		$path = (isset ($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : @ getenv('PATH_INFO');
		$path = rtrim(strtolower($path), '/');
		
		// explode it's segments
		$path_array = explode('/', preg_replace('|/*(.+?)/*$|', '\\1', $path));
		$segments = array();
		
		$result['path_array']=$path_array;
		
		// If it's the root uri, this is easy fo sheezy.
		if ($path == '/' || $path == '')
		{
			// set the controller and method
			return array (
				'root' => PATH_APP.'controller/',
				'path' => '',
				'controller' => 'index',
				'method' => 'index',
				'segments' => array (),
				'path_array' => $path_array
			);
		}
		else
		{
			// parse the segments out for security
			foreach ($path_array as $val)
			{ 
				if (!preg_match('|^[a-z 0-9~%.:_-]+$|i', $val))
					$val=preg_replace('|[^a-z 0-9~%.:_-]*|i','',$val);

				$val = trim($val);

				if ($val != '')
					$segments[] = $val;
			}			
		}

		// setup the parsed uri result
		$result['root'] = PATH_APP.'controller';
		$result['path'] = '';
		$result['controller'] = 'index';
		$result['method'] = 'index';

		// Does the requested controller exist in the root folder?
		if (file_exists($result['root'] . $segments[0] . EXT))
		{
			$result['controller'] = $segments[0];
			$segments = array_slice($segments, 1);

			if (count($segments) >= 1)
			{
				$result['method'] = str_replace('-','_',$segments[0]);
				$segments = array_slice($segments, 1);
			}
		}
		// Is the controller in a sub-folder?
		else if (is_dir($result['root'] . $segments[0]))
			self :: RecurseSegment($segments, $result);
		else
			$result['method'] = str_replace('-','_',$segments[0]);
		
		if (($result['method']=='index') && (count($segments)>0))
		{
			$result['method']=str_replace('-','_',$segments[0]);
			$segments=array_slice($segments,1);
		}
		
		$result['segments']=$segments;
				
		return $result;
	}	
	
	/**
	 * Dispatches the current request.
	 */
	public static function Dispatch()
	{
		$parsed_uri=self::ParseURI();
		
		uses('app.controller.'.$parsed_uri['controller']);
		$classname=$parsed_uri['controller'].'Controller';
		
		if (!class_exists($classname))
			throw new Exception("'$classname' can not be found in '".$parsed_uri['controller']."'.");
			
		// sets the request method.  By setting X-Ajax-Real-Method header, you can override since some XMLHTTPRequest don't allow PUT, DELETE or other custom verbs.
		$reqmethod=(isset($_SERVER['HTTP_X_AJAX_REAL_METHOD'])) ? $_SERVER['HTTP_X_AJAX_REAL_METHOD'] : $_SERVER['REQUEST_METHOD'];
		
		$found_method=find_methods($classname, $reqmethod."_".$parsed_uri['method'], $parsed_uri['method'], 'index');

		if (!$found_method)
			throw new Exception("Could not find an action to call.");
			
		$root = implode('/', array_diff($parsed_uri['path_array'], $parsed_uri['segments']));
		$class=new $classname($root,$parsed_uri['segments']);

		$class->method=$reqmethod;
				
		$method=$found_method;
				
		if ($found_method=='index' || $found_method == $reqmethod.'_index') // Then ParseSegments wrongly stripped the first parameter thinking it was the method
		{
		   if($parsed_uri['method']!='index')
   			   array_unshift($parsed_uri['segments'],$parsed_uri['method']);  // so here we put that mistakenly stripped parameter back on. 
		}
				
		$parsed_uri['method']=$method;

		if ((isset ($class->ignored)) && (in_array($method, $class->ignored)))
			throw new Exception("Ignored method called.");
						
		// call the method and pass the segments (add returned data to any initially returned by screens)
		$data = call_user_func_array(array(&$class, $method), $parsed_uri['segments']);
						
		$class->session->save();

		$data['controller']=&$class;
		$data['session']=&$class->session;
		
		// TODO: Clean this up, support more types such as mobile, etc.
		$req_type=(isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? 'ajax' : 'html');
		
		if (isset($_SERVER['HTTP_X_RENDER_PARTIAL']))
			$req_type='html';
		
		if ($req_type=='html')
			$req_type=(preg_match('#iPhone#',$_SERVER['HTTP_USER_AGENT'])) ? 'iphone' : $req_type;
			
		$view_name=$parsed_uri['path'].$parsed_uri['controller'].'/'.$parsed_uri['method'];
		
		if ($req_type=='ajax')
		{
			if ($_SERVER['HTTP_X_RESPONSE_FORMAT'])
			{
				unset($data['controller']);
				unset($data['errors']);
				unset($data['session']);
				
				switch($_SERVER['HTTP_X_RESPONSE_FORMAT'])
				{
					case 'xml':
						header("Content-type: text/xml");
						print XML_serialize($data);
						return true;
					case 'json':
						header("Content-type: text/json");
						print json_encode($data);
						return true;
					case 'yaml':
						header("Content-type: text/yaml");
						print syck_dump($data);
						return true;
				}
			}
			else
				header("Content-type: text/javascript");
		}		
					
		$view_found=file_exists(PATH_APP.'view/'.$view_name.'.'.$req_type.EXT);
		
		if ((!$view_found) && ($req_type!='html') && (file_exists(PATH_APP.'view/'.$view_name.'.html'.EXT)))
		{
			$req_type='html';
			$view_found=true;
		}
			
		if (($view_found==false) && ($req_type!='ajax'))
			vomit($view_name);
							
		if ($view_found)
		{	
			$view=new View($view_name.'.'.$req_type);
			
			print $view->render($data);
		}
	}
}