<?
uses('sys.controller');

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
		
		// If it's the root uri, this is easy fo sheezy.
		if ($path == '/' || $path == '')
		{
			// set the controller and method
			return array (
				'root' => PATH_CONTROLLER,
				'path' => '',
				'controller' => 'index',
				'method' => 'index',
				'segements' => array ()
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
		$result['root'] = PATH_CONTROLLER;
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
		
		uses('controller.'.$parsed_uri['controller']);
		$classname=$parsed_uri['controller'].'Controller';
		
		if (!class_exists($classname))
			throw new Exception("'$classname' can not be found in '".$parsed_uri['controller']."'.");
			
		// sets the request method.  By setting X-Ajax-Real-Method header, you can override since some XMLHTTPRequest don't allow PUT, DELETE or other custom verbs.
		$reqmethod=(isset($_SERVER['HTTP_X_AJAX_REAL_METHOD'])) ? $_SERVER['HTTP_X_AJAX_REAL_METHOD'] : $_SERVER['REQUEST_METHOD'];
		
		$found_method=find_methods($classname, $reqmethod."_".$parsed_uri['method'], $parsed_uri['method'], 'index');

		if (!$found_method)
			throw new Exception("Could not find an action to call.");
			
		$root = implode('/', array_diff($path_array, $segments));
		$class=new $classname($root,$parsed_uri['segments']);

		$class->method=$reqmethod;
				
		$method=$found_method;
				
		if ($found_method=='index' || $found_method == $reqmethod.'_index') // Then ParseSegments wrongly stripped the first parameter thinking it was the method
		{
		   if($parsed_uri['method']!='index')
   			   array_unshift($segments,$parsed_uri['method']);  // so here we put that mistakenly stripped parameter back on. 
		}
				
		$parsed_uri['method']=$method;

		if ((isset ($class->ignored)) && (in_array($method, $class->ignored)))
			throw new Exception("Ignored method called.");
						
		// call the method and pass the segments (add returned data to any initially returned by screens)
		$data = call_user_func_array(array(&$class, $method), $segments);
						
		$class->session->save();

		$data['controller']=&$class;
		$data['session']=&$class->session;
			
		vomit($data);
	}
}