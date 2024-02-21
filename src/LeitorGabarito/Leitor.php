<?php

namespace App\LeitorGabarito;

class Leitor extends Image
{

    public function __construct($image)
    {
        parent::__construct($image);
        echo "<pre>";
        print_r($this->processImage());
        echo "</pre>";
    }

    public function processImage()
    {
        $imagem_copia = imagecreatetruecolor($this->width, $this->heitht);
        imagecopy($imagem_copia, $this->image, 0, 0, 0, 0, $this->width, $this->heitht);
        

        $nova_imagem_com_acertos = imagecreatetruecolor($this->width, $this->heitht);
        imagecopy($nova_imagem_com_acertos, imagecreatefrompng('images/cartao-resposta.png'), 0, 0, 0, 0, $this->width, $this->heitht);
        
        $alternativas_marcadas = [];
        $quantidade_acertos = 0;
        for ($y = 0; $y < $this->heitht; $y++) {
            $alternativa_marcada_na_linha = null;

            for ($x = 0; $x < $this->width; $x++) {
                $cor_pixel = imagecolorat($this->image, $x, $y);
                $rgb = imagecolorsforindex($this->image, $cor_pixel);

                if ($rgb['red'] < 30 && $rgb['green'] < 30 && $rgb['blue'] < 30) {
                    $alternativa_marcada_na_linha = [$y, $x];
                    imagesetpixel($imagem_copia, $x, $y, imagecolorallocate($imagem_copia, 0, 0, 0));
                    if(imagecolorat($this->image, $x, $y) == imagecolorat($this->gabarito, $x, $y)) {
                        imagesetpixel($nova_imagem_com_acertos, $x, $y, imagecolorallocate($nova_imagem_com_acertos, 0, 0, 255));
                        $quantidade_acertos++;
                    }
                }
            }
            if ($alternativa_marcada_na_linha !== null) {
                $alternativas_marcadas[] = $alternativa_marcada_na_linha;
            }
        }

        imagepng($imagem_copia, 'images/image.png');
        imagepng($nova_imagem_com_acertos, 'images/acertos.png');
        imagedestroy($this->image);
        imagedestroy($imagem_copia);
        imagedestroy($nova_imagem_com_acertos);
        echo "<pre>";
        print_r('Qtd. acertos: ' . $quantidade_acertos);
        echo "</pre>";


        return $alternativas_marcadas;
    }
}