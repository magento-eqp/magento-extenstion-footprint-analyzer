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
     * @param string $extensionDir
     * @return \stdClass
     */
    public function execute(string $extensionDir): \stdClass
    {
        $result = new \stdClass();
        $finder = new Finder();

        try {
            $finder->in($extensionDir . "/etc")->depth(0)->files()->name("webapi.xml");
            if ($finder->count() === 0) {
                $result->webApiEndpoints = 0;
                return $result;
            }
            $contents = iterator_to_array($finder, false)[0]->getContents();

            $crawler = new Crawler($contents);
            $result->webApiEndpoints = $crawler->filterXPath("//routes/route")->count();

            return $result;
        } catch (\Exception $exception) {
            $result->webApiEndpoints = 0;
            return $result;
        }
    }
}
