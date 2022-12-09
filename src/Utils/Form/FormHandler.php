<?php

namespace App\Utils\Form;

use App\Utils\Form\Exception\FormException;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;

class FormHandler
{
    /**
     * @throws FormException
     */
    public function handleForm(FormInterface $form, array $content): mixed
    {
        $form->submit($content);
        if (!$form->isValid() || !$form->isSubmitted()) {
            $this->extractErrors($form);
        }

        return $form->getData();
    }

    /**
     * @throws FormException
     *
     * @suppress PhanPossiblyUndeclaredMethod
     */
    private function extractErrors(FormInterface $form): void
    {
        $errors = [];

        /** @var FormError $error */
        foreach ($form->getErrors(true) as $error) {
            if (null === $error->getOrigin()) {
                continue;
            }

            $name = $error->getOrigin()->getName();

            $name = $name !== $form->getName() ? $name : '_form';

            if (!array_key_exists($name, $errors)) {
                $errors[$name] = [];
            }

            $errors[$name][] = $error->getMessage();
        }

        foreach ($errors as &$fieldsErrors) {
            $fieldsErrors = array_unique($fieldsErrors);
        }
        unset($fieldsErrors);

        throw new FormException($errors);
    }
}
