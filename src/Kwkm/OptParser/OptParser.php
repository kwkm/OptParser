<?php
/**
 * Created by PhpStorm.
 * User: kwkm
 * Date: 15/06/20
 * Time: 14:58
 */

namespace Kwkm\OptParser;


class OptParser
{
    private $parser;
    private $parsedOption;
    private $requiredOption;

    private $helpData;
    private $helpGroup;
    private $helpNameLength;

    public function addOptionGroup($groupName, $groupTitle, $groupGuide = null)
    {
        $this->helpGroup[$groupName] = array(
            'title' => $groupTitle,
            'guide' => $groupGuide,
        );

        return $this;
    }

    public function addOption($optionName, $options = array())
    {
        $options = $this->setOptionDefaultValue($options);

        $this->setOptionValue($optionName, $options);
        $this->setHelpData($optionName, $options);

        return $this;
    }

    public function isOption($key)
    {
        return isset($this->parsedOption[$key]);
    }

    public function getOption($key)
    {
        return $this->parsedOption[$key];
    }

    public function getArgument()
    {
        return $this->parser->getArgument();
    }

    public function checkRequired()
    {
        foreach ($this->requiredOption as $optionName) {
            if (is_null($this->parsedOption[$optionName])) {
                return false;
            }
        }

        return true;
    }

    public function help()
    {
        if ($this->helpNameLength >= 28) {
            $this->helpNameLength = 28;
        }

        foreach ($this->helpGroup as $key => $value) {
            echo $value['title'] . PHP_EOL;
            if (strlen($value['guide']) !== 0) {
                echo $value['guide'] . PHP_EOL;
            }
            $this->groupHelp($key);
        }
    }

    private function groupHelp($groupName)
    {
        foreach ($this->helpData[$groupName] as $key => $value) {
            if (strlen($value['name']) > $this->helpNameLength) {
                echo $value['name'] . PHP_EOL;
                echo sprintf("%s  %s", str_repeat(' ', $this->helpNameLength), $value['guide']) . PHP_EOL;
            } else {
                echo sprintf("%s  %s", str_pad($value['name'], $this->helpNameLength), $value['guide']) . PHP_EOL;
            }
        }
    }

    private function setOptionValue($optionName, $options)
    {
        if ((isset($options['alias'])) and ($this->parser->isOption($options['alias']))) {
            $this->parsedOption[$optionName] = $this->parser->getOption($options['alias'], $options['default']);
        }

        if ($this->parser->isOption($optionName)) {
            $this->parsedOption[$optionName] = $this->parser->getOption($optionName, $options['default']);
        }

        if (!isset($this->parsedOption[$optionName])) {
            $this->parsedOption[$optionName] = $options['default'];
        }

        if ($options['required'] === true) {
            $this->requiredOption[] = $optionName;
        }
    }

    private function setHelpData($optionName, $options)
    {
        $name = $this->getOptionName($optionName, $options);
        $this->helpData[$options['group']][] = array(
            'name' => $name,
            'guide' => $options['help'],
        );

        if (strlen($name) > $this->helpNameLength) {
            $this->helpNameLength = strlen($name);
        }
    }

    private function getOptionName($optionName, $options)
    {
        if (strlen($options['var']) !== 0) {
            if (isset($options['alias'])) {
                return sprintf('  %s, %s=%s', $optionName, $options['alias'], $options['var']);
            } else {
                return sprintf('  %s %s', $optionName, $options['var']);
            }
        } else {
            if (isset($options['alias'])) {
                return sprintf('  %s, %s', $optionName, $options['alias']);
            } else {
                return sprintf('  %s', $optionName);
            }
        }
    }

    private function setOptionDefaultValue($options)
    {
        $default = array(
            'group' => 'default',
            'required' => false,
            'help' => '',
            'default' => null,
            'var' => '',
        );

        return array_merge($default, $options);
    }

    public function __construct($argv = array())
    {
        $this->parser = new OptionParser($argv, OptionParser::DUPLICATE_OVERWRITE);
        $this->parsedOption = array();
        $this->requiredOption = array();
        $this->helpData = array();
        $this->helpGroup = array();
        $this->helpNameLength = 0;
        $this->addOptionGroup('default', 'Options:');
    }
}