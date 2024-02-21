<?php

namespace App\Http\Controllers;

use App\Models\Gabarito;
use App\Models\Pixel;
use App\Models\RespostaPixel;
use Illuminate\Http\Request;

class Leitor extends Controller
{
    private $image;
    private $gabarito;
    private $name;
    private $type;
    private $width;
    private $heitht;


    public function processImage(Request $request)
    {
        if (isset($_FILES['image'])) {
            $obImage = $_FILES['image'];
            // echo "<pre>";
            //     dd($obImage);
            // echo "</pre>";
        }
        // dd($request->file('image'));
        $this->image = imagecreatefromstring(file_get_contents($obImage['tmp_name']));
        $this->gabarito = imagecreatefrompng(public_path() . '/images/gabarito.png');
        list($this->width, $this->heitht) = getimagesize($obImage['tmp_name']);
        $this->name = $obImage['name'];
        $this->type = $obImage['type'];


        $imagem_copia = imagecreatetruecolor($this->width, $this->heitht);
        imagecopy($imagem_copia, $this->image, 0, 0, 0, 0, $this->width, $this->heitht);


        $nova_imagem_com_acertos = imagecreatetruecolor($this->width, $this->heitht);
        imagecopy($nova_imagem_com_acertos, imagecreatefrompng(public_path() . '/images/cartao-resposta.png'), 0, 0, 0, 0, $this->width, $this->heitht);

        RespostaPixel::truncate();
        $alternativas_marcadas = [];
        for ($y = 0; $y < $this->heitht; $y++) {
            $alternativa_marcada_na_linha = null;

            for ($x = 0; $x < $this->width; $x++) {
                $cor_pixel = imagecolorat($this->image, $x, $y);
                $rgb = imagecolorsforindex($this->image, $cor_pixel);

                if ($rgb['red'] < 1 && $rgb['green'] < 1 && $rgb['blue'] < 1) {
                    $alternativa_marcada_na_linha = [$y, $x];
                    imagesetpixel($imagem_copia, $x, $y, imagecolorallocate($imagem_copia, 0, 0, 0));
                    if (imagecolorat($this->image, $x, $y) === imagecolorat($this->gabarito, $x, $y)) {
                        imagesetpixel($nova_imagem_com_acertos, $x, $y, imagecolorallocate($nova_imagem_com_acertos, 0, 0, 255));
                    }
                }
            }
            if ($alternativa_marcada_na_linha !== null) {
                $alternativas_marcadas[] = $alternativa_marcada_na_linha;

                RespostaPixel::updateOrCreate(
                    [
                        'y' => $alternativa_marcada_na_linha[0],
                        'x' => $alternativa_marcada_na_linha[1],
                    ],
                    [
                        'y' => $alternativa_marcada_na_linha[0],
                        'x' => $alternativa_marcada_na_linha[1],
                    ]
                );
                // Pixel::updateOrCreate(
                //     [
                //         'y' => $alternativa_marcada_na_linha[0],
                //         'x' => $alternativa_marcada_na_linha[1],
                //         'questao' => '5',
                //         'alternativa' => 'E',
                //     ],
                //     [
                //         'y' => $alternativa_marcada_na_linha[0],
                //         'x' => $alternativa_marcada_na_linha[1],
                //         'questao' => '5',
                //         'alternativa' => 'E',
                //     ]
                // );
            }
        }


        $quantidade_acertos = 0;
        $margem_erro = 40; // valor percentual da margem de erro
        foreach (Gabarito::get() as $questao) {

            $pixels = Pixel::where('questao', $questao->questao)->where('alternativa', $questao->alternativa)->get();

            $x = $pixels->pluck('x')->toArray();
            $y = $pixels->pluck('y')->toArray();

            $respostas = RespostaPixel::whereIn('x', $x)->whereIn('y', $y)->get();
            $area_corberta = ($respostas->count() / $pixels->count());
            dump($area_corberta);
            if ($area_corberta >= (1 - ($margem_erro / 100)) && $area_corberta <= (1 + ($margem_erro / 100))) {
                $quantidade_acertos++;
            }
        }
        // dd($quantidade_acertos);

        imagepng($imagem_copia, public_path() . '/images/image.png');
        imagepng($nova_imagem_com_acertos, public_path() . '/images/aluno_respostas.png');
        imagedestroy($this->image);
        imagedestroy($imagem_copia);
        imagedestroy($nova_imagem_com_acertos);
        // echo "<pre>";
        // dd('Qtd. pixels marcados: ' . $quantidade_acertos);
        // echo "</pre>";

        // $acertos = Pixel::whereIn(
        //     'y',
        //     array_map(function ($item) {
        //         return $item[0];
        //     }, $alternativas_marcadas)
        // )
        //     ->whereIn(
        //         'x',
        //         array_map(function ($item) {
        //             return $item[1];
        //         }, $alternativas_marcadas)
        //     )
        //     ->get()
        //     ->filter(function ($item) {
        //         // dd($item->alternativa,$item->questao);
        //         if (
        //             ($item->alternativa === 'E' && $item->questao === '1') ||
        //             ($item->alternativa === 'B' && $item->questao === '2') ||
        //             ($item->alternativa === 'C' && $item->questao === '3') ||
        //             ($item->alternativa === 'D' && $item->questao === '4') ||
        //             ($item->alternativa === 'A' && $item->questao === '5')
        //         ) {
        //             return true;
        //         }
        //     });

        $gabarito = Pixel::where(
            function ($query) {
                $query->where('alternativa', 'E')
                    ->where('questao', 1);
            }
        )
            ->orWhere(function ($query) {
                $query->where('alternativa', 'B')
                    ->where('questao', 2);
            })
            ->orWhere(function ($query) {
                $query->where('alternativa', 'C')
                    ->where('questao', 3);
            })
            ->orWhere(function ($query) {
                $query->where('alternativa', 'D')
                    ->where('questao', 4);
            })
            ->orWhere(function ($query) {
                $query->where('alternativa', 'A')
                    ->where('questao', 5);
            })
            ->get();

        // $respostas_aluno = $acertos->pluck('alternativa', 'questao');
        $respostas_corretas = $gabarito->pluck('alternativa', 'questao');

        dd(
            $quantidade_acertos,
            // 'Quantidade questões respondidas pelo aluno: ' . $respostas_aluno->count(),
            // $acertos->pluck('alternativa', 'questao')->toArray(),
            'Quantidade questões na prova: ' . $respostas_corretas->count(),
            $gabarito->pluck('alternativa', 'questao')->toArray(),
        );

        return response()->json($alternativas_marcadas);
    }
}
