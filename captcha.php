<?php
session_start();

/* generate random captcha text with no 1/l or 0/O or 2/Z*/
$characters = 'ABCDEFGHJKLMNPQRSTUVWXY3456789';
$captcha_text = '';

/* loop runs 6 times to build a string to use */
for ($i = 0; $i < 6; $i++) {
    $captcha_text .= $characters[random_int(0, strlen($characters) - 1)];
}

/* store correct answer in session */
$_SESSION['captcha_text'] = $captcha_text;

/* create image*/
$width = 180;
$height = 60;
$image = imagecreate($width, $height);

/* create colours using white/grey */
$background = imagecolorallocate($image, 255, 255, 255);
$text_color = imagecolorallocate($image, 34, 34, 34);
$line_color = imagecolorallocate($image, 180, 180, 180);
$noise_color = imagecolorallocate($image, 220, 220, 220);

/* adds 100 small pixel dots */
for ($i = 0; $i < 100; $i++) {
    imagesetpixel(
        $image,
        random_int(0, $width),
        random_int(0, $height),
        $noise_color
    );
}

/* add 5 random lines across the created image */
for ($i = 0; $i < 5; $i++) {
    imageline(
        $image,
        random_int(0, $width),
        random_int(0, $height),
        random_int(0, $width),
        random_int(0, $height),
        $line_color
    );
}

/* draw the captcha text, where to draw, font size, position, text, colour*/
imagestring($image, 5, 35, 22, $captcha_text, $text_color);

/* output image */
header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);
?>