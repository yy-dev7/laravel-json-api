<?php

namespace GzhPackages\JsonApi\Traits;

use Illuminate\Http\Response;

trait ApiHelper
{
    public function content($content = null, $addition = [])
    {
        $res = array_merge(['data' => $content], $addition);

        return response($res, Response::HTTP_OK);
    }

    public function created()
    {
        return response(null, Response::HTTP_CREATED);
    }

    public function accepted()
    {
        return response(null, Response::HTTP_ACCEPTED);
    }

    public function noContent()
    {
        return response(null, Response::HTTP_NO_CONTENT);
    }

}