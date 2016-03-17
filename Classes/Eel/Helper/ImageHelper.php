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
use TYPO3\Media\Domain\Model\AssetInterface;
use TYPO3\Media\Domain\Model\ThumbnailConfiguration;
use TYPO3\Media\Domain\Service\ThumbnailService;

/**
 * EEL Image Helper
 */
class ImageHelper implements ProtectedContextAwareInterface
{

    /**
     * @Flow\Inject
     * @var ThumbnailService
     */
    protected $thumbnailService;

    /**
     * @param AssetInterface $asset
     * @return array
     * @throws Exception
     */
    public function averageColor(AssetInterface $asset)
    {
        $configuration = new ThumbnailConfiguration(null, 400, null, 400);
        $sampleAsset = $this->thumbnailService->getThumbnail($asset, $configuration);
        $image = $this->imageCreateFromAny($sampleAsset->getResource()->createTemporaryLocalCopy());
        $pixel = imagecreatetruecolor(1, 1);
        imagecopyresampled($pixel, $image, 0, 0, 0, 0, 1, 1, $sampleAsset->getWidth(), $sampleAsset->getHeight());
        $rgb = imagecolorat($pixel, 0, 0);
        return imagecolorsforindex($pixel, $rgb);;
    }

    /**
     * @param AssetInterface $asset
     * @param integer $sampleSize
     * @return float
     * @throws Exception
     * @see http://stackoverflow.com/questions/596216/formula-to-determine-brightness-of-rgb-color
     */
    public function brightness(AssetInterface $asset, $sampleSize = 10)
    {
        $configuration = new ThumbnailConfiguration(null, 400, null, 400);
        $sampleAsset = $this->thumbnailService->getThumbnail($asset, $configuration);
        $image = $this->imageCreateFromAny($sampleAsset->getResource()->createTemporaryLocalCopy());

        $width = imagesx($image);
        $height = imagesy($image);

        $xStep = (integer)($width / $sampleSize);
        $yStep = (integer)($height / $sampleSize);

        $totalBrightness = 0;

        $sampleNumber = 1;

        for ($x = 0; $x < $width; $x += $xStep) {
            for ($y = 0; $y < $height; $y += $yStep) {

                $rgb = imagecolorat($image, $x, $y);
                $red = ($rgb >> 16) & 0xFF;
                $green = ($rgb >> 8) & 0xFF;
                $blue = $rgb & 0xFF;

                $brightness = ($red + $red + $blue + $green + $green + $green) / 6;

                $totalBrightness += $brightness;

                $sampleNumber++;
            }
        }

        return $totalBrightness / $sampleNumber;
    }

    /**
     * Create an image resource from GIF, JPEG or PNG image
     *
     * @param string $filepath
     * @return resource
     * @throws Exception
     */
    protected function imageCreateFromAny($filepath)
    {
        switch (exif_imagetype($filepath)) {
            case 1 :
                $image = imageCreateFromGif($filepath);
                break;
            case 2 :
                $image = imageCreateFromJpeg($filepath);
                break;
            case 3 :
                $image = imageCreateFromPng($filepath);
                break;
            default:
                throw new Exception('Unsupported image type', 1421006963);
                break;
        }
        return $image;
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
