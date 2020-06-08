<?php

namespace Msvdev\Bitrix\Forms\Constraints;


use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class WordsConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof WordsConstraint) {
            throw new UnexpectedTypeException($constraint, WordsConstraint::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) take care of that
        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        $words = explode(" ", $value);

        if (count($words) > $constraint->max) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ max }}', $constraint->max)
                ->addViolation();
        }
    }


}