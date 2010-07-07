<?php
// vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 fdm=marker encoding=utf8 :
/**
 * JSONWriter
 *
 * Copyright (c) 2010, Nicolas Thouvenin
 *
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the author nor the names of its contributors may be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE REGENTS AND CONTRIBUTORS ``AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE REGENTS AND CONTRIBUTORS BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  JSONWriter
 * @package   JSONWriter
 * @author    Nicolas Thouvenin <nthouvenin@gmail.com>
 * @copyright 2010 Nicolas Thouvenin
 * @license   http://opensource.org/licenses/bsd-license.php BSD Licence
 */

/**
 * JSONWriter
 *
 * @category  JSONWriter
 * @package   JSONWriter
 * @author    Nicolas Thouvenin <nthouvenin@gmail.com>
 * @copyright 2010 Nicolas Thouvenin
 * @license   http://opensource.org/licenses/bsd-license.php BSD Licence
 */
class JSONWriter
{
    protected $a = array();
    protected $p;
    protected $h = null;
    protected $b = '';
    protected $l = 0;
    protected $i = false;
    protected $is = "\t";

    protected $memory = false;
    protected $mute = false;
    protected $stack = array();
    protected $target;


    /**
     * Constructeur
     */
    function __construct()
    {
    }
    /**
     * Constructeur
     */
    function __destruct()
    {
        if (is_resource($this->h)) fclose($this->h);
    }

    protected function &stack_push($n, $a = array())
    {
        $i = count($this->stack);
        $this->stack[$i] = array(
            strtoupper(substr($n, 17)),
            $a
        );
        return $this->stack[$i][1];
    }
    protected function stack_pop()
    {
        list(,$r) = array_pop($this->stack);
        return $r;
    }

    protected function stack_end0()
    {
        list($r,) = end($this->stack);
        return $r;
    }

    protected function &stack_end1()
    {
        $i = count($this->stack) - 1;
        return $this->stack[$i][1];
    }


    protected function _i($nl = true)
    {
        if ($this->i) {
            if ($nl) $this->b .= PHP_EOL;
            for($i=0; $i < $this->l; $i++) $this->b .= $this->is;
        }
    }
    protected function _ob()
    {
        ++$this->l;
        $this->b .= '{';

    }

    protected function _cb()
    {
        --$this->l;
        $this->b = rtrim($this->b, ',');
        $this->_i();
        $this->b .= '},';
    }
    protected function _k($n)
    {
        $this->_i();
        $this->b .= json_encode($n);
        $this->b .= ': ';
    }
    protected function _v($c)
    {
        $this->b .= json_encode($c);
        $this->b .= ',';
    }
    protected function _kv($n, $c)
    {
        $this->_k($n);
        $this->_v($c);
    }


