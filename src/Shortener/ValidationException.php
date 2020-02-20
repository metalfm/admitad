<?php declare(strict_types=1);

namespace App\Shortener;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends \Exception
{
    private ConstraintViolationListInterface $violations;

    public function __construct(string $message, ConstraintViolationListInterface $violations)
    {
        parent::__construct($message);
        $this->violations = $violations;
    }

    public function __toString()
    {
        return (string)$this->violations;
    }
}
