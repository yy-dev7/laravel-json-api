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
     * @var array
     */
    protected $meta;

    /**
     * BaseApiException constructor.
     *
     * @param $error
     * @param $replacement
     */
    public function __construct($error, array $replacement = [])
    {
        $this->error = $error;
        $errorConfig = config('errors.' . $error);
        $this->code = $errorConfig['code'];
        $this->detail = isset($errorConfig['detail']) ? $this->detailReplace($errorConfig['detail'], $replacement) : '';

        parent::__construct($this->detail);
    }

    protected function detailReplace($detail, array $data)
    {
        $count = preg_match_all('/{(\w+)}/', $detail, $matches);
        if ($count > 0) {
            foreach ($matches[1] as $key) {
                $detail = str_replace('{' . $key . '}', $data[$key] ?? '', $detail);
            }
        }

        return $detail;
    }

    public function withMeta(array $meta): BaseApiException
    {
        $this->meta = $meta;

        return $this;
    }

    public function toArray(): array
    {
        $json = [
            'error' => [
                'code'   => $this->code,
                'title'  => strtoupper($this->error),
                'status' => $this->status,
                'detail' => $this->detail,
            ],
        ];

        if (isset($this->meta)) {
            $json['meta'] = $this->meta;
        }

        return $json;
    }

    public function getStatus(): int
    {
        return $this->status;
    }
}