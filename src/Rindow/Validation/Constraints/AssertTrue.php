<?php
namespace Rindow\Validation\Constraints;

use Rindow\Validation\Core\AbstractConstraint;
/**
 * The annotated element must be true.
 *
 * @Annotation
 * @Target({ METHOD, FIELD, ANNOTATION_TYPE , PARAMETER})
 * @Constraint(validatedBy = {})
 */
class AssertTrue extends AbstractConstraint
{
    public $message = "must be true.";
    public $groups = array();
    public $payload = array();
}
