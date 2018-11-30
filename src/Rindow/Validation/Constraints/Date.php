<?php
namespace Rindow\Validation\Constraints;

use Rindow\Validation\Core\AbstractConstraint;
/**
 * @Annotation
 * @Target({ METHOD, FIELD, ANNOTATION_TYPE ,PARAMETER})
 * @Constraint(validatedBy = {})
 */
class Date extends AbstractConstraint
{
    public $message = "must be a valid date.";
    public $groups = array();
    public $payload = array();

    /**
     * value the element must be lower or equal to
     */
    public $format;
}

