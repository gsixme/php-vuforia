<?php

namespace Gsix\Vuforia\Request;

use HTTP_Request2;

class TargetRequest extends AbstractRequest
{
	public function all()
	{
		return $this->request(
			HTTP_Request2::METHOD_GET,
			'targets'
		);
	}

	public function get($id)
	{
		dd("ok");
		return $this->request(
			HTTP_Request2::METHOD_GET,
			'targets/' . $id
		);
	}

	public function create($target)
	{
		if(is_array($target)) {
            $target = new Target($target);
        }

		if(!($target instanceof Target)) {
            throw new Exception(
            	"Invalid target type. Only array and VuforiaWebService/Target are supported"
            );            
        }

        if(empty($target->name)) {
            throw new Exception("Target name is required");  
        }

        if(!empty($this->naming_rule) && !preg_match($this->naming_rule, $target->name)) {
            throw new Exception("Invalid naming"); 
        }

        if(!is_numeric($target->width)) {
            throw new Exception("Target width is required");  
        }

        if($target->width <= 0) {
            throw new Exception("Target width should be a number");  
        }

        if(empty($target->image)) {
            throw new Exception("Target image is required");  
        }

        if(is_numeric($this->max_image_size) && strlen($target->image) > $this->max_image_size) {
            throw new Exception("Image is too large"); 
        }

        if(!empty($target->metadata) && is_numeric($this->max_meta_size) && strlen($target->metadata) > $this->max_meta_size) {
            throw new Exception("Metadata is too large"); 
        }

		return $this->request(
			HTTP_Request2::METHOD_POST,
			'targets',
			json_encode($target),
			['Content-Type' => 'application/json']
		);
	}

	public function update($id, $target)
	{
		if(is_array($target)) {
            $target = new Target($target);
        }
       	
       	if(!($target instanceof Target)) {
            throw new Exception(
            	"Invalid target type. Only array and VuforiaWebService/Target are supported"
           	);            
        }

        if(!empty($target->name)) {
            if(!empty($this->naming_rule) && !preg_match($this->naming_rule, $target->name)) {
                throw new Exception("Invalid naming"); 
            }
        }

        if(is_numeric($target->width)) {
            if($target->width <= 0) {
                throw new Exception("Target width should be a number");  
            }
        }

        if(!empty($target->image) && is_numeric($this->max_image_size) && strlen($target->image) > $this->max_image_size) {
            throw new Exception("Image is too large"); 
        }

        if(!empty($target->metadata) && is_numeric($this->max_meta_size) && strlen($target->metadata) > $this->max_meta_size) {
            throw new Exception("Metadata is too large"); 
       	}

       	return $this->request(
            HTTP_Request2::METHOD_PUT,
            'targets/' . $id,
            json_encode($target),
            ['Content-Type' => 'application/json']
        );
	}

	public function delete($id)
	{
		return $this->request(
            HTTP_Request2::METHOD_DELETE,
			'targets/' . $id
		);
	}
}