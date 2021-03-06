<?php
namespace App\Command;

use App\Analyzer\AnalyzeWebapi;
use App\Analyzer\AnalyzeProgrammaticApi;
use App\Analyzer\AnalyzeGraphQl;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class AnalyzeExtensionFootprintCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:analyze-extension-footprint';

    /**
     * @var AnalyzeProgrammaticApi
     */
    private $analyzeProgrammaticApi;

    /**
     * @var AnalyzeWebapi
     */
    private $analyzeWebapi;

    /**
     * @var AnalyzeGraphQl
     */
    private $analyzeGraphQl;

    /**
     * @param AnalyzeProgrammaticApi $analyzeProgrammaticApi
     * @param AnalyzeWebapi $analyzeWebapi
     * @param AnalyzeGraphQl $analyzeGraphQl
     */
    public function __construct(
        AnalyzeProgrammaticApi $analyzeProgrammaticApi,
        AnalyzeWebapi $analyzeWebapi,
        AnalyzeGraphQl $analyzeGraphQl
    ) {
        $this->analyzeProgrammaticApi = $analyzeProgrammaticApi;
        $this->analyzeWebapi = $analyzeWebapi;
        $this->analyzeGraphQl = $analyzeGraphQl;
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setDescription("Analyze Magento extension footprint");
        $this->addArgument("extension-directory", InputArgument::REQUIRED, "Path to extension files to be analyzed");
        $this->addOption("report-file", null, InputOption::VALUE_REQUIRED);
        $this->addOption("json");
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $extensionPath = $input->getArgument("extension-directory");
        $result = [];

        try {
            $result = array_merge($result, (array) $this->analyzeProgrammaticApi->execute($extensionPath));
            $result = array_merge($result, (array) $this->analyzeWebapi->execute($extensionPath));
            $result = array_merge($result, (array) $this->analyzeGraphQl->execute($extensionPath));

            if ($input->getOption("json")) {
                $output->writeln(json_encode($result, JSON_PRETTY_PRINT));
                if ($input->getOption("report-file")) {
                    file_put_contents(
                            $input->getOption("report-file"),
                            json_encode($result)
                    );
                }
            } else {
                foreach ($result as $key => $value) {
                    $output->writeln(sprintf(" - %s: %s", $key, $value));
                }
                $output->writeln("");
            }

            return 0;
        } catch (\Throwable $error) {
            $output->writeln($error->getMessage());
            $output->writeln("Unable to analyze extension");

            return 1;
        }
    }
}
