<?php

namespace App\DependencyInjection\InjectionTrait;

use Symfony\Component\Form\FormFactoryInterface;

trait FormFactoryInjectionTrait
{
    protected FormFactoryInterface $formFactory;

    /**
     * @required
     *
     * @return $this
     */
    public function setFormFactory(FormFactoryInterface $formFactory): self
    {
        $this->formFactory = $formFactory;

        return $this;
    }
}
