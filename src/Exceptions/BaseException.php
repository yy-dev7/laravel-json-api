<?php


namespace GzhPackages\JsonApi\Exceptions;

use Exception;

abstract class BaseApiException extends Exception
{
    /**
     * @var string
     */
    protected $error;

    /**
     * @var int
     */
    protected $status = 500;

    /**
     * @var string
     */
    protected $detail;

    /**
     * @var int
     */
    protected $code;

    /**
     * BaseApiException constructor.
     *
     * @param $error
     * @param $replacement
     */
    public function __construct($error, array $replacement)
    {
        $this->error = $error;
        $errorConfig = config('errors.' . $error);
        $this->detail = $this->templateRender($errorConfig['detail'], $replacement);
        $this->code = $errorConfig['code'];

        parent::__construct($this->detail);
    }

    protected function templateRender($template, array $data)
    {
        $count = preg_match_all('/{(\w+)}/', $template, $matches);
        if ($count > 0) {
            foreach ($matches[1] as $key) {
                $template = str_replace('{' . $key . '}', $data[$key] ?? '', $template);
            }
        }

        return $template;
    }

    public function toArray()
    {
        return [
            'code'   => $this->code,
            'title'  => strtoupper($this->error),
            'status' => $this->status,
            'detail' => $this->detail,
        ];
    }

    public function getStatus()
    {
        return $this->status;
    }
}