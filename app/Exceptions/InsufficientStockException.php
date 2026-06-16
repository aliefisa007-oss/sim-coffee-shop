<?php

namespace App\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
    public function __construct(string $message = 'Stok tidak mencukupi.')
    {
        parent::__construct($message);
    }
}