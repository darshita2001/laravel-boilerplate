<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

trait ValidationErrorsTrait
{
    /**
     * This function is used to overwrite the failedValidation function and make sure it won't redirect and properly
     * handle the json error response here.
     *
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->response(collect($validator->errors())->toArray()));
    }

    /**
     * @param array $errors
     * @return JsonResponse
     */
    private function response(array $errors)
    {
        return response()->json([
            'success' => false,
            'errors' => $errors,
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
    }

     /**
     * @param array $actualKeys
     * @param array $expectedKeys
     */
    protected function checkUnknownKey(array $actualKeys, array $expectedKeys)
    {

        $unknownKeys = array_diff($actualKeys, $expectedKeys);

        if (!empty($unknownKeys)) {
            $validator = \Illuminate\Support\Facades\Validator::make([], []); // Create an empty validator instance

            // Add a validation error message
            $validator->errors()->add('unknown_keys', 'Unknown keys are passed: ' . implode(', ', $unknownKeys));

            // Throw a ValidationException with the errors
            throw new ValidationException($validator, response()->json(['errors' => $validator->errors()], 422));
        }
    }
}
