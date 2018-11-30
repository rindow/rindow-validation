<?php
namespace Rindow\Validation\Core;

use Rindow\Validation\Constraint;
use Rindow\Stdlib\Entity\AbstractPropertyAccess;

abstract class AbstractConstraint extends AbstractPropertyAccess implements Constraint
{
    public $validatedBy;
    public $path;

    public function initialize(Constraint $constraint)
    {
    }
}