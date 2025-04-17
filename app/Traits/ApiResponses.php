<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\Response;

trait ApiResponses
{
    protected function ok($message, $data = null)
    {
        return $this->success($message, Response::HTTP_OK, $data);
    }

    protected function created($message, $data = null)
    {
        return $this->success($message, Response::HTTP_CREATED, $data);
    }

    protected function success($message, $statusCode = Response::HTTP_OK, $data = null)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    protected function error($message, $statusCode, $errors = null)
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    protected function validationError($errors)
    {
        return $this->error('Validation errors', Response::HTTP_UNPROCESSABLE_ENTITY, $errors);
    }

    protected function unauthorized($message = 'Unauthorized')
    {
        return $this->error($message, Response::HTTP_UNAUTHORIZED);
    }

    protected function notFound($message, $data = null)
    {
        return $this->error($message, Response::HTTP_NOT_FOUND, $data);
    }

    protected function deleted($message = 'Resource deleted successfully')
    {
        return response()->json([
            'success' => true,
            'message' => $message
        ], Response::HTTP_NO_CONTENT);
    }
}
