<?php

declare(strict_types=1);

namespace App\Dominio\ObjetoValor;

use Exception;

final class Email
{
    public function __construct(
        private string $email
    )
    {
        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            throw new Exception('E-mail is not valid');
        }

        $this->email = strtolower($this->email);
    }

    function get(): string{
        return $this->email;
    }
}