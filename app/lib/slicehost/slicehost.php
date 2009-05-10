<?
require_once('HTTP/Request.php');
require_once('slice_resource.php');

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
* "Slicehost" and "slice" are trademarks of Slicehost, LLC.
*/

/**
 * Management interface for slicehost.
 * 
 * This class has magical properties that lazily load information from slicehost.
 * 
 * To get all of your slices:
 * 
 * $slices=$slicehost->slices;
 * $backups=$slicehost->backups;
 * $images=$slicehost->images;
 * $flavors=$slicehost->flavors;
 * 
 * To create a new slice:
 * 
 * $slice=new SliceResource($slicehost);
 * $slice->image_id=2;
 * $slice->name='example';
 * $slice->flavor_id=1;
 * $slice->save();
 * 
 * To update a slice:
 * 
 * $slice=new SliceResource($slicehost,12345);
 * $slice->name='New Name';
 * $slice->save();
 * 
 * To delete a slice:
 * 
 * $slice=new SliceResource($slicehost,12345);
 * $slice->delete();
 * 
 * 
 */
class Slicehost
{
	/**
	 * Slicehost API key.
	 * 
	 * @var string
	 */
	private $key='';
	
	/**
	 * Constructor
	 * 
	 * @param $key string The Slicehost API key
	 */
	public function __construct($key)
	{
		$this->key=$key;
	}
	
	/**
	 * Builds an object or an array of object from the request's response body
	 * 
	 * @param $body string The response body from the request.
	 * @return mixed An array of objects if the server returned an array response, otherwise a single instance.
	 */
	private function build_response($body)
	{
		$xml=simplexml_load_string($body);

		// check to see if the server returned an array of objects
		$type=(String)$xml['type'];
		if ($type=='array')
		{
			// grab the children nodes
			$children=$xml->xpath("/{$xml->getName()}/*");
			
			// loop through
			foreach($children as $child)
			{
				// try to find the corresponding class
				// if that fails, we use the SlicehostResourceBase class as a generic
				$class=$child->getName().'Resource';
				if (!class_exists($class))
					$class="SlicehostResourceBase";
				
				// create the object and add it to the results
				$result[]=new $class($this,$child);
			}
			
			return $result;
		}
		else
		{
			// php bug, you need to test for existance (even though it exists) before I can call it's getName() method
			// don't believe me?  comment out the if statement.
			if ($xml)
				$name=$xml->getName();

			// try to find the corresponding class
			// if that fails, we use the SlicehostResourceBase class as a generic
			$class=$name.'Resource';
			if (!class_exists($class))
				$class="SlicehostResourceBase";
				
			return new $class($this,$xml);
		}
	}
	
	/**
	 * Generates an API request
	 * 
	 * @param $path string The path, eg "slices"
	 * @param $id string The id of the object, optional and really only used for slices and zones
	 * @param $action string Any action to take on the object, for instance "reboot"
	 * @param $method string The HTTP method for the request
	 * @param $parameters array Any post/query string parameters
	 * @param $postbody string The raw postbody, used in lieu of $parameters
	 * @return string The response body
	 */
	public function request($path,$id=null,$action=null,$method='GET',$parameters=null,$postbody=null)
	{
		// append slashes where needed
		if ($id!='')
			$path.='/';
		if (($action!=null) && ($id!=null))
			$id.='/';
			
		// build the url
		$url="https://api.slicehost.com/{$path}{$id}{$action}.xml";
		
		// create a request
		$request=new HTTP_Request($url);

		// set the auth
		$request->setBasicAuth($this->key,'');
		
		// set the method
		$method=strtoupper($method);
		$request->setMethod($method);
		
		// $postbody was specified, so this is an xml request
		if ($postbody)
		{
			$request->addHeader('Content-Type','text/xml');
			$request->addRawPostData($postbody);
		}
			
		// since we are posting well formed xml documents instead of
		// post data, all the parameters will be appended as query strings
		// since they are only used in the api in weird places.
		foreach($paramters as $key=>$value)
			$request->addQueryString($key,$value);
		
		// send it off
		$request->sendRequest(true);
		
		// if success, build the response objects, otherwise throw an exception.
		$repcode=$request->getResponseCode();
		switch($repcode)
		{
			case 200:
			case 201:
			case 204:
				return $this->build_response($request->getResponseBody());
			default:
				throw new SlicehostException("$url.\nResponse Code $repcode.\n\n{$request->getResponseBody()}\n\n");
		}
		
		
	}
	
	/**
	 * Magical properties to lazy load
	 * 
	 * @param $key
	 * @return array
	 */
	public function __get($key)
	{
		switch($key)
		{
			case 'flavors':
			case 'images':
			case 'backups':
			case 'slices':
				return $this->request($key);
			default:
				throw new SlicehostException("Unknown object '$key'.");
		}
	}
	
}