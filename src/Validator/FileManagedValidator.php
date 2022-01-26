<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class FileManagedValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\FileManaged */

        if (null === $value || '' === $value) {
            return;
        }

        if ($value instanceof \App\Entity\FileManaged){
            $mimeType = $value->getMimeType();

            foreach ($constraint->mimeTypes as $allowMimeType){
                if ($allowMimeType === $mimeType){
                    return;
                }

                if ($discrete = strstr($allowMimeType, '/*', true)){
                    if (strstr($mimeType,'/',true) === $discrete){
                        return;
                    }
                }
            }
        }

        // TODO: implement the validation here
        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ name }}', $value->getOriginName())
            ->addViolation();
    }
}
