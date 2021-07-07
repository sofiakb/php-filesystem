<?php
/**
 * This file contains Eol class.
 * Created by PhpStorm.
 * User: Sofiane Akbly <sofiane.akbly@gmail.com>
 * Date: 07/07/2021
 * Time: 11:25
 */

namespace Sofiakb\Filesystem;

use Sofiakb\Filesystem\Facades\File as FileFacade;

class Eol
{
    
    /**
     * Newline characters in different Operating Systems
     * The names given to the different sequences are:
     * ============================================================================================
     * NewL  Chars       Name     Description
     * ----- ----------- -------- ------------------------------------------------------------------
     * LF    0x0A        UNIX     Apple OSX, UNIX, Linux
     * CR    0x0D        TRS80    Commodore, Acorn BBC, ZX Spectrum, TRS-80, Apple II family, etc
     * LFCR  0x0A 0x0D   ACORN    Acorn BBC and RISC OS spooled text output.
     * CRLF  0x0D 0x0A   WINDOWS  Microsoft Windows, DEC TOPS-10, RT-11 and most other early non-Unix
     * and non-IBM OSes, CP/M, MP/M, DOS (MS-DOS, PC DOS, etc.), OS/2,
     * ----- ----------- -------- ------------------------------------------------------------------
     */
    const EOL_UNIX = 'lf';           // Code: \n
    const EOL_TRS80 = 'cr';          // Code: \r
    const EOL_ACORN = 'lfcr';        // Code: \n \r
    const EOL_WINDOWS = 'crlf';      // Code: \r \n
    
    const EOLS = [
        self::EOL_ACORN   => '\n\r',
        self::EOL_WINDOWS => '\r\n',
        self::EOL_UNIX    => '\n',
        self::EOL_TRS80   => '\r',
    ];
    
    public static function detect($str, &$key): string
    {
        static $eols = array(
            self::EOL_ACORN   => "\n\r",  // 0x0A - 0x0D - acorn BBC
            self::EOL_WINDOWS => "\r\n",  // 0x0D - 0x0A - Windows, DOS OS/2
            self::EOL_UNIX    => "\n",    // 0x0A -      - Unix, OSX
            self::EOL_TRS80   => "\r",    // 0x0D -      - Apple ][, TRS80
        );
        
        $key = "";
        $curCount = 0;
        $curEol = '';
        foreach ($eols as $k => $eol) {
            if (($count = substr_count($str, $eol)) > $curCount) {
                $curCount = $count;
                $curEol = $eol;
                $key = $k;
            }
        }
        return $curEol;
    }
    
    /**
     * Detects the EOL of an file by checking the first line.
     * @param string $filepath File to be tested (full pathname).
     * @return boolean false | Used key = enum('cr', 'lf', crlf').
     * @uses detect
     */
    public static function detectFileEOL(string $filepath)
    {
        if (!FileFacade::exists($filepath)) {
            return false;
        }
        
        // Gets the line length
        $handle = @fopen($filepath, "r");
        if ($handle === false) {
            return false;
        }
        $line = fgets($handle);
        $key = "";
        self::detect($line, $key);
        
        return self::EOLS[$key];
    }
    
}