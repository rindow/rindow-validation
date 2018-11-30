<?php
namespace Rindow\Validation\Constraints;

use Rindow\Validation\Core\AbstractConstraint;
/**
 * Define a group sequence
 *
 * @Annotation
 * @Target({ TYPE })
 * @Retention(RUNTIME)
 */
class GroupSequence extends AbstractConstraint
{
	/**
	 * Class<?>[] value();
     */
	public $value;

	public function __construct(array $groups=null)
	{
		if(isset($groups['value']))
			$this->value = $groups['value'];
		else
			$this->value = $groups;
	}
}