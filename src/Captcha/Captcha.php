<?php

namespace Nigo\CaptchaImg\Captcha;

use GdImage;

class Captcha
{
    private int $width;

    private int $height;

    private GdImage $img;

    private false|int $bg;

    public function __construct(int $width = 200, int $height = 150, bool $alfa = false)
    {
        $this->width = $width;
        $this->height = $height;
        $this->img = imagecreatetruecolor($this->width, $this->height);
        imagesavealpha($this->img, $alfa);
    }

    public function fill(int $red = 255, int $green = 255, int $blue = 255, int $alfa = 0): static
    {
        $this->bg = imagecolorallocatealpha($this->img, $red, $green, $blue, $alfa);
        imagefill($this->img, 0, 0, $this->bg);
        return $this;
    }

    public function save(string $path, string $type = 'png'): void
    {
        $col_ellipse = imagecolorallocatealpha($this->img, 255, 255, 0, 80);
        imagefilledellipse($this->img, 50, 50, 50, 50, $col_ellipse);

        $col_ellipse2 = imagecolorallocatealpha($this->img, 255, 5, 200, 80);
        imagefilledellipse($this->img, 50, 20, 70, 50, $col_ellipse2);

        match ($type) {
            'png' => imagepng($this->img, $path),
            'jpeg' => imagejpeg($this->img, $path),
        };

        imagedestroy($this->img);
    }
}