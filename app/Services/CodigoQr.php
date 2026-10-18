<?php

namespace App\Services;

use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

/**
 * Genera códigos QR como imagen PNG para los correos.
 *
 * Gmail y Outlook no muestran el QR de la web (se dibuja con JavaScript) ni imágenes SVG,
 * así que en los correos va como PNG incrustado. La librería chillerlan/php-qrcode calcula
 * qué cuadraditos son negros, y el PNG se escribe aquí a mano (el formato es sencillo) para
 * no depender de la extensión GD, que no está garantizada en el PHP de Vercel.
 */
class CodigoQr
{
    /** Devuelve el PNG (contenido binario) de un QR con el texto indicado. */
    public static function png(string $texto, int $pixelesPorModulo = 8): string
    {
        $matriz = (new QRCode(new QROptions([
            'eccLevel'      => EccLevel::M,   // corrección de errores media: se lee aunque la pantalla tenga brillos
            'addQuietzone'  => true,          // margen blanco alrededor, necesario para que las cámaras lo lean
            'quietzoneSize' => 4,
        ])))->addByteSegment($texto)->getQRMatrix();

        $modulos = $matriz->getSize();
        $lado = $modulos * $pixelesPorModulo;

        // Imagen en escala de grises de 8 bits: cada fila empieza con un 0 (filtro «ninguno»)
        // y luego un byte por píxel (0 = negro, 255 = blanco)
        $pixeles = '';
        for ($y = 0; $y < $modulos; $y++) {
            $fila = "\0";
            for ($x = 0; $x < $modulos; $x++) {
                $fila .= str_repeat($matriz->check($x, $y) ? "\x00" : "\xFF", $pixelesPorModulo);
            }
            $pixeles .= str_repeat($fila, $pixelesPorModulo);
        }

        return "\x89PNG\r\n\x1a\n"
            . self::bloque('IHDR', pack('NNCCCCC', $lado, $lado, 8, 0, 0, 0, 0)) // ancho, alto, 8 bits, gris
            . self::bloque('IDAT', gzcompress($pixeles, 9))                      // píxeles comprimidos (zlib)
            . self::bloque('IEND', '');
    }

    /** Bloque PNG: longitud + tipo + datos + CRC32 (comprobación de errores). */
    private static function bloque(string $tipo, string $datos): string
    {
        return pack('N', strlen($datos)) . $tipo . $datos . pack('N', crc32($tipo . $datos));
    }
}
