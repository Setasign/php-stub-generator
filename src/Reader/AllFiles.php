<?php
declare(strict_types=1);

namespace setasign\PhpStubGenerator\Reader;

class AllFiles implements ReaderInterface
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var string[]
     */
    protected $excludes;

    public function __construct(string $path, array $excludes = [])
    {
        $_path = realpath($path);
        if (!is_string($_path) || !is_dir($_path)) {
            throw new \InvalidArgumentException(sprintf(
                'Path "%s" couldn\'t be found or isn\'t an directory!',
                $path
            ));
        }
        $path = $_path;

        foreach ($excludes as $k => $exclude) {
            $_exclude = realpath($exclude);
            if (!is_string($_exclude)) {
                throw new \InvalidArgumentException(sprintf(
                    'Exclude Path "%s" couldn\'t be found!',
                    $exclude
                ));
            }

            $excludes[$k] = $_exclude;
        }

        $this->path = $path;
        $this->excludes = $excludes;
    }

    /**
     * @param string $directory
     * @return string[]
     */
    protected function getSubFiles(string $directory): array
    {
        $result = [];

        foreach (glob($directory . '/*', GLOB_ONLYDIR) as $subDirectory) {
            if (in_array($subDirectory, $this->excludes, true)) {
                continue;
            }

            foreach ($this->getSubFiles($subDirectory) as $file) {
                $result[] = $file;
            }
        }

        foreach (glob($directory . '/*.php') as $file) {
            if (in_array($file, $this->excludes, true)) {
                continue;
            }

            $result[] = $file;
        }

        return $result;
    }

    /**
     * @return string[]
     */
    public function getFiles(): array
    {
        return $this->getSubFiles($this->path);
    }
}
