<?php
namespace Rindow\Validation\Constraints;

use Rindow\Validation\Core\AbstractConstraint;
/**
 * The annotated element must not be <code>null</code>.
 * Accepts any type.
 *
 * @Annotation
 * @Target({ METHOD, FIELD, ANNOTATION_TYPE })
 * @Constraint(validatedBy = {})
 */
class NotNull extends AbstractConstraint
{
    public $message = "may not be null.";
    public $groups = array();
    public $payload = array();
}
