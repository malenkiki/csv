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
 * @brief Prend en charge les fichiers CSV.
 */
class ParserCsv {
    const SEPARATOR = '|'; //!< Séparateur de champ par défaut

    private $file      = null;            //!< Nom du fichier CSV
    private $separator = self::SEPARATOR; //!< Le séparateur utilisé
    private $fields    = 0;               //!< Le nombre de champs du fichier
    private $lines     = 0;               //!< Le nombre de lignes du fichier
    private $content   = null;            //!< Tableau des valeurs du fichier
    private $goodcsv   = false;           //!< Information sur la validité du fichier

    /**
     * @brief Le constructeur
     *
     * Prend obligatoirement en argument le nom du fichier CSV. Si le fichier 
     * existe, il est aussitôt lu pour déterminer sa validité : s’il est valide,
     * les différentes valeurs sont stockées et sont accessibles.
     * Pour savoir si le fichier est valide, après l’appel du constructeur, il 
     * faut utiliser la méthode $this->isGoodCsv().
     */
    public function __construct($file){
        if(file_exists($file)){
            $this->file = $file;
            $this->isWellFormed();
        } else {
            $this->goodcsv = false;
        }
    }

    /**
     * @brief Détermine si un nombre donné est bien compris dans l’intervalle des index de lignes.
     *
     * @param $integer Un entier
     * @return Booléen
     */
    private function isInRangeOfRows($integer){
        if($integer >= 0 and $integer < $this->lines)
            return true;
        else
            return false;
    }

    /**
     * @brief Détermine si un nombre donné correspond à un index possible pour un champ du fichier.
     *
     * @param $integer Un entier
     * @return Booléen
     */
    private function isInRangeOfFields($integer){
        if($integer >= 0 and $integer < $this->fields)
            return true;
        else
            return false;
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
    private function isWellFormed(){
        if(!is_null($this->file)){
            $lines = file($this->file, FILE_IGNORE_NEW_LINES);
            $nbl   = count($lines);

            for ($i = 0 ; $i < $nbl ; $i++){
                if($i > 0)
                    $nbs2 = $nbs1;

                $nbs1 = substr_count($lines[$i], $this->separator);

                if($i > 0 and $nbs1 != $nbs2)
                    $this->goodcsv = false;
            }

            // comme c’est bon, on connaît le nombre de champs
            $this->fields  = $nbs1 + 1;
            // on a le nombre de lignes aussi
            $this->lines   = $nbl;
            $this->content = $lines;
            $this->goodcsv = true;
        } else {
            $this->goodcsv = false;
        }
    }

    /**
     * @brief Détermine si le fichier est un bon CSV
     *
     * Si le fichier est une bon fichier CSV, alors retourne @c vrai, et @c faux 
     * dans le cas contraire.
     *
     * @return Booléen
     */
    public function isGoodCsv(){
        return $this->goodcsv;
    }

    /**
     * @brief Détermine le caractère séparateur de champs
     *
     * Fixe le caractère séparateur utilisé dans le fichier CSV.
     * Si aucune valeur n’est fournie, le caractère utilisé est un point virgule.
     *
     * @param $separator 
     */
    public function setSeparator($separator = ';'){
        if(strlen(trim($separator)) == 1 and !is_null($separator)){
            $this->separator = $separator;
            return true;
        } else {
            return false;
        }
    }

    /**
     * @brief Retourne le nombre de lignes du fichier.
     *
     * @return Entier
     */
    public function getNumberOfLines(){
        return $this->lines;
    }

    /**
     * @brief Retourne le nombre de champs du fichier.
     *
     * @return Entier
     */
    public function getNumberOfFields(){
        return $this->fields;
    }

    /**
     * @brief Retourne un tableau de la ligne choisie.
     *
     * Retourne un @c tableau si tout va bien, dans le cas contraire, retourne le
     * booléen @c false.
     *
     * @return Tableau ou booléen 
     */
    public function getLine($number){
        if(is_null($this->content))
            return false;

        if($this->isInRangeOfRows($number))
            return explode($this->separator,$this->content[$number]);
        else
            return false;
    }

    /**
     * @brief Retourne le contenu d’un champ à une ligne précise.
     *
     * Retourne la valeur si tout va bien, ou un booléen @c false dans le cas
     * contraire.
     *
     * @return Chaîne de caractères ou booléen
     */
    public function getFieldAtLine($field, $line){
        if($this->isInRangeOfFields($field)){
            $row = $this->getLine($line);

            if($row === false)
                return false;

            return $row[$field];
        } else {
            return false;
        }
    }
}