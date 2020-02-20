<?php declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class FutureDateTime extends Constraint
{
    public $message = 'The value should be in future';
}
