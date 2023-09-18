<?php

namespace Infrastructure\Exceptions;

use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GeneralExceptions extends Exception
{
    protected $message;
    protected $code;
    protected ?Exception $e;


    public function __construct($message, $code, Exception $e = null)
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
        if (app()->bound('sentry')) {
            app('sentry')->captureException($this->e);
        }
    }


    /**
     * Report the exception as an HTTP response
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function render(Request $request): JsonResponse
    {
        $finalResponse = [];
        $this->message = $this->getMessage();
        $this->code = $this->getCode();

        $defaultResponse = [
            'message'   => $this->message,
            'code'      => $this->code,
        ];

        if($this->e instanceof QueryException)
        {

            $itemsResponse = [
                'shortMessage' => 'Houve um erro interno na aplicação, em alguns instantes este problama será resolvido!',
                'code'         => 500,
                'file'         => $this->getFile(),
                'line'         => $this->getLine(),
            ];

            $finalResponse = array_merge($defaultResponse['error'], $itemsResponse);

            return new JsonResponse($finalResponse, 500);
        }
        else
        {
            $response = array_merge($finalResponse, $defaultResponse);
            return new JsonResponse($response, $this->code);
        }
    }
}
