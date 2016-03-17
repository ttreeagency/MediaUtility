<?php
namespace Ttree\MediaUtility\Eel\Helper;

/*
 * This file is part of the Ttree.MediaUtility package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use TYPO3\Eel\Exception;
use TYPO3\Eel\ProtectedContextAwareInterface;
use TYPO3\Flow\Annotations as Flow;

/**
 * EEL Image Helper
 */
class ColorHelper implements ProtectedContextAwareInterface
{

    /**
     * Convert RGB values to hexadecimal color code
     *
     * This method must receive an array of integer value, in correct order (red, green, blue)
     *
     * @param array $rgb
     * @return string
     */
    public function rgba2hex(array $rgb)
    {
        return vsprintf('#%s%s%s', array(
            str_pad(dechex(isset($rgb[0]) ? $rgb[0] : $rgb['red']), 2, "0", STR_PAD_LEFT),
            str_pad(dechex(isset($rgb[1]) ? $rgb[1] : $rgb['blue']), 2, "0", STR_PAD_LEFT),
            str_pad(dechex(isset($rgb[2]) ? $rgb[2] : $rgb['green']), 2, "0", STR_PAD_LEFT)
        ));
    }

    /**
     * Convert RGB values to CSS notation rgba()
     * @param array $rgba
     * @return string
     */
    public function rgba2css(array $rgba)
    {
        return vsprintf('rgba(%u,%u,%u,%F)', $rgba);
    }

    /**
     * Convert RGB values to CSS notation rgb()
     * @param array $rgba
     * @return string
     */
    public function rgb2css(array $rgba)
    {
        return vsprintf('rgb(%u,%u,%u)', $rgba);
    }

    /**
     * Convert hexadecimal color code to RGB values
     *
     * @param string $color
     * @return array
     * @throws Exception
     */
    public function hex2rgb($color)
    {
        $color = str_replace('#', '', $color);
        if (strlen($color) != 6) {
            throw new Exception('Invalid RGB color, must contains exactly 6 caracters', 1421009907);
        }
        $rgb = array();
        for ($x = 0; $x < 3; $x++) {
            $rgb[] = hexdec(substr($color, (2 * $x), 2));
        }
        return $rgb;
    }

    /**
     * All methods are considered safe
     *
     * @param string $methodName
     * @return boolean
     */
    public function allowsCallOfMethod($methodName)
    {
        return TRUE;
    }

}
