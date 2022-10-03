<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Création d'une contrainte custom
 */
class NotOnlyFieldEmptyValidator extends ConstraintValidator
{
    public $ERROR = false;
    public $ERROS = [];
    public $TIME = '';
    public function __construct(public TranslatorInterface $translatorInterface)
    {
        
    }
    public function validate($value, Constraint $constraint)
    {

        foreach ($value as  $val) {      
            switch ($val) {
            
                case $val->getOpen() !== null && $val->getClose() === null:
                    $this->ERROR = true;
                    $this->ERRORS[] = $val->getDay();
                    break;

                case $val->getOpen() === null && $val->getClose() !== null:
                        $this->ERROR = true;
                        $this->ERRORS[] = $val->getDay();
                        break;

                        case $val->getOpen() >= $val->getClose() and $val->getClose() and $val->getOpen() !== null && $val->getClose() !== null:
                            $this->ERROR = true;
                            $this->ERRORS[] = $val->getDay();
                            $this->TIME =  $this->translatorInterface->trans('L\'heure de début doit être inférieur à l`\'heure de fin');
                            break;

                case $val->getOpenpm() === null && $val->getClosepm() !== null:
                            $this->ERROR = true;
                            $this->ERRORS[] = $val->getDay();
                            break;
                case $val->getOpenpm() !== null && $val->getClosepm() === null:
                                $this->ERROR = true;
                                $this->ERRORS[] = $val->getDay();
                                break;

                case $val->getOpenpm() <= $val->getClose() and $val->getOpen() !== null && $val->getClose() !== null and $val->getOpenpm() !== null && $val->getClosepm() !== null:
                        $this->ERROR = true;
                        $this->ERRORS[] = $val->getDay();
                        $this->TIME = $this->translatorInterface->trans('L\'heure de fermeture ne peut pas être inférieur à l\'heure d`\'ouverture');
                        break;
            }
            // if($val->getOpen() !== null && $val->getClose() === null || $val->getOpen() === null && $val->getClose() !== null ){
            //     $this->ERROR = true;
            //     $this->ERRORS[] = $val->getDay();
            // }
        }
        if($this->ERROR){
            if(!empty($this->TIME)){
                $this->context->buildViolation($this->TIME)
                ->setParameters($this->ERRORS)
                ->addViolation();
            }else{
                $this->TIME = $this->translatorInterface->trans('Un des champs n\'est pas complètés');
                $this->context->buildViolation($this->TIME)
                ->setParameters($this->ERRORS)
                ->addViolation();
            }
        }
    }
}
