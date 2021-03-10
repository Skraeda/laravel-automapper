<?php

namespace Skraeda\AutoMapper\Tests\Data;

use AutoMapperPlus\CustomMapper\CustomMapper;
use Skraeda\AutoMapper\Attributes\Maps;

/**
 * Maps from A to B
 *
 * @author Gunnar Örn Baldursson <gunnar@sjukraskra.is>
 */
#[Maps(A::class, B::class)]
class ABMapper extends CustomMapper
{
    /**
     * {@inheritDoc}
     */
    public function mapToObject($source, $destination, array $context = [])
    {
        $destination->Value = $source->Value + 1;
        return $destination;
    }
}
