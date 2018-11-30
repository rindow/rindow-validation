<?php
namespace Rindow\Validation\Constraints;
/**
 * @Annotation
 * @Target({ ANNOTATION_TYPE })
 */
class Constraint
{
    /**
     * array of ConstraintValidator classes implementing the constraint
     * public Class<? extends ConstraintValidator<?, ?>>[] validatedBy();
     */
    public $validatedBy;
}
