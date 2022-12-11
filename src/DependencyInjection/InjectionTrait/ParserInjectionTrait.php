<?php

namespace App\DependencyInjection\InjectionTrait;

use App\Parser\Parser;

trait ParserInjectionTrait
{
    protected Parser $parser;

    /**
     * @required
     *
     * @return $this
     */
    public function setParser(Parser $parser): self
    {
        $this->parser = $parser;

        return $this;
    }
}
