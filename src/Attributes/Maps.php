<?php

namespace Skraeda\AutoMapper\Attributes;

use Attribute;

#[Attribute]
class Maps
{
    /**
     * Constructor
     *
     * @param string $source
     * @param string $target
     */
    public function __construct(
        public string $source,
        public string $target
    ) {}
}
