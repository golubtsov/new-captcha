<?php

namespace Nigo\CaptchaImg\Captcha;

use GdImage;

class Captcha
{
    protected int $countCircles;

    protected int $maxCountCircles;

    protected array $circlesRadius = [];

    protected int $width;

    protected int $height;

    protected GdImage $img;

    protected false|int $bg;

    public function __construct(
        int  $width = 200,
        int  $height = 150,
        bool $alfa = true,
        int  $maxCountCircles = 8
    )
    {
        $this->width = $width;
        $this->height = $height;
        $this->img = imagecreatetruecolor($this->width, $this->height);
        $this->maxCountCircles = $maxCountCircles;
        $this->countCircles = rand(1, $this->maxCountCircles);
        imagesavealpha($this->img, $alfa);
    }

    public function fill(int $red = 255, int $green = 255, int $blue = 255, int $alfa = 0): static
    {
        $this->bg = imagecolorallocatealpha($this->img, $red, $green, $blue, $alfa);
        imagefill($this->img, 0, 0, $this->bg);
        return $this;
    }

    protected function createBgColor(): array
    {
        do {
            $red = rand(50, 255);
            $green = rand(50, 255);
            $blue = rand(50, 255);
        } while ($red == $blue && $red == $green);

        return [$red, $green, $blue];
    }

    protected function createCircles(): static
    {
        for ($i = 0; $i < $this->countCircles; $i++) {

            do {
                $radius = rand(10, 100);
                $cx = rand(10, $this->width - 20);
                $cy = rand(10, $this->height - 20);
            } while (
                $this->checkDiameterInArray($radius) ||
                $cx + $radius > $this->width ||
                $cx - $radius < 0 ||
                $cy + $radius > $this->height ||
                $cy - $radius < 0
            );

            $this->circlesRadius[] = $radius;

            $colors = $this->createBgColor();

            $bgEllipse = imagecolorallocatealpha($this->img, $colors[0], $colors[1], $colors[2], 20);

            imagefilledellipse($this->img, $cx, $cy, $radius, $radius, $bgEllipse);
        }

        return $this;
    }

    protected function checkDiameterInArray(int $radius): bool
    {
        return in_array($radius, $this->circlesRadius);
    }

    /** Return count circles on image */
    public function save(string $path, string $type = 'png'): int
    {
        $this->createCircles();

        match ($type) {
            'png' => imagepng($this->img, $path),
            'jpeg' => imagejpeg($this->img, $path),
        };

        imagedestroy($this->img);

        return $this->countCircles;
    }
}