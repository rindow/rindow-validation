<?php
namespace Rindow\Validation\Constraints;

use Rindow\Validation\Core\AbstractConstraint;
/**
 * The annotated element must be a number whose value must be lower or
 * equal to the specified maximum.
 *
 * @Annotation
 * @Target({ METHOD, FIELD, ANNOTATION_TYPE ,PARAMETER})
 * @Constraint(validatedBy = {})
 */
class Max extends AbstractConstraint
{
    public $message = "must be less than or equal to {value}.";
    public $groups = array();
    public $payload = array();

    /**
     * integer value the element must be lower or equal to
     */
    public $value;
}
