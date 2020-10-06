<?php

namespace Gsix\Vuforia\Request;

use HTTP_Request2;

class SummaryRequest extends AbstractRequest
{
	public function database()
    {
        return $this->request(
            HTTP_Request2::METHOD_GET,
            'summary'
        );
    }

    public function target($id)
    {
        return $this->request(
            HTTP_Request2::METHOD_GET,
            'summary/' . $id
        );
    }
}