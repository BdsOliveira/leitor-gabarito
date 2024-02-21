<?php

namespace App\Http\Controllers;

class Image extends Controller
{

    protected $image;
    protected $gabarito;
    private $name;
    private $type;
    protected $width;
    protected $heitht;

    public function __construct($file)
    {
        $this->image = imagecreatefromstring(file_get_contents($file['tmp_name']));
        $this->gabarito = imagecreatefrompng('images/gabarito.png');
        list($this->width, $this->heitht) = getimagesize($file['tmp_name']);
        $this->name = $file['name'];
        $this->type = $file['type'];
    }
}
