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
     * @param string $sourceClass
     * @param string $targetClass
     * @return void
     */
    public function registerCustomMapper(string $mapperClass, string $sourceClass, string $targetClass): void;

    /**
     * Scan a directory for Custom Mappers
     *
     * @param string|array $dirs
     * @return array
     */
    public function scanMappingDirectory(string|array $dir): array;
}
