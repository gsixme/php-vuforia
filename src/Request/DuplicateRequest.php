<?php

namespace Gsix\Vuforia\Request;

use HTTP_Request2;

class DuplicateRequest extends AbstractRequest
{
    public function get($id)
    {
        return $this->request(
            HTTP_Request2::METHOD_GET,
            'duplicates/' . $id
        );
    }
}