<?php
class Class_Form_Decorator_Composite extends Zend_Form_Decorator_Abstract
{
    public function buildLabel()
    {
        $element = $this->getElement();
        $label = $element->getLabel();
        if(empty($label)) {
            return '<dt>&nbsp;</dt>';
        }
        if ($translator = $element->getTranslator()) {
            $label = $translator->translate($label);
        }
        if ($element->isRequired()) {
            $label = '* '.$label;
        }
        $label .= ':';
        return '<dt>'.$element->getView()
                       ->formLabel($element->getName(), $label).'</dt>';
    }

    public function buildInput()
    {
        $element = $this->getElement();
        $helper  = $element->helper;
        return '<dd class="input">'.$element->getView()->$helper(
            $element->getName(),
            $element->getValue(),
            $element->getAttribs(),
            $element->options
        ).'</dd>';
    }

    public function buildErrors()
    {
        $element  = $this->getElement();
        $messages = $element->getMessages();
        if (empty($messages)) {
            return '';
        }
        return '<dd class="errors">' .
               $element->getView()->formErrors($messages) . '</dd>';
    }

    public function buildDescription()
    {
        $element = $this->getElement();
        $desc    = $element->getDescription();
        if (empty($desc)) {
            return '';
        }
        return '<dd class="description">' . $desc . '</dd>';
    }

    public function render($content)
    {
        $element = $this->getElement();
        if (!$element instanceof Zend_Form_Element) {
            return $content;
        }
        if (null === $element->getView()) {
            return $content;
        }

        $separator = $this->getSeparator();
        $placement = $this->getPlacement();
        $label     = $this->buildLabel();
        $input     = $this->buildInput();
        $desc      = $this->buildDescription();
        $errors    = $this->buildErrors();

        $output = '<dl>'.$label
                . $input
                . $desc
                . $errors.'</dl>';

        switch ($placement) {
            case (self::PREPEND):
                return $output . $separator . $content;
            case (self::APPEND):
            default:
                return $content . $separator . $output;
        }
        return '';
    }
}