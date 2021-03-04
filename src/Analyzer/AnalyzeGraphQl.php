<?php
namespace App\Analyzer;

use Symfony\Component\Finder\Finder;

class AnalyzeGraphQl
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
     * @param string $extensionName
     * @return \stdClass
     */
    public function execute(string $extensionName): \stdClass
    {
        $result = new \stdClass();
        $extensionDir = $this->projectDir . "/../" . $extensionName;

        $finder = new Finder();
        $finder->in($extensionDir . "/etc")->depth(0)->files()->name("schema.graphqls");
        if ($finder->count() === 0) {
            $result->graphql = 0;
            return $result;
        }
        $contents = iterator_to_array($finder, false)[0]->getContents();

        $result->graphql = 1;
        $result->graphqlTypes = preg_match_all("/type \w+ {/", $contents);
        $result->graphqlInputs = preg_match_all("/input \w+ {/", $contents);
        return $result;
    }
}
