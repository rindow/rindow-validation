<?php
namespace Rindow\Validation\Constraints;

use Rindow\Validation\Core\AbstractConstraint;

/**
 * @Annotation
 * @Target({ METHOD, FIELD, ANNOTATION_TYPE , PARAMETER})
 * @Constraint(validatedBy = {})
 */
class Email extends AbstractConstraint
{
    public $message = "not a well-formed email address.";
    public $groups  = array();
    public $payload = array();

    /**
     * value the element must be email address format
     */
    public $value;
}
