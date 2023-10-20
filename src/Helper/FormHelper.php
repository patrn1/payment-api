<?php

namespace App\Helper;

use Symfony\Component\Form\Form;

/**
 * Helper class for the Form objects.
 *
 **/
class FormHelper
{
    /**
     * Gets all errors from a form.
     *
     * @return array array of error strings
     **/
    public static function getErrors(Form $form): array
    {
        $errors = [];
        if ($form->count()) {
            foreach ($form->all() as $child) {
                if (!$child->isValid()) {
                    $errors[$child->getName()] = (string) $form[$child->getName()]->getErrors();
                }
            }
        }

        return $errors;
    }
}
