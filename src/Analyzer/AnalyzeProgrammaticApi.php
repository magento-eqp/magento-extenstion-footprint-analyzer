<?php
namespace App\Analyzer;

use Symfony\Component\Finder\Finder;

class AnalyzeProgrammaticApi
{
    /**
     * @var string
     */
    private $projectDir;

    /**
     * @param string $projectDir
     */
    public function __construct(string $projectDir) {
        $this->projectDir = $projectDir;
    }

    /**
     * @param string $extensionDir
     * @return \stdClass
     */
    public function execute(string $extensionDir): \stdClass
    {
        $result = new \stdClass();

        $finder = new Finder();
        try {
            $finder->in($extensionDir . "/Api")->depth(0)->files()->name("*Interface.php");
            $result->programmaticInterfacesCount = $finder->count();
        } catch (\Exception $exception) {
            $result->programmaticInterfacesCount = 0;
        } finally {
            unset($finder);
        }


        $finder = new Finder();
        try {
            $finder->in($extensionDir . "/Api/Data")->depth(0)->files()->name("*Interface.php");
            $result->programmaticDataInterfacesCount = $finder->count();
        } catch (\Exception $exception) {
            $result->programmaticDataInterfacesCount = 0;
        } finally {
            unset($finder);
        }

        return $result;
    }
}
