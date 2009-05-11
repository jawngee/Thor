<?
require_once('slicehost_exception.php');
require_once('slicehost_resource_base.php');

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
 * Represents a Slice
 */
class SliceResource extends SlicehostResourceBase
{
	private $image=null;
	private $flavor=null;
	
	/**
	 * Constructor 
	 * @param $slicehost Slicehost Instance of a slicehost.
	 * @param $item mixed The element to parse properties from OR a numeric ID for the slice
	 */
	public function __construct($slicehost,$item=null)
	{
		// if a numer ID is passed in, we're going to pull the info from slicehost.
		if (is_numeric($item))
		{
			parent::__construct($slicehost,null);
			
			$slice=$slicehost->request('slices',$item);
			
			// copy the values.
			foreach($slice->_props as $key=>$value)
				$this->_props[str_replace('-','_',$key)] =$value;
		}
		else // create from the item, or create a new unsaved slice resource.
		{
			parent::__construct($slicehost,$item);

			if ($item==null)
				$this->_props['status']='new';
		}
	}
	
	/**
	 * Does the actual creation of the slice by sending the API request to slicehost.
	 * 
	 * This shit is janky.  
	 */
	private function create()
	{
		// So the deal is, as far as I can figure out, is that you have to post this stuff
		// as xml.  Which would be fine if their docs described it.
		//
		// Apparently there is another way, but I couldn't get it to work for shit.
		//
		if ($this->_props['image_id'])
		{
			$r=<<<RDOC
<?xml version="1.0" encoding="UTF-8"?>
<slice>
  <name>{$this->_props['name']}</name>
  <image-id type="integer">{$this->_props['image_id']}</image-id>
  <flavor-id type="integer">{$this->_props['flavor_id']}</flavor-id>
</slice>
RDOC;
			
		}
		else
		{
			$r=<<<RDOC
<?xml version="1.0" encoding="UTF-8"?>
<slice>
  <name>{$this->_props['name']}</name>
  <backup_id type="integer">{$this->_props['backup_id']}</backup-id>
  <flavor-id type="integer">{$this->_props['flavor_id']}</flavor-id>
</slice>
RDOC;
		}
		
		// post this shit to the slicehost API
		$result=$this->slicehost->request('slices',null,null,'post',null,$r);
		
		// assign the results to our properties.
		foreach($result as $key=>$value)
			$this->{$key}=$value;
	}
	
	/**
	 * Saves changes to a pre-existing slice.
	 * 
	 * If it's new, it will create it.
	 */
	public function save()
	{
		// if the status is new, create the slice
		if ($this->_props['status']=='new')
		{
			$this->create();
			return;
		}
		
		$r=<<<RDOC
<?xml version="1.0" encoding="UTF-8"?>
<slice>
	<id type="integer">{$this->_props['id']}</id>
  <name>{$this->_props['name']}</name>
</slice>
RDOC;
		$this->slicehost->request('slices',$this->id,null,'put',null,$r);
	}
	
	/**
	 * Deletes the slice.
	 */
	public function delete()
	{
		$r=<<<RDOC
<?xml version="1.0" encoding="UTF-8"?>
<slice>
	<id type="integer">{$this->_props['id']}</id>
</slice>
RDOC;
		$this->slicehost->request('slices',$this->id,null,'delete',null,$r);
	}
	
	/**
	 * Reboots the slice
	 * @param $hard bool True for a hard reboot, otherwise soft
	 */
	public function reboot($hard=false)
	{
		if($hard)
			$result=$this->slicehost->request('slices',$this->id,'hard_reboot','put');
		else
			$result=$this->slicehost->request('slices',$this->id,'reboot','put');
			
		return $result;
	}
	
	public function __get($key)
	{
		switch($key)
		{
			case 'image':
				if ($this->_image)
					return $this->_image;
					
				$images=$this->slicehost->images;
				foreach($images as $image)
					if ($image->id==$this->image_id)
						return $this->_image=$image;
				break;
			case 'flavor':
				if ($this->_flavor)
					return $this->_flavor;
				
				$flavors=$this->slicehost->flavors;
				foreach($flavors as $flavor)
					if ($flavor->id==$this->flavor_id)
						return $this->_flavor=$flavor;
				break;
		}
		
		return parent::__get($key);
	}
}