<?php

namespace achedon\loto\librairies\forms;

use achedon\loto\librairies\forms\elements\Dropdown;
use achedon\loto\librairies\forms\elements\Element;
use achedon\loto\librairies\forms\elements\Input;
use achedon\loto\librairies\forms\elements\Label;
use achedon\loto\librairies\forms\elements\Slider;
use achedon\loto\librairies\forms\elements\StepSlider;
use achedon\loto\librairies\forms\elements\Toggle;
use pocketmine\form\FormValidationException;

class CustomFormResponse
{
    /** @var Element[] */
    private array $elements;

    /**
     * @param Element[] $elements
     */
    public function __construct(array $elements)
    {
        $this->elements = $elements;
    }

    public function getDropdown(): Dropdown
    {
        return $this->tryGet(Dropdown::class);
    }

    /**
     * @param string $expected
     *
     * @return Element|mixed
     * @internal
     *
     */
    public function tryGet(string $expected = Element::class): Element
    {
        if (($element = array_shift($this->elements)) instanceof Label) {
            return $this->tryGet($expected); //remove useless element
        } elseif ($element === null || !($element instanceof $expected)) {
            throw new FormValidationException("Expected a element with of type $expected, got " . get_class($element));
        }
        return $element;
    }

    public function getInput(): Input
    {
        return $this->tryGet(Input::class);
    }

    public function getSlider(): Slider
    {
        return $this->tryGet(Slider::class);
    }

    public function getStepSlider(): StepSlider
    {
        return $this->tryGet(StepSlider::class);
    }

    public function getToggle(): Toggle
    {
        return $this->tryGet(Toggle::class);
    }

    public function getElements(): array
    {
        return $this->elements;
    }

    public function getValues(): array
    {
        $values = [];
        foreach ($this->elements as $element) {
            if ($element instanceof Label) {
                continue;
            }
            $values[] = $element instanceof Dropdown ? $element->getSelectedOption() : $element->getValue();
        }
        return $values;
    }
}