<?php  

class Captcha
{

	private $captchaImage;
	private $captchaControl;
	private $width = 120;
	private $height = 50;
	private $distanceBetweenNumbers = 17;
	private $minColorValue = 0;
	private $maxColorValue = 180; 
	private $minBackgroundColorValue = 200;
	private $maxBackgroundColorValue = 255;
	private $captchaInfoText = "";
	public static $captchaOriginProperty = "captchaOrigin";
	public static $getCaptchaProperty = "captcha";
	public static $captchaProperty = "captcha";
	private $fileName = "";
	private $filePath = "captcha/";
	private static $captchaPath = "captcha/";

	public function __construct( $alternativeInfoText = "captcha.info" )
	{
		$this->captchaInfoText = "Subtrahieren Sie von jeder Zahl eines und tragen sie sie in das texfeld ein"; 
	}

	public static function getCaptchaOriginProperty(  )
	{
		return self::$captchaOriginProperty;
	}

	public static function getCaptchaProperty(  )
	{
		return self::$getCaptchaProperty;
	}

	public function generateCaptcha( )
	{
		$this->captchaImage = imagecreatetruecolor($this->width, $this->height); 
		
		$white = imagecolorallocate($this->captchaImage, $this->generateRandomNumber($this->minBackgroundColorValue, $this->maxBackgroundColorValue), $this->generateRandomNumber($this->minBackgroundColorValue, $this->maxBackgroundColorValue), $this->generateRandomNumber($this->minBackgroundColorValue, $this->maxBackgroundColorValue));
		imagefill($this->captchaImage, 0, 0, $white);
		
		$captchaNumbers = "";

		for ( $i = 0; $i < 7; $i++ )
		{
			$nextNumber = $this->generateRandomNumber(1, 9);
			$captchaNumbers .= $nextNumber;
			imagestring($this->captchaImage, 5, $i*$this->distanceBetweenNumbers + 5, $this->generateRandomNumber(3, 30), $nextNumber, $this->generateRandomColor());
		}
		 
		$this->saveCaptcha($captchaNumbers);
	}

	public static function validate( $data )
	{  
		$captchaFileName = $data[self::getCaptchaOriginProperty()];
		$enteredCaptcha  = $data[self::getCaptchaProperty()]; 

		if ( $captchaFileName == "" || $enteredCaptcha == "" )
		{
			return false;
		}
		
		$pathToCaptcha = scandir(self::$captchaPath);

		for ( $i = 0; $i < count($pathToCaptcha); $i++ ) {
			if ( strlen($pathToCaptcha[$i]) > 3 ) {
				unlink(self::$captchaPath . "/" . $pathToCaptcha[$i]);
			}
		}

		$originalNumbers = explode("_", $captchaFileName)[1];
		$originalNumbers = explode(".", $originalNumbers)[0]; 

		return $originalNumbers == $enteredCaptcha;
	}

	public function getForm()
	{
		$html = "<input type='text' name='" . self::getCaptchaProperty() . "' id='" . self::getCaptchaProperty() . "'><input type='hidden' value='$this->fileName' name='" . self::getCaptchaOriginProperty() . "' id='" . self::getCaptchaOriginProperty() . "'>";
		return $html;
	}	

	public function getFormInfo()
	{
		$html = "<pre>{$this->captchaInfoText}</pre>";
		return $html;
	}	

	public function getImage()
	{
		$html = "<img src='{$this->fileName}' style='width:{$this->width}px;height:{$this->height}px;'>";
		return $html;
	}	

	private function saveCaptcha( $captchaNumbers )
	{
		ob_start(); 
		imagejpeg($this->captchaImage, NULL, 100); 
		$contents = ob_get_contents(); 
		ob_end_clean();
		 
		imagedestroy($this->captchaImage);
		$this->fileName = $this->filePath . time() . "_{$captchaNumbers}.jpg";		

		$fh = fopen($this->fileName, "w" );
		fwrite( $fh, $contents );
		fclose( $fh );	
	}

	private function generateRandomNumber( $min, $max )
	{
		return rand($min, $max);
	}

	private function generateRandomColor( )
	{ 
		return imagecolorallocate($this->captchaImage, $this->generateRandomNumber($this->minColorValue, $this->maxColorValue), $this->generateRandomNumber($this->minColorValue, $this->maxColorValue), $this->generateRandomNumber($this->minColorValue, $this->maxColorValue));
	}

}
