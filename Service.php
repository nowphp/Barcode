<?php
namespace Barcode;
// Including all required classes

require_once('class/BCGFontFile.php');
require_once('class/BCGColor.php');
require_once('class/BCGDrawing.php');

class Service {
    
    private $font = null;
    private $file_type = 'png';
    
    public function __construct($font_size = 20,$file_type = 'png')
    {       
        $this->font = new \BCGFontFile(EXTEND_PATH.'Barcode/Arial.ttf', $font_size);  
        if(in_array(strtolower($file_type),array('png','gif','jpeg'))) {
            $this->file_type = $file_type;
        }
    }
    public function drawing($content = null)
    {
        // Including the barcode technology
        require_once('class/BCGcode128.barcode.php');
        
        // The arguments are R, G, B for color.
        $color_black = new \BCGColor(0, 0, 0);
        $color_white = new \BCGColor(255, 255, 255);
        
        $drawException = null;
        try {
            $code = new \BCGcode128();
            $code->setScale(2); // Resolution
            $code->setThickness(30); // Thickness
            $code->setForegroundColor($color_black); // Color of bars
            $code->setBackgroundColor($color_white); // Color of spaces
            $code->setFont($this->font); // Font (or 0)
            $code->parse($content); // Text
        } catch(Exception $exception) {
            $drawException = $exception;
        }
        
        /* Here is the list of the arguments
         1 - Filename (empty : display on screen)
         2 - Background color */
        $drawing = new \BCGDrawing('', $color_white);
        if($drawException) {
            $drawing->drawException($drawException);
        } else {
            $drawing->setBarcode($code);
            $drawing->draw();
        }
        
        // Header that says it is an image (remove it if you save the barcode to a file)
        header('Content-Type: image/'.$this->file_type);
        // Draw (or save) the image into PNG format.
        $drawing->finish(\BCGDrawing::IMG_FORMAT_PNG);
    }
}
?>