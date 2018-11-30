<?php
namespace Rindow\Validation\Constraints;

use Rindow\Validation\Core\AbstractConstraint;

/**
 * The annotated element must be false.
 *
 * @Annotation
 * @Target({ METHOD, FIELD })
 */
class CList extends AbstractConstraint
{
    public $message = "list of constraint.";
    public $groups = array();
    public $payload = array();

    /**
    * value list of annotation.
    */
    public $value;
}