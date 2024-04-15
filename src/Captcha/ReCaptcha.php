<?php

namespace Nigo\CaptchaImg\Captcha;

class ReCaptcha
{
    private int $recaptchaWidth;
    private int $height;
    private int $correlationWidthHeight;
    private int $countElementsOnImage;
    private int $countElementsOnReCaptcha = 6;
    private array $createdImages = [];
    private array $indexesRepeatImages = [];

    public function __construct(
        int $recaptchaWidth = 250,
        int $height = 200,
        int $correlationWidthHeight = 3,
        int $countElementsOnImage = 2
    )
    {
        $this->recaptchaWidth = $recaptchaWidth;
        $this->correlationWidthHeight = $correlationWidthHeight;
        $this->countElementsOnImage = $countElementsOnImage;
        $this->height = $height == 0 ? $this->recaptchaWidth / $this->correlationWidthHeight : $height;
    }

    public function create(): string
    {
        for ($i = 0; $i < $this->countElementsOnReCaptcha - 1; $i++) {
            $this->createdImages[] = $this->createImage();
        }

        return $this->createBlockReCaptcha();
    }

    public function setStyleForDivReCaptcha(string $style = null, bool $class = false): string
    {
        if (is_null($style)) {
            return "style='width: {$this->recaptchaWidth}px; display: grid; grid-template-columns: 1fr 1fr 1fr; width: {$this->recaptchaWidth}; grid-gap: 1px'";
        }

        if ($class) {
            return "$class='$style'";
        }

        return $style;
    }

    public function setCorrelationWidthHeight(int $correlationWidthHeight): void
    {
        $this->correlationWidthHeight = $correlationWidthHeight;
    }

    public function setCountElementsOnImage(int $countElementsOnImage): void
    {
        $this->countElementsOnImage = $countElementsOnImage;
    }

    public function setCountElementsOnReCaptcha(int $countElementsOnReCaptcha): void
    {
        $this->countElementsOnReCaptcha = $countElementsOnReCaptcha;
    }

    public function setRecaptchaWidth(int $width): void
    {
        $this->recaptchaWidth = $width;
    }

    public function getIndexesRepeatImages(): array
    {
        return $this->indexesRepeatImages;
    }

    private function createBlockReCaptcha(): string
    {
        $this->createRepeatImage();

        $block = "<div id='recaptcha' {$this->setStyleForDivReCaptcha()}>";

        foreach ($this->createdImages as $image) {
            $block .= "<img src='data:image/png;base64," . base64_encode($image) . "' />";
        }

        $block .= '</div>';

        return $block;
    }

    private function createRepeatImage(): void
    {
        $displacementIndex = rand(0, count($this->createdImages) - 1);
        $imageIndex = rand(0, count($this->createdImages) - 1);

        $repeatImage = $this->createdImages[$imageIndex];

        array_splice($this->createdImages, $displacementIndex, 0, $repeatImage);

        foreach ($this->createdImages as $index => $image) {
            if ($image == $repeatImage) {
                $this->indexesRepeatImages[] = $index;
            }
        }
    }

    private function createImage(): string
    {
        $image = imagecreatetruecolor($this->recaptchaWidth, $this->height);

        // Allocate colors
        $bgColor = $this->createBgColor();
        $elementColor = $this->createElementColor();

        // Fill the background
        imagefilledrectangle($image, 0, 0, $this->recaptchaWidth, $this->height, $bgColor);

        // Draw elements
        for ($i = 0; $i < $this->countElementsOnImage; $i++) {
            $elem = rand(0, 1);

            switch ($elem) {
                case 0:
                    $this->drawRectangle($image, $elementColor);
                    break;
                case 1:
                    $this->drawCircle($image, $elementColor);
                    break;
                default:
                    break;
            }
        }

        // Capture the output
        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();

        // Free up memory
        imagedestroy($image);

        return $imageData;
    }

    private function createBgColor(): int
    {
        do {
            $red = rand(50, 255);
            $green = rand(50, 255);
            $blue = rand(50, 255);
        } while ($red == $blue && $red == $green);

        return imagecolorallocatealpha(imagecreatetruecolor(1, 1), $red, $green, $blue, 63);
    }

    private function createElementColor(): int
    {
        return imagecolorallocate(imagecreatetruecolor(1, 1), rand(0, 255), rand(0, 255), rand(0, 255));
    }

    private function drawRectangle($image, $color): void
    {
        do {
            $x1 = rand(10, $this->recaptchaWidth - 20);
            $y1 = rand(10, $this->height - 20);
            $x2 = rand($x1, $this->recaptchaWidth - 10);
            $y2 = rand($y1, $this->height - 10);
        } while ($x2 - $x1 < 20 || $y2 - $y1 < 20);

        imagefilledrectangle($image, $x1, $y1, $x2, $y2, $color);
    }

    private function drawCircle($image, $color): void
    {
        do {
            $cx = rand(10, $this->recaptchaWidth - 10);
            $cy = rand(10, $this->height - 10);
            $radius = rand(10, 100);
        } while (
            $cx - $radius < 0 ||
            $cy - $radius < 0 ||
            $cx + $radius > $this->recaptchaWidth ||
            $cy + $radius > $this->height
        );

        imagefilledellipse($image, $cx, $cy, $radius * 2, $radius * 2, $color);
    }
}