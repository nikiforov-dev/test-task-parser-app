<?php

namespace App\DependencyInjection\InjectionTrait;

use App\Utils\Form\FormHandler;

trait FormHandlerInjectionTrait
{
    protected FormHandler $formHandler;

    /**
     * @required
     *
     * @return $this
     */
    public function setFormHandler(FormHandler $formHandler): self
    {
        $this->formHandler = $formHandler;

        return $this;
    }
}
