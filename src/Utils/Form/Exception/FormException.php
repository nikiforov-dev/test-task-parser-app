<?php

namespace App\Utils\Form\Exception;

class FormException extends \Exception
{
    public function __construct(array $errors)
    {
        $errorMessage = '';

        foreach ($errors as $errorKey => $error) {
            if (is_array($error)) {
                $message = '';

                foreach ($error as $value) {
                    $message .= "{$errorKey} => {$value}\n";
                }

                $error = new FormFieldException($message);
            }

            $errorMessage .= sprintf(
                "Message: %s\nWhere: %s: %s\nCode: %s\n",
                $error->getMessage(),
                $error->getFile(),
                $error->getLine(),
                $error->getCode(),
            );
        }

        parent::__construct($errorMessage);
    }
}
