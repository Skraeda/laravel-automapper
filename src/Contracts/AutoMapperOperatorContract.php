<?php

namespace Skraeda\AutoMapper\Contracts;

/**
 * AutoMapperOperator Contract to define utility operations to register custom mappers.
 *
 * @author Gunnar Örn Baldursson <gunnar@sjukraskra.is>
 */
interface AutoMapperOperatorContract
{
    /**
     * Register a Custom Mapper
     *
     * @param string $mapperClass
     * @param string $sourceCkass
     * @param string $targetClass
     * @return self
     */
    public function registerCustomMapper(string $mapperClass, string $sourceCkass, string $targetClass): self;

    /**
     * Scan a directory for Custom Mappers
     *
     * @param string $dir
     * @return self
     */
    public function scanMappingDirectory(string $dir): array;
}