    /**
     * Termine un attribut
     * @return boolean
     */
    function endAttribute()
    {
        $this->stack_pop();
        return true;
    }
    /**
     *  — Termine un bloc CDATA
     *  @return boolean
     */
    function endCData()
    {
        return true;
    }
    /**
     *  — Termine un commentaire
     *  @return boolean
     */
    function endComment()
    {
        $this->mute = false;
        $this->stack_pop();
        return true;
    }
    /**
     *  — Termine un document
     *  @return boolean
     */
    function endDocument()
    {
        $this->_cb();
        $a = $this->stack_pop();
        return true;
    }
    /**
     *  — Termine la liste des attributs de la DTD courante
     *  @return boolean
     */
    function endDTDAttlist()
    {
        $this->stack_pop();
        trigger_error(__METHOD__.' is not implemented.', E_USER_WARNING);
        return false;
    }
    /**
     *  — Termine l'élément de la DTD courante
     *  @return boolean
     */
    function endDTDElement()
    {
        $this->stack_pop();
        trigger_error(__METHOD__.' is not implemented.', E_USER_WARNING);
        return false;
    }
    /**
     *  — Termine l'entité de la DTD courante
     *  @return boolean
     */
    function endDTDEntity()
    {
        $this->stack_pop();
        trigger_error(__METHOD__.' is not implemented.', E_USER_WARNING);
        return false;
    }
    /**
     *  — Termine la DTD courante
     *  @return boolean
     */
    function endDTD()
    {
        $this->stack_pop();
        trigger_error(__METHOD__.' is not implemented.', E_USER_WARNING);
        return false;
    }
    /**
     *  — Termine l'élément courant
     *  @return boolean
     */
    function endElement()
    {
        $this->_cb();
        $z = $this->stack_pop();
        $a =& $this->stack_end1();
        $a[key($a)] = $z;
        var_dump( $z,$a);
        return true;
    }
    /**
     *  — Termine le PI courant
     *  @return boolean
     */
    function endPI()
    {
        $this->stack_pop();
        $this->target = '';
        return true;
    }
    /**
     *  — Affiche le buffer courant
     *  @return mixed
     */
    function flush($clear = true)
    {
        if (!is_bool($clear)) return false;

        $this->b = rtrim($this->b, ',');

        if ($this->memory) return true;
        if (!is_resource($this->h)) return false;
        $r = fwrite($this->h, $this->b);
        if ($clear) $this->b = '';
        return $r;
    }
    /**
     *  — Termine l'élément courant
     *  @return boolean
     */
    function fullEndElement()
    {
        return true;
    }
    /**
     *  — Crée un nouveau xmlwriter en utilisant la mémoire pour l'affichage des chaînes
     *  @return boolean
     */
    function openMemory()
    {
        $this->memory = true;
        $this->b = '';
        return true;
    }
    /**
     *  — Crée un nouveau xmlwriter, en utilisant l'URI source pour l'affichage
     *  @return boolean
     */
    function openURI($uri)
    {
        if (is_resource($this->h)) fclose($this->h);
        if (! $this->h = fopen($uri, 'w')) return false;
        $this->memory = false;
        return true;
    }
    /**
     *  — Retourne le buffer courant
     *  @return boolean
     */
    function outputMemory($clear = true)
    {
        if (!is_bool($clear)) return false;
        $r = $this->b;
        if ($clear) $this->b = '';
        return $r;
    }
    /**
     *  — Définit la chaîne à utiliser pour l'indentation
     *  @return boolean
     */
    function setIndentString($indentString)
    {
        if (!is_string($indentString)) return false;
        $this->is = $indentString;
        return true;
    }
    /**
     *  — Active ou non l'indentation
     *  @return boolean
     */
    function setIndent($indent)
    {
        if (!is_bool($indent)) return false;
        $this->i = $indent;
        return true;
    }
    /**
     *  — Crée un attribut pour l'espace de noms
     *  @return boolean
     */
    function startAttributeNS($prefix,  $name,  $uri)
    {
        if (!is_string($prefix)) return false;
        if (!is_string($name)) return false;
        if (!is_null($uri) and !is_string($uri)) return false;

        if (strpos($this->stack_end0(), 'ELEMENT') !== 0) return false;

        $this->stack_push(__METHOD__);

        if (!is_null($uri)) {
            $this->startAttribute('xmlns$'.$prefix);
            $this->text($uri);
            $this->endAttribute();
        }
        $this->startAttribute($prefix.'$'.$name);

        return true;
    }
    /**
     *  — Crée un attribut
     *  @return boolean
     */
    function startAttribute($name)
    {
        if (!is_string($name)) return false;

        if (strpos($this->stack_end0(), 'ELEMENT') !== 0) return false;
        $this->stack_push(__METHOD__);

        $this->_k($name);
        return true;
    }
    /**
     *  — Crée une balise CDATA
     *  @return boolean
     */
    function startCData()
    {
        return true;
    }
    /**
     *  — Crée un commentaire
     *  @return boolean
     */
    function startComment()
    {
        $this->mute = true;
        $this->stack_push(__METHOD__);
        return true;
    }
  
    /**
     *  — Crée une liste d'attributs pour la DTD
     *  @return boolean
     */
    function startDTDAttlist($name)
    {
        if (!is_string($name)) return false;
        $this->stack_push(__METHOD__);

        trigger_error(__METHOD__.' is not implemented.', E_USER_WARNING);
        return false;
    }
    /**
     *  — Crée un élément DTD
     *  @return boolean
     */
    function startDTDElement($qualifiedName)
    {
        if (!is_string($qualifiedName)) return false;
        $this->stack_push(__METHOD__);

        trigger_error(__METHOD__.' is not implemented.', E_USER_WARNING);
        return false;
    }
    /**
     *  — Crée une entité DTD
     *  @return boolean
     */
    function startDTDEntity($name, $isparam)
    {
        if (!is_string($name)) return false;
        if (!is_bool($isparam)) return false;
        $this->stack_push(__METHOD__);

        trigger_error(__METHOD__.' is not implemented.', E_USER_WARNING);
        return false;
    }
    /**
     *  — Crée une DTD
     *  @return boolean
     */
    function startDTD($qualifiedName,  $publicId = null,  $systemId = null)
    {
        if (!is_string($qualifiedName)) return false;
        if (!is_null($publicId) and !is_string($publicId)) return false;
        if (!is_null($systemId) and !is_string($systemId)) return false;
        $this->stack_push(__METHOD__);

        trigger_error(__METHOD__.' is not implemented.', E_USER_WARNING);
        return false;
    }
    /**
     *  — Crée un document
     *  @return boolean
     */
    function startDocument($version = '1.0',  $encoding = 'utf-8', $standalone = null)
    {
        if (!is_string($version)) return false;
        if (!is_string($encoding)) return false;
        if (!is_null($standalone) and !is_bool($standalone)) return false;
        $a =& $this->stack_push(__METHOD__);

        $a['version'] = $version;
        $a['encoding'] = $encoding;
        if (!is_null($standalone)) {
            $a['standalone'] = $standalone ?  'yes' : 'no';
        }

        $this->_ob();
        $this->_kv('version', $version);
        $this->_kv('encoding', $encoding);
        if (!is_null($standalone)) {
            $this->_kv('standalone', $standalone ?  'yes' : 'no');
        }
        return true;
    }
    /**
     *  — Crée un élément
     *  @return boolean
     */
    function startElement($name)
    {
        if (!is_string($name)) return false;

        $a =& $this->stack_end1();

        if (!isset($a[$name])) {
            $a[$name] = array();
            $this->stack_push(__METHOD__, $a[$name]);
        }
        elseif (!isset($a[$name][0])) {
            $t = $a['name'];
            $a[$name] = array($t);
            $this->stack_push(__METHOD__, $a[$name][0]);
        }
        else {
            $i = count($a[$name]);
            $a[$name][$i] = array();
            $this->stack_push(__METHOD__, $a[$name][$i]);
        }

        $this->_k($name);
        $this->_ob();
        return true;
    }
    /**
     *  — Crée un élément d'un espace de noms
     *  @return boolean
     */
    function startElementNS($prefix, $name, $uri)
    {
        if (!is_string($prefix)) return false;
        if (!is_string($name)) return false;
        if (!is_string($uri)) return false;
        $this->stack_push(__METHOD__);
        $this->_k($prefix.'$'.$name);
        $this->_ob();
        $this->_kv('xmlns$'.$prefix, $uri);
        return true;
    }
    /**
     *  — Crée une balise PI
     *  @return boolean
     */
    function startPI($target)
    {
        if (!is_string($target)) return false;
        $this->stack_push(__METHOD__);
        $this->target = $target;
        return true;
    }
    /**
     *  — Écrit du texte
     *  @return boolean
     */
    function text($content)
    {
        if (!is_string($content)) return false;
        if ($this->mute) return true;

        if (strpos($this->stack_end0(), 'ELEMENT') === 0) {
            $a =& $this->stack_end1();
            $a['$t'] = $content;
            $this->_kv('$t', $content);
        }
        elseif (strpos($this->stack_end0(), 'ATTRIBUTE') === 0) {
            $this->_v($content);
        }
        elseif (strpos($this->stack_end0(), 'PI') === 0 and $this->target !== '') {
            $this->_kv('$t', '<?'.$this->target.' '.$content.' ?>');
        }

        return true;
    }
    /**
     *  — Écrit un attribut d'un espace de noms
     *  @return boolean
     */
    function writeAttributeNS($prefix, $name, $uri, $content)
    {
        if (!is_string($prefix)) return false;
        if (!is_string($name)) return false;
        if (!is_null($uri) and !is_string($uri)) return false;
        if (!is_string($content)) return false;

        return ($this->startAttributeNS($prefix, $name, $uri) and $this->text($content) and $this->endAttribute()) ? true : false;
    }
    /**
     *  — Écrit un attribut
     */
    function writeAttribute($name, $content)
    {
        if (!is_string($name)) return false;
        if (!is_string($content)) return false;

        return ($this->startAttribute($name) and $this->text($content) and $this->endAttribute()) ? true : false;
    }
    /**
     *  — Écrit un bloc CDATA
     *  @return @boolean
     */
    function writeCData($content)
    {
        if (!is_string($content)) return false;
        return ($this->startCData() and $this->text($content) and $this->endCData()) ? true : false;
    }
    /**
     *  — Écrit un commentaire
     *  @return boolean
     */
    function writeComment($content)
    {
        if (!is_string($content)) return false;
        return ($this->startComment() and $this->text($content) and $this->endComment()) ? true : false;
    }
    /**
     *  — Écrit une liste d'attributs DTD
     *  @return boolean
     */
    function writeDTDAttlist($name, $content)
    {
        if (!is_string($name)) return false;
        if (!is_string($content)) return false;

        trigger_error(__METHOD__.' is not implemented.', E_USER_WARNING);
        return false;
    }
    /**
     *  — Écrit un élément DTD
     *  @return boolean
     */
    function writeDTDElement($name, $content)
    {
        if (!is_string($name)) return false;
        if (!is_string($content)) return false;

        trigger_error(__METHOD__.' is not implemented.', E_USER_WARNING);
        return false;
    }
    /**
     *  — Écrit une entité DTD
     *  @return boolean
     */
    function writeDTDEntity($name, $content, $pe, $pubid, $sysid, $ndataid)
    {
        if (!is_string($name)) return false;
        if (!is_string($content)) return false;
        if (!is_bool($pe)) return false;
        if (!is_string($pubid)) return false;
        if (!is_string($sysid)) return false;
        if (!is_string($ndataid)) return false;

        trigger_error(__METHOD__.' is not implemented.', E_USER_WARNING);
        return false;
    }
    /**
     *  — Écrit une DTD
     *  @return boolean
     */
    function writeDTD( string $name, $publicId = null, $systemId = null , $subset = null)
    {
        if (!is_string($name)) return false;
        if (!is_null($publicId) and !is_string($publicId)) return false;
        if (!is_null($systemId) and !is_string($systemId)) return false;
        if (!is_null($subset) and !is_string($subset)) return false;

        trigger_error(__METHOD__.' is not implemented.', E_USER_WARNING);
        return false;
    }
    /**
     *  — Écrit un élément d'un espace de noms
     *  @return boolean
     */
    function writeElementNS($prefix, $name, $uri, $content = null)
    {
        if (!is_string($prefix)) return false;
        if (!is_string($name)) return false;
        if (!is_null($content) and !is_string($content)) return false;
        return ($this->startElementNS($prefix, $name, $uri) and $this->text($content) and $this->endElement()) ? true : false;
    }
    /**
     *  — Écrit un élément
     *  @return boolean
     */
    function writeElement($name, $content = null)
    {
        if (!is_string($name)) return false;
        if (!is_null($content) and !is_string($content)) return false;
        return ($this->startElement($name) and $this->text($content) and $this->endElement()) ? true : false;
    }
    /**
     *  — Écrit la balise PI
     * @return boolean
     */
    function writePI($target, $content)
    {
        if (!is_string($target)) return false;
        if (!is_string($content)) return false;
        return ($this->startPI($target) and $this->text($content) and $this->endPI()) ? true : false;
    }
    /**
     *  — Écrit un texte XML brut
     *  @return boolean
     */
    function writeRaw($content, $isjson = false)
    {
        if (!is_string($content)) return false;
        if ($isjson or (!$isjson and json_decode($content, true, 1))) 
            $this->b .= $content;
        else 
            $this->b .= json_encode($content);
        return true;
    }
}
