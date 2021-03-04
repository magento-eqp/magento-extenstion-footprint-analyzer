<?php
namespace App\Analyzer;

use Symfony\Component\Finder\Finder;
use Symfony\Component\DomCrawler\Crawler;

class AnalyzeWebapi
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
        $finder->in($extensionDir . "/etc")->depth(0)->files()->name("webapi.xml");
        if ($finder->count() === 0) {
            $result->webApiEndpoints = 0;
            return $result;
        }
        $contents = iterator_to_array($finder, false)[0]->getContents();

        $crawler = new Crawler($contents);
        $result->webApiEndpoints = $crawler->filterXPath("//routes/route")->count();

        return $result;
    }
}
