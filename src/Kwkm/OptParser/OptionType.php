<?php
/**
 * Option Parser - Option Type
 *
 * @package OptParser
 * @author Takehiro Kawakami <take@kwkm.org>
 * @license http://opensource.org/licenses/mit-license.php
 */
namespace Kwkm\OptParser;

class OptionType
{
    /**
     * 値のみ
     */
    const VALUE = 0;

    /**
     * 長いオプション
     */
    const LONG_OPTION = 1;

    /**
     * 長いオプション - 値付き
     */
    const LONG_OPTION_WITH_VALUE = 2;

    /**
     * 短いオプション
     */
    const SHORT_OPTION = 3;

    /**
     * 短いオプション - 値付き
     */
    const SHORT_OPTION_WITH_VALUE = 4;

    /**
     * 複合オプション
     */
    const MULTIPLE_OPTION = 5;
}
