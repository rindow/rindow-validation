<?php
namespace Rindow\Validation\Constraints;

use Rindow\Validation\Core\AbstractConstraint;
/**
 * The annotated element must be a date in the past.
 *
 * @Annotation
 * @Target({ METHOD, FIELD, ANNOTATION_TYPE, PARAMETER })
 * @Constraint(validatedBy = {})
 */
class Past extends AbstractConstraint
{
	public $message = "must be in the past.";
	public $groups = array();
	public $payload = array();
}
