<?php namespace avansdp\abstracts;

abstract class class_loader
{
    protected string $directory;
    protected string $namespace;

    public function __construct(string $directory , string $namespace)
    {
        $this->directory = $directory;
        $this->namespace = $namespace;
    }

    public function loadClasses(): array
    {
        $instances = [];
        foreach ($this->getPhpFiles() as $file) {
            $className = $this->getClassNameFromFile($file);
            if ($this->shouldLoadClass($className)) {
                require_once $this->directory . '/' . $file;
                $instances[] = $this->instantiateClass($this->namespace . "\\" . $className);
            }
        }

        return $instances;
    }


    private function getPhpFiles(): array
    {
        return array_filter(scandir($this->directory), function ($file) {
            return pathinfo($file, PATHINFO_EXTENSION) === 'php';
        });
    }


    private function getClassNameFromFile(string $file): string
    {
        return pathinfo($file, PATHINFO_FILENAME);
    }

    protected function shouldLoadClass(string $className): bool
    {
        return true;
    }

    protected function instantiateClass(string $className): object
    {
        return new $className();
    }
}