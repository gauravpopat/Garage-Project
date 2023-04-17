<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\Auth;
use App\Models\Error;
use Throwable;



class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,

    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (ModelNotFoundException $e, $exception) {
            return error('Failed', 'Model Not Found');
        });

        $this->renderable(function (NotFoundHttpException $e, $exception) {
            return error('Failed', 'Data Not Found');
        });

        $this->reportable(function (Throwable $exception) {
            // only create entries if app environment is not local
            if (!app()->environment('local')) {
                $user_id = 0;


                if (Auth::user()) {
                    $user_id = Auth::user()->id;
                }

                $data = array(
                    'user_id'   => $user_id,
                    'code'      => $exception->getCode(),
                    'file'      => $exception->getFile(),
                    'line'      => $exception->getLine(),
                    'message'   => $exception->getMessage(),
                    'trace'     => $exception->getTraceAsString(),
                );

                Error::create($data);
            }
        });


        // $this->reportable(function (Throwable $e){});
    }
}
