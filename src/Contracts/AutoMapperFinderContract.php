<?php

namespace Skraeda\AutoMapper\Contracts;

/**
 * AutoMapperFinder Contract to define utility operations to register custom mappers.
 *
 * @author Gunnar Örn Baldursson <gunnar@sjukraskra.is>
 */
interface AutoMapperFinderContract
{
    /**
     * Scan a directory for Custom Mappers
     *
     * @param string|array $dirs
     * @return array
     */
    public function scanMappingDirectory(string|array $dir): array;
}
