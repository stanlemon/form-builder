<?php
namespace Lemon\FormBuilder\Element;

use Lemon\FormBuilder\Element;
use Lemon\FormBuilder\AbstractElement;

class Input extends AbstractElement implements Element {

	const INPUT_CHECKBOX = 'checkbox';
	const INPUT_RADIO = 'radio';

	public function setValue($value) {
		if ($this->dom->getAttribute(self::ATTR_TYPE) == self::INPUT_CHECKBOX || 
			$this->dom->getAttribute(self::ATTR_TYPE) == self::INPUT_RADIO) {
			
			if ($this->dom->getAttribute(self::ATTR_VALUE) == $value) {
				$this->dom->setAttribute(self::ATTR_CHECKED, self::ATTR_CHECKED);
			} else {
				$this->dom->removeAttribute(self::ATTR_CHECKED);
			}
		} else {
			return parent::setValue($value);
		}
	}
}
