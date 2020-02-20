<?php declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class FutureDateTimeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof FutureDateTime) {
            throw new UnexpectedTypeException($constraint, FutureDateTime::class);
        }

        if (!$value instanceof \DateTimeInterface) {
            throw new UnexpectedValueException($value, \DateTimeInterface::class);
        }

        if ($value < new \DateTimeImmutable()) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
