<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PermissionException extends Exception
{
    /**
     * The exception message.
     */
    protected $message = 'ليس لديك الصلاحية لتنفيذ هذا الإجراء.';

    /**
     * The exception code.
     */
    protected $code = 403;

    /**
     * Render the exception into an HTTP response.
     */
    public function render(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $this->getMessage(),
                'error' => 'تم رفض الصلاحية'
            ], $this->getCode());
        }

        return response()->view('404', [], $this->getCode());
    }
} 