<?php
namespace Rindow\Validation\Constraints;

use Rindow\Validation\Core\AbstractConstraint;

/**
 * The annotated element must be false.
 *
 * @Annotation
 * @Target({ METHOD, FIELD, ANNOTATION_TYPE , PARAMETER})
 * @Constraint(validatedBy = {})
 */
class AssertFalse extends AbstractConstraint
{
    public $message = "must be false.";
    public $groups = array();
    public $payload = array();
}
