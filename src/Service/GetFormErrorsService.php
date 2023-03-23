<?php

namespace App\Service;

use Symfony\Component\Form\FormInterface;

class GetFormErrorsService
{
    private array $errorMessages = [];

    public function getErrorsFromForm(FormInterface $form): array
    {
        $iterator = $form->getErrors(true);
        if (0 !== $iterator->count()) {
            foreach ($iterator as $error) {
                $this->errorMessages[] = $error->getMessage();
            }
        }
        return $this->errorMessages;
    }
}