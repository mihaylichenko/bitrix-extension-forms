<?php

namespace Msvdev\Bitrix\Forms\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\MissingOptionsException;


class WordsConstraint extends Constraint
{

    public $message = 'Поле имеет ограничение в {{ max }} слов.';

    public $max;

    public $min;

    public function __construct($options = null)
    {
        if (null !== $options && !\is_array($options)) {
            $options = [
                'min' => $options,
                'max' => $options,
            ];
        } elseif (\is_array($options) && isset($options['value']) && !isset($options['min']) && !isset($options['max'])) {
            $options['min'] = $options['max'] = $options['value'];
            unset($options['value']);
        }

        parent::__construct($options);

        if (null === $this->max) {
            throw new MissingOptionsException(sprintf('Either option "min" or "max" must be given for constraint "%s".', __CLASS__), ['min', 'max']);
        }
    }

}