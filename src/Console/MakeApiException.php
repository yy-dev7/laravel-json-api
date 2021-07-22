<?php


namespace GzhPackages\JsonApi\Console;

use Illuminate\Console\GeneratorCommand;

class MakeApiException extends GeneratorCommand
{
    protected $signature = 'make:api-exception {name}';

    protected $description = 'Create a new custom api exception class';

    protected function getStub(): string
    {
        return __DIR__.'/../stubs/Exception.stub';
    }

     public function getDefaultNamespace($rootNamespace): string
     {
        return $rootNamespace.'\Exceptions';
    }
}