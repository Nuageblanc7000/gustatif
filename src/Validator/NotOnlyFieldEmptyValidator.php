<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NotOnlyFieldEmptyValidator extends ConstraintValidator
{
    public $ERROR = false;
    public $ERROS = [];
    public function validate($value, Constraint $constraint)
    {

        foreach ($value as  $val) {      
            if($val->getOpen() !== null && $val->getClose() === null || $val->getOpen() === null && $val->getClose() !== null ){
                $this->ERROR = true;
                $this->ERRORS[] = $val->getDay();
            }
        }
        if($this->ERROR){
           $this->context->buildViolation($constraint->message)
           ->setParameters($this->ERRORS)
           ->addViolation();
        }
    }
}
