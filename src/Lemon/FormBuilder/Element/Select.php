<?php
namespace Lemon\FormBuilder\Element;

use Lemon\FormBuilder\AbstractElement;

class Select extends AbstractElement {

	public function setValue($value) {
		foreach ($this->dom->childNodes as $child) {
			if ($child->getAttribute(self::ATTR_VALUE) == $value) {
				$child->setAttribute(self::ATTR_SELECTED, self::ATTR_SELECTED);
			}
		}
	}
}
