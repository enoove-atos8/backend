<?php

namespace Infrastructure\Exceptions;

use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class GeneralExceptions extends Exception
{
    protected $message;
    protected $code;
    protected ?Exception $e;


    public function __construct($message, $code = 0, Exception|Throwable $e = null)
    {
        $this->message = $message;
        $this->code = $code;
        $this->e = $e;

        parent::__construct($message, $code, $e);
    }


    /**
     * Report the exception
     *
     * @return void
     */
    public function report(): void
    {
        if (app()->bound('sentry') && $this->e instanceof Throwable) {
            app('sentry')->captureException($this->e);
        }
    }


    /**
     * Report the exception as an HTTP response
     *
     * @param Request $request
     * @param Throwable $e
     * @return JsonResponse
     */
    public function render(Request $request, Throwable $e): JsonResponse
    {
        $finalResponse = [];
        $this->message = $this->getMessage();
        $this->code = $this->getCode();

        $defaultResponse = [
            'message'   => $this->message,
            'code'      => $this->code,
            'exception' => get_class($this),
            'trace'     => $this->getTrace(),
        ];

        if($this->e instanceof QueryException)
        {

            $itemsResponse = [
                'message' => 'Houve um erro interno na aplicação, em alguns instantes este problama será resolvido!',
                'code'         => 500,
                'file'         => $this->getFile(),
                'line'         => $this->getLine(),
            ];

            $finalResponse = array_merge($defaultResponse, $itemsResponse);

            return new JsonResponse($finalResponse, 500);
        }
        else
        {
            $response = array_merge($finalResponse, $defaultResponse);
            return new JsonResponse($response, $this->code);
        }
    }
}
