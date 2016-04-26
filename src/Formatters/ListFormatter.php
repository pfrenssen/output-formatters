<?php
namespace Consolidation\OutputFormatters\Formatters;

use Symfony\Component\Console\Output\OutputInterface;
use Consolidation\OutputFormatters\FormatterInterface;
use Consolidation\OutputFormatters\OverrideRestructureInterface;
use Consolidation\OutputFormatters\StructuredData\ListDataInterface;
use Consolidation\OutputFormatters\StructuredData\RenderCellInterface;

/**
 * Display the data in a simple list.
 *
 * This formatter prints a plain, unadorned list of data,
 * with each data item appearing on a separate line.  If you
 * wish your list to contain headers, then use the table
 * formatter, and wrap your data in an AssociativeList.
 */
class ListFormatter implements FormatterInterface, OverrideRestructureInterface, RenderDataInterface
{
    /**
     * @inheritdoc
     */
    public function write(OutputInterface $output, $data, $options = [])
    {
        $output->writeln(implode("\n", $data));
    }

    /**
     * @inheritdoc
     */
    public function overrideRestructure($structuredOutput, $configurationData, $options)
    {
        // If the structured data implements ListDataInterface,
        // then we will render whatever data its 'getListData'
        // method provides.
        if ($structuredOutput instanceof ListDataInterface) {
            return $this->renderData($structuredOutput, $structuredOutput->getListData(), $configurationData, $options);
        }
    }

    /**
     * @inheritdoc
     */
    public function renderData($originalData, $restructuredData, $configurationData, $options)
    {
        if ($originalData instanceof RenderCellInterface) {
            return $this->renderEachCell($originalData, $restructuredData, $configurationData, $options);
        }
        return $restructuredData;
    }

    protected function renderEachCell($originalData, $restructuredData, $configurationData, $options)
    {
        foreach ($restructuredData as $key => $cellData) {
            $restructuredData[$key] = $originalData->renderCell($key, $cellData, $configurationData, $options);
        }
        return $restructuredData;
    }
}
