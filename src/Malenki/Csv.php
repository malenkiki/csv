<?php
/*
Copyright (c) 2013 Michel Petit <petit.michel@gmail.com>

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
"Software"), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */


namespace Malenki;



/**
 * Class to play with CSV file.
 * @todo clean and modernize this…
 * @todo add method to change lines to rows or rows to lines
 * @todo translate into english!
 * @author Michel Petit <petit.michel@gmail.com> 
 */
class Csv 
{
    /**
     * Default field’s separator 
     */
    const SEPARATOR = ',';


    /**
     * CSV file’s name 
     * 
     * @var string
     * @access private
     */
    private $str_file = null;


    /**
     * custom separator
     * 
     * @var string
     * @access private
     */
    private $chr_separator = self::SEPARATOR;



    /**
     * Fields counter 
     * 
     * @var integer
     * @access private
     */
    private $int_fields = 0;



    /**
     * Lines counter 
     * 
     * @var integer
     * @access private
     */
    private $int_lines = 0;



    /**
     * Array with file’s values 
     * 
     * @var array
     * @access private
     */
    private $arr_content = array();
    
    
    
    /**
     * CSV valid or not? 
     * 
     * @var boolean
     * @access private
     */
    private $bool_goodcsv = false;



    /**
     * Constructor
     */
    public function __construct($file)
    {
        $this->str_file = $file;
        $this->isWellFormed();
    }



    /**
     * @brief Détermine si un nombre donné est bien compris dans l’intervalle des index de lignes.
     *
     * @param $integer Un entier
     * @return boolean
     */
    private function isInRangeOfRows($integer)
    {
        if($integer >= 0 && $integer < $this->int_lines)
        {
            return true;
        }
        else
        {
            return false;
        }
    }



    /**
     * @brief Détermine si un nombre donné correspond à un index possible pour un champ du fichier.
     *
     * @param $integer Un entier
     * @return Booléen
     */
    private function isInRangeOfFields($integer)
    {
        if($integer >= 0 && $integer < $this->int_fields)
        {
            return true;
        }
        else
        {
            return false;
        }
    }



    /**
     * @brief Teste si le fichier est un CSV correct.
     *
     * Pour être correct, le CSV doit comporter un nombre identique de séparateur 
     * sur chaque ligne : sinon, il n’est pas bon.
     *
     * Si le fichier est bon, il y a également des valeurs fournies à la classe 
     * comme le nombre de champs du fichier, le nombre de lignes et le contenu du 
     * fichier dans un tableau.
     */
    private function isWellFormed()
    {
        $this->bool_goodcsv = true;

        if(!is_null($this->str_file))
        {
            $lines = file($this->str_file, FILE_IGNORE_NEW_LINES);

            foreach ($lines as $i => $line)
            {
                if($i > 0)
                    $nbs2 = $nbs1;

                $nbs1 = substr_count($line, $this->chr_separator);

                if($i > 0 && $nbs1 != $nbs2)
                {
                    $this->bool_goodcsv = false;
                }
            }

            // comme c’est bon, on connaît le nombre de champs
            $this->int_fields  = $nbs1 + 1;
            // on a le nombre de lignes aussi
            $this->int_lines   = count($lines);
            $this->arr_content = $lines;
        }
    }



    /**
     * Checks whether CSV file is welldone.
     *
     * @return boolean
     */
    public function isValid()
    {
        return $this->bool_goodcsv;
    }



    /**
     * Set separator character.
     *
     * @param string $separator 
     */
    public function setSeparator($separator = ';')
    {
        if(is_string($separator) && strlen(trim($separator)) == 1 && !is_null($separator))
        {
            $this->chr_separator = trim($separator);
        }
        else
        {
            throw \InvalidArgumentException('Separator must be a valid character');
        }
    }



    /**
     * Get amount of lines
     *
     * @return Integer
     */
    public function getNumberOfLines()
    {
        return $this->int_lines;
    }



    /**
     * Get amount of fields.
     *
     * @return Integer
     */
    public function getNumberOfFields()
    {
        return $this->int_fields;
    }



    /**
     * Get one line as array.
     *
     * @return array 
     */
    public function getLine($number)
    {
        if(count($this->arr_content) == 0)
        {
            throw new \Exception('CSV file is void!');
        }

        if($this->isInRangeOfRows($number))
        {
            return explode($this->chr_separator, $this->arr_content[$number]);
        }
        else
        {
            throw new \OutOfRangeException('Row is out of range!');
        }
    }



    /**
     * Get field content at a specific line.
     *
     * @return string
     */
    public function getFieldAtLine($field, $line)
    {
        if($this->isInRangeOfFields($field))
        {
            $row = $this->getLine($line);

            return $row[$field];
        }
        else
        {
            throw new \OutOfRangeException('Field is out of range!');
        }
    }
}
