<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
          if ($e instanceof InternalServerException) {
               $data = array(
                    'message' => '',
                    'code' => '500',
               );
               return response()->view('errors.500', $data, 500);
          }

          if ($e instanceof NotFoundHttpException) {
               $data = array(
                    'message' => '',
                    'code' => '404',
               );
               return response()->view('errors.404', $data, 404);
          }

          if ($e instanceof HttpException) {
               $data = array(
                    'message' => '',
                    'code' => '404',
               );
               return response()->view('errors.404', $data, 404);
          }
        return parent::render($request, $e);
    }
}
