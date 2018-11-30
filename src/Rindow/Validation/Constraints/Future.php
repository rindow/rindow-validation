<?php
namespace Rindow\Validation\Constraints;

use Rindow\Validation\Core\AbstractConstraint;
/**
 * The annotated element must be a date in the future.
 *
 * @Annotation
 * @Target({ METHOD, FIELD, ANNOTATION_TYPE, CONSTRUCTOR, PARAMETER })
 * @Constraint(validatedBy = {})
 */
class Future extends AbstractConstraint
{
    public $message = "must be in the future.";
    public $groups = array();
    public $payload = array();
}
