<?php
/** vim: set expandtab tabstop=4 shiftwidth=4: 
   +----------------------------------------------------------------------+
   | Diagram:                                                             |
   | Offers you the ability to create diagram graphs                      |
   +----------------------------------------------------------------------+
   |                                                                      |
   | Copyright (C) 2004 Diogo Resende, diogo@ect-ua.com, Portugal         |
   |                                                                      |
   | This program is free software; you can redistribute it and/or        |
   | modify it under the terms of the GNU General Public License          |
   | as published by the Free Software Foundation; either version 2       |
   | of the License, or (at your option) any later version.               |
   |                                                                      |
   | This program is distributed in the hope that it will be useful,      |
   | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
   | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the        |
   | GNU General Public License for more details.                         |
   |                                                                      |
   | You should have received a copy of the GNU General Public License    |
   | along with this program; if not, write to the Free Software          |
   | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA            |
   | 02111-1307, USA.                                                     |
   |                                                                      |
   | Author: Diogo Resende, diogo@ect-ua.com, Portugal                    |
   |                                                                      |
   +----------------------------------------------------------------------+
  
*/

  /**
   * Diagram
   * Offers you the ability to create top-down tree diagrams
   *
   * @author JSooter
   * @version 0.0.2
   *
   */
  class Diagram
  {
    private $data;
    private $bgcolor;
    private $bordercolor;
    private $borderwidth;
    private $rect_bordercolor;
    private $rect_bgcolor;
	private $border_radius;
    private $fontcolor;
    private $font;
    private $fontwidth;
    private $fontheight;
    private $padding;
    private $inpadding;
    private $spacepadding;
    private $alpha; // 0 (opaque) to 127 (transparent)
    private $leftoffset;
    private $aspect_ratio;
    
    /**
     * Set some defaults
     * @param array $params 
     */
    public function __construct($params = null){
		// load some defaults
		$this->bgcolor          = !empty($params['bgcolor'])          ? $params['bgcolor']          : array(255, 255, 255);
		$this->bordercolor      = !empty($params['bordercolor'])      ? $params['bordercolor']      : array(100, 100, 100);
		$this->borderwidth      = !empty($params['borderwidth'])      ? $params['borderwidth']      : 1;
		$this->rect_bordercolor = !empty($params['rect_bordercolor']) ? $params['rect_bordercolor'] : array(170, 170, 170);
		$this->rect_bgcolor     = !empty($params['rect_bgcolor'])     ? $params['rect_bgcolor']     : array(200, 200, 200);
		$this->border_radius    = !empty($params['border_radius'])    ? $params['border_radius']    : null;
		$this->fontcolor        = !empty($params['fontcolor'])        ? $params['fontcolor']        : array(0, 0, 0);
		$this->font             = !empty($params['font'])             ? $params['font']             : 2;
		$this->fontwidth        = !empty($params['fontwidth'])        ? $params['fontwidth']        : 0;
		$this->fontheight       = !empty($params['fontheight'])       ? $params['fontheight']       : 0;
		$this->padding          = !empty($params['padding'])          ? $params['padding']          : 10;
		$this->inpadding        = !empty($params['inpadding'])        ? $params['inpadding']        : 10;
		$this->spacepadding     = !empty($params['spacepadding'])     ? $params['spacepadding']     : 5;
		$this->alpha            = !empty($params['alpha'])            ? $params['alpha']            : 0;
		$this->leftoffset       = !empty($params['leftoffset'])       ? $params['leftoffset']       : 0;
		$this->aspect_ratio     = !empty($params['aspect_ratio'])     ? $params['aspect_ratio']     : 1.333;
	}
    
    /**
     * Diagram::SetData()
     * Set diagram data. Should be an indexed array [of arrays ..]
     *
     * @param array $data
     */
    public function SetData($data){
        if (is_array($data)){
            $this->data = $data;
            $this->leftoffset = 0;
            return true;
        }
        else {
            return false;
        }
    }
    
    public function SetBackgroundColor($r, $g, $b){
		$this->bgcolor = array($r, $g, $b);
    }
	
    public function SetBorderColor($r, $g, $b){
		$this->bordercolor = array($r, $g, $b);
    }

    public function SetBorderWidth($n){
		$this->borderwidth = ($n < 0 ? 0 : (int) $n);
    }

    public function SetRectangleBackgroundColor($r, $g, $b){
		$this->rect_bgcolor = array($r, $g, $b);
    }

    public function SetRectangleBorderColor($r, $g, $b){
		$this->rect_bordercolor = array($r, $g, $b);
    }

    public function SetFontColor($r, $g, $b){
		$this->fontcolor = array($r, $g, $b);
    }

    public function SetFont($font){
		$this->font = $font;
    }

    public function SetPadding($p){
		$this->padding = (int) $p;
    }

    public function SetInPadding($p){
		$this->inpadding = (int) $p;
    }

    public function SetSpacing($p){
		$this->spacepadding = (int) $p;
    }

	/**
	 * 
	 */
    public function Draw($file = ""){
		if (count($this->data) == 0){
			return;
		}

		$arrk = array_keys($this->data);
		$this->fontwidth = imagefontwidth($this->font);
		$this->fontheight = imagefontheight($this->font);
		$maxw = $this->__GetMaxWidth($this->data);

		$w = $maxw + (2 * $this->padding) + 1;
		$h = $this->__GetMaxDeepness($this->data);
		$h = (2 * $this->padding) +
			(($this->fontheight + (2 * $this->inpadding)) * $h) +
			((2 * $this->spacepadding) * ($h - 1)) + 1;

		// calculate width and height to fit aspect ratio
		$aspect_height = $w / $this->aspect_ratio;
		$aspect_width  = $h / $this->aspect_ratio;
      
		// adjust width or height
		if($aspect_height > $h){
			$h = $aspect_height;
		} else if($aspect_width > $w){
		    $w = $aspect_width;
		}
      
		$this->im = imagecreatetruecolor($w, $h);
		
		// some distributions of php5 do not include 
		if(function_exists('imageantialias')){
			imageantialias($this->im,true);
		}
		// background color
		$this->__AllocateColor("im_bgcolor", $this->bgcolor, false);
		imagefilledrectangle($this->im, 0, 0, $w, $h, $this->im_bgcolor);
		if ($this->borderwidth > 0) {
			$this->__AllocateColor("im_bordercolor", $this->bordercolor);
			for ($i = 0; $i < $this->borderwidth; $i++) {
				imagerectangle($this->im, $i, $i, $w - 1 - $i, $h - 1 - $i, $this->im_bordercolor);
			}
		}
      
		// allocate colors
		$this->__AllocateColor("im_rect_bgcolor", $this->rect_bgcolor);
		$this->__AllocateColor("im_rect_bordercolor", $this->rect_bordercolor);
		$this->__AllocateColor("im_fontcolor", $this->fontcolor);
      
		// draw all data
		$this->__DrawData($this->data[$arrk[0]], $this->padding);
		
		// draw 1st square
		$rw = ($this->fontwidth * strlen($arrk[0])) + (2 * $this->inpadding);
		$x1 = round(($w - $rw) / 2);
		$y1 = $this->padding;
		$x2 = $x1 + $rw;
		$y2 = $y1 + (2 * $this->inpadding) + $this->fontheight;
		$this->__Rectangle($x1, $y1, $x2, $y2, $this->im_rect_bordercolor, $this->im_rect_bgcolor);
		imagestring($this->im, $this->font, $x1 + $this->inpadding, $y1 + $this->inpadding, $arrk[0], $this->im_fontcolor);
		$x1 = $x1 + round(($x2 - $x1) / 2);
		imageline($this->im, $x1, $y2 + 1, $x1, $y2 + $this->spacepadding - 1, $this->im_rect_bordercolor);
      
		// output
		if (strlen($file) > 0 && is_dir(dirname($file))){
			imagepng($this->im, $file);
		}
		else {
			header("Content-Type: image/png");
			imagepng($this->im,null,0);
		}
    }

    public function __DrawData(&$data, $offset = 0, $level = 1, $width = 0) {
		$top = $this->padding + ($level * (($this->spacepadding * 2) + $this->fontheight + (2 * $this->inpadding)));
		$startx = $endx = 0;
		foreach ($data as $k => $v) {
			if (is_array($v)) {
				$width = $this->__GetMaxWidth($v);
				$rw = ($this->fontwidth * strlen($k)) + (2 * $this->inpadding);
				if ($width < $rw) {
					$width = $rw;
				}

				$x1 = $offset + round(($width - $rw) / 2);
				$y1 = $top;
				$x2 = $x1 + $rw;
				$y2 = $y1 + (2 * $this->inpadding) + $this->fontheight;
		
				//echo "($x1,$y1)-($x2,$y2)<br>\n";
				$this->__Rectangle($x1, $y1, $x2, $y2, $this->im_rect_bordercolor, $this->im_rect_bgcolor);
				imagestring($this->im, $this->font, $x1 + $this->inpadding, $y1 + $this->inpadding, $k, $this->im_fontcolor);
			
				// upper line
				$x1 = $x1 + round(($x2 - $x1) / 2);
				imageline($this->im, $x1, $y1 - 1, $x1, $y1 - $this->spacepadding + 1, $this->im_rect_bordercolor);
	
				// lower line
				imageline($this->im, $x1, $y2 + 1, $x1, $y2 + $this->spacepadding - 1, $this->im_rect_bordercolor);

				$this->__DrawData($v, $offset, $level + 1, $width);
				$offset += $width + $this->spacepadding + 1;
			}
			else {
				$rw = ($this->fontwidth * strlen($v)) + (2 * $this->inpadding);

				if (count($data) == 1) {
					$offset += round(($width - $rw) / 2);
				}

				$x1 = $offset;
				$y1 = $top;
				$x2 = $x1 + $rw;
				$y2 = $y1 + (2 * $this->inpadding) + $this->fontheight;
          
				$this->__Rectangle($x1, $y1, $x2, $y2, $this->im_rect_bordercolor, $this->im_rect_bgcolor);
				imagestring($this->im, $this->font, $x1 + $this->inpadding, $y1 + $this->inpadding, $v, $this->im_fontcolor);

				// upper line
				$x1 = $x1 + round(($x2 - $x1) / 2);
				imageline($this->im, $x1, $y1 - 1, $x1, $y1 - $this->spacepadding + 1, $this->im_rect_bordercolor);

				$offset += $rw + $this->spacepadding + 1;
			}
			if ($startx == 0) {
				$startx = $x1;
			}
			$endx = $x1;
		}
		$top -= $this->spacepadding;
		imageline($this->im, $startx, $top, $endx, $top, $this->im_rect_bordercolor);
    }
    
    public function __GetMaxWidth(&$arr) {
		$c = 0;
		foreach ($arr as $k => $v) {
			if ($c > 0) {
				$c += $this->spacepadding + 1;
			}
			if (is_array($v)) {
				$n = $this->__GetMaxWidth($v);
				if ($n > (2 * $this->inpadding) + (imagefontwidth($this->font) * strlen($k))) {
					$c += $n;
				}
				else {
					$c += (2 * $this->inpadding) + (imagefontwidth($this->font) * strlen($k));
				}
			}
			else {
				$c += (2 * $this->inpadding) + (imagefontwidth($this->font) * strlen($v));
			}
		}
		return $c;
    }
    
    public function __GetMaxDeepness(&$arr) {
		$p = 0;
		foreach ($arr as $k => $v) {
			if (is_array($v)) {
				$r = $this->__GetMaxDeepness($v);
				if ($r > $p) {
					$p = $r;
				}
			}
		}
		return ($p + 1);
    }
    
    public function __Rectangle($x1, $y1, $x2, $y2, $color, $bgcolor) {
		if($this->border_radius > 0){
			$this->draw_roundrectangle($this->im, $x1, $y1, $x2, $y2, $this->border_radius, $color);
			$this->ImageRectangleWithRoundedCorners($this->im, $x1 + 1, $y1 + 1, $x2 - 1, $y2 - 1, $this->border_radius, $bgcolor);
		} else {
			imagerectangle($this->im, $x1, $y1, $x2, $y2, $color);
			imagefilledrectangle($this->im, $x1 + 1, $y1 + 1, $x2 - 1, $y2 - 1, $bgcolor);
		}
    }
    
    public function __AllocateColor($var, $color, $alpha = true) {
		$alpha = ($alpha ? $this->alpha : 0);
		$this->$var = imagecolorallocatealpha($this->im, $color[0], $color[1], $color[2], $alpha);
    }
	
    public function ImageRectangleWithRoundedCorners(&$im, $x1, $y1, $x2, $y2, $radius, $color) {
        // draw rectangle without corners
        imagefilledrectangle($im, $x1+$radius, $y1, $x2-$radius, $y2, $color);
        imagefilledrectangle($im, $x1, $y1+$radius, $x2, $y2-$radius, $color);
        // draw circled corners
        imagefilledellipse($im, $x1+$radius, $y1+$radius, $radius*2, $radius*2, $color);
        imagefilledellipse($im, $x2-$radius, $y1+$radius, $radius*2, $radius*2, $color);
        imagefilledellipse($im, $x1+$radius, $y2-$radius, $radius*2, $radius*2, $color);
        imagefilledellipse($im, $x2-$radius, $y2-$radius, $radius*2, $radius*2, $color);
    }
	
    public function draw_roundrectangle($img, $x1, $y1, $x2, $y2, $radius, $color,$filled=1) {
        if ($filled==1){
            imagefilledrectangle($img, $x1+$radius, $y1, $x2-$radius, $y2, $color);
            imagefilledrectangle($img, $x1, $y1+$radius, $x1+$radius-1, $y2-$radius, $color);
            imagefilledrectangle($img, $x2-$radius+1, $y1+$radius, $x2, $y2-$radius, $color);
    
            imagefilledarc($img,$x1+$radius, $y1+$radius, $radius*2, $radius*2, 180 , 270, $color, IMG_ARC_PIE);
            imagefilledarc($img,$x2-$radius, $y1+$radius, $radius*2, $radius*2, 270 , 360, $color, IMG_ARC_PIE);
            imagefilledarc($img,$x1+$radius, $y2-$radius, $radius*2, $radius*2, 90 , 180, $color, IMG_ARC_PIE);
            imagefilledarc($img,$x2-$radius, $y2-$radius, $radius*2, $radius*2, 360 , 90, $color, IMG_ARC_PIE);
        }
		else {
            imageline($img, $x1+$radius, $y1, $x2-$radius, $y1, $color);
            imageline($img, $x1+$radius, $y2, $x2-$radius, $y2, $color);
            imageline($img, $x1, $y1+$radius, $x1, $y2-$radius, $color);
            imageline($img, $x2, $y1+$radius, $x2, $y2-$radius, $color);
    
            imagearc($img,$x1+$radius, $y1+$radius, $radius*2, $radius*2, 180 , 270, $color);
            imagearc($img,$x2-$radius, $y1+$radius, $radius*2, $radius*2, 270 , 360, $color);
            imagearc($img,$x1+$radius, $y2-$radius, $radius*2, $radius*2, 90 , 180, $color);
            imagearc($img,$x2-$radius, $y2-$radius, $radius*2, $radius*2, 360 , 90, $color);
        }                
    }
}

