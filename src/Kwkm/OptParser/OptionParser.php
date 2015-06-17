<?php
/**
 * Option Parser - Option Parser
 *
 * @package OptParser
 * @author Takehiro Kawakami <take@kwkm.org>
 * @license http://opensource.org/licenses/mit-license.php
 */
namespace Kwkm\OptParser;


class OptionParser
{
    const DUPLICATE_OVERWRITE = 0;
    const DUPLICATE_ARRAY = 1;

    private $argument;
    private $argumentCounter;
    private $option;
    private $parseMode;

    /**
     * argument parse
     *
     * @param $argv array   Command line arguments.
     */
    private function parse($argv)
    {
        $option = new \ArrayIterator($argv);

        while ($option->valid()) {
            switch ($this->detectOptionType($option->current())) {
                case OptionType::VALUE:
                    $this->setArgument($option->current());
                    $option->next();
                    break;
                case OptionType::SHORT_OPTION:
                case OptionType::LONG_OPTION:
                    $key = $option->current();
                    $option->next();
                    if ($this->detectOptionType($option->current()) === OptionType::VALUE) {
                        $this->setOption($key, $option->current());
                        $option->next();
                    } else {
                        $this->setOption($key, true);
                    }
                    break;
                case OptionType::SHORT_OPTION_WITH_VALUE:
                case OptionType::LONG_OPTION_WITH_VALUE:
                    list($key, $value) = explode('=', $option->current(), 2);
                    $this->setOption($key, $value);
                $option->next();
                    break;
                case OptionType::MULTIPLE_OPTION:
                    $this->splitMultiOption($option->current());
                    $option->next();
                    break;
            }
        }
    }

    public function getArgument()
    {
        return $this->argument;
    }

    public function getOption()
    {
        return $this->option;
    }

    private function splitMultiOption($value)
    {
        $splitArgument = preg_split("//", ltrim($value, '-'), null, PREG_SPLIT_NO_EMPTY);
        $option = new \ArrayIterator($splitArgument);

        while ($option->valid()) {
            $k = $option->current();
            $this->setOption("-{$k}", true);
            $option->next();
        }
    }

    private function setArgument($value)
    {
        $this->argument[$this->argumentCounter] = $value;
        $this->argumentCounter++;
    }

    private function setOption($key, $value)
    {
        if (($this->parseMode === OptionParser::DUPLICATE_OVERWRITE) or (!isset($this->option[$key]))) {
            $this->option[$key] = $value;

            return;
        }

        if (!is_array($this->option[$key])) {
            $this->option[$key] = array(
                $this->option[$key],
                $value,
            );

            return;
        }

        $this->option[$key][] = $value;
    }

    private function detectOptionType($k)
    {
        if (strpos($k, '--') === 0) {
            if (strpos($k, '=') === false) {
                return OptionType::LONG_OPTION;
            } else {
                return OptionType::LONG_OPTION_WITH_VALUE;
            }
        } elseif (strpos($k, '-') === 0) {
            if (strpos($k, '=') !== false) {
                return OptionType::SHORT_OPTION_WITH_VALUE;
            } else {
                if (strlen($k) === 2) {
                    return OptionType::SHORT_OPTION;
                } else {
                    return OptionType::MULTIPLE_OPTION;
                }
            }
        } else {
            return OptionType::VALUE;
        }
    }

    public function __construct($argv = array(), $mode = OptionParser::DUPLICATE_OVERWRITE)
    {
        $this->argument = array();
        $this->argumentCounter = 0;
        $this->parseMode = $mode;
        $this->parse($argv);
    }
}
