<?php

/**
 * ModernWeb
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.modernweb.pl/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kontakt@modernweb.pl so we can send you a copy immediately.
 *
 * @category    Modern
 * @package     Modern_Mail
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2011 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */

/** @see Zend_Mail */
require_once('Zend/Mail.php');

/** @see Modern_Mime_Part */
require_once('Modern/Mime/Part.php');

/**
 * Klasa do budowania wiadomości email. Wymaga szablonu o odpowiedniej budowie
 * (@see Modern/Mail/Data/Template.tpl).
 * Jeżeli do e-maila są załączane obrazki, to należy podać także ścieżkę do katalogu z nimi.
 * Nie trzeba się martwić o typ MIME e-maila podczas załączania obrazków w treści czy
 * załączników - odpowiedni typ jest ustawiany automatycznie.
 *
 * @category    Modern
 * @package     Modern_Mail
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2011 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Mail extends Zend_Mail
{
    const DEFAULT_ENCODING = 'iso-8859-2';

    /**
     * Instancja klasy implementującej interfejs Zend_View_Interface.
     *
     * @var Zend_View_Interface
     */
    private $_view;

    /**
     * Tablica ze znalezionymi w treści HTMLowej e-maila obrazkami.
     *
     * @var array
     */
    private $_images = array();

    /**
     * Tablica asjocjacyjna zawierająca rozszerzenia dozwolonych obrazków
     * w treści HTMLowej e-maila - kluczami są te rozszerzenia, wartości
     * stanowią odpowiadające rozszerzeniom typy MIME obrazków.
     *
     * @var array
     */
    private $_allowedExt  = array(
        'gif'  => 'image/gif',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpe'  => 'image/jpeg',
        'bmp'  => 'image/bmp',
        'png'  => 'image/png',
        'tif'  => 'image/tiff',
        'tiff' => 'image/tiff',
        'swf'  => 'application/x-shockwave-flash',
    );

    /**
     * Oryginalna HTMLowa treść e-maila. Zostaje ona później zmieniona.
     * Nazwy obrazków są zastępowane na identyfikatory, np.:
     * <img src="foto.jpg"> zostaje zamienione na
     * <img src="cid:md5('foto.jpg')">
     * Dopiero tak zmodyfikowana treść jest włączana do e-maila (za pomocą
     * metody setBodyHtml)
     *
     * @var string
     */
    private $_htmlContent = false;

    /**
     * Zmienna przechowująca log z wysyłki e-maila
     *
     * @var string
     */
    private $_log = '';

    /**
     * Konstruktor. Wywołuje konstruktor klasy rodzica oraz tworzy obiekt Modern_View_Opt.
     *
     * @param Zend_View_Interface $view Referencja do obiektu klasy implementującej interfejs Zend_View_Interface.
     * @param string $enc Kodowanie znaków.
     */
    public function __construct(Zend_View_Interface $view, $enc = null)
    {
        if(null === $enc) {
            $enc = self::DEFAULT_ENCODING;
        }
        parent::__construct($enc);
        $this->_view = $view;
        $this->_view->assign('charset', $enc);
    }

    /**
     * Metoda powoduje obudowanie maila danymi z szablonu $template. Szablon ten powinien
     * mieć specyficzną budowę (@see Modern/Mail/Data/Template.tpl)
     *
     * @param string $template   ścieżka do szablonu
     * @param string|null $imagesDir   ścieżka do katalogu z obrazkami
     */
    public function buildEmail($template, $imagesDir = null)
    {
        $data = array(
            'subject',
            'textBody',
            'htmlBody'
        );

        foreach($data as $v) {
            $this->_view->type = $v;
            $$v = iconv('utf-8', $this->_charset.'//TRANSLIT', trim($this->_view->render($template)));
        }

        $this->setType(Zend_Mime::MULTIPART_ALTERNATIVE);
        $this->hasAttachments = false;
        $this->_parts = array();

        $this->_subject = null;
        $this->_resetHeader('Subject');
        $this->setSubject($subject);
        $this->setBodyText($textBody);
        $this->_htmlContent = $htmlBody;

        if(null !== $imagesDir)
            $this->_addImages($imagesDir);

        $this->setBodyHtml($this->_htmlContent);
    }

    /**
     * Ta metoda została przeze mnie nadpisana, ponieważ w oryginalnej
     * wersji brakowało wywołania metody setType.
     *
     * @param  Modern_Mime_Part $attachment
     * @return Modern_Mail Provides fluent interface
     */
    public function addAttachment(Zend_Mime_Part $attachment)
    {
        $this->addPart($attachment);
        $this->hasAttachments = true;
        $this->setType(Zend_Mime::MULTIPART_ALTERNATIVE);
        return $this;
    }

    /**
     * Metoda utworzona, aby nadpisać wartość stałej Zend_Mime::LINELENGTH
     * Wykorzystuje się tutaj Modern_Mime oraz Modern_Mime_Part
     * Jeżeli w przyszłości okaże się, że Zend zrobi tak, że tę stałą będzie
     * można nadpisać jakoś (wtedy to oczywiście nie będzie to już zapewne stała),
     * to można tę metodę oraz wymienione klasy usunąć (dodatkowo w metodzie
     * Modern_Mail::addAttachment trzeba będzie zmienić w treści
     * Modern_Mime na Zend_Mime.
     *
     * @param string $body Treść
     * @param string $mimeType Typ MIME załącznika
     * @param string $disposition Sposób umieszczenia pliku (inline - na przykład dla obrazków w treści albo jako załącznik)
     * @param string $encoding Sposób zakodowania załącznika
     * @param string $filename Nazwa pod jaką będzie widziany załącznik
     * @return Modern_Mime_Part
     */
    public function createAttachment($body,
                                     $mimeType    = Zend_Mime::TYPE_OCTETSTREAM,
                                     $disposition = Zend_Mime::DISPOSITION_ATTACHMENT,
                                     $encoding    = Zend_Mime::ENCODING_BASE64,
                                     $filename    = null)
    {
        $mp = new Modern_Mime_Part($body);
        $mp->encoding = $encoding;
        $mp->type = $mimeType;
        $mp->disposition = $disposition;
        $mp->filename = $filename;

        $this->addAttachment($mp);

        return $mp;
    }

    /**
     * Sets the text body for the message.
     *
     * Metoda pochodzi ze strony http://framework.zend.com/issues/browse/ZF-1041
     * Powoduje ona, że treść tekstowa nie jest ucinana po 76 znakach
     * i nie dostawia to dodatkowych kropek w linkach.
     *
     * @param string $txt
     * @param string $charset
     * @return Zend_Mime_Part
    */
    public function setBodyText($txt, $charset = null, $encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE)
    {
        if ($charset === null) {
            $charset = $this->_charset;
        }

        $mp = new Modern_Mime_Part($txt);
        $mp->encoding = Modern_Mime::ENCODING_8BIT;
        $mp->type = Modern_Mime::TYPE_TEXT;
        $mp->disposition = Modern_Mime::DISPOSITION_INLINE;
        $mp->charset = $charset;

        $this->_bodyText = $mp;
        return $mp;
    }

    /**
     * Sets the HTML body for the message
     *
     * Metoda pochodzi ze strony http://framework.zend.com/issues/browse/ZF-1041
     * Powoduje ona, że treść html'owa nie jest ucinana po 76 znakach
     * i nie dostawia to dodatkowych kropek w linkach.
     *
     * @param string $html
     * @param string $charset
     * @return Zend_Mime_Part
     */
    public function setBodyHtml($html, $charset = null, $encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE)
    {
        if ($charset === null) {
            $charset = $this->_charset;
        }

        $mp = new Modern_Mime_Part($html);
        $mp->encoding = Modern_Mime::ENCODING_8BIT;
        $mp->type = Modern_Mime::TYPE_HTML;
        $mp->disposition = Modern_Mime::DISPOSITION_INLINE;
        $mp->charset = $charset;

        $this->_bodyHtml = $mp;
        return $mp;
    }

    /**
     * Nadpisana metoda wysyłająca e-mail. Nadpisanie jest konieczne
     * ze względu na wymuszenie resetowania loga dla maili. Ponadto
     * dzięki tej nadpisanej metodzie klasa Modern_Mail posiada
     * mechanizm przechowywania loga z wysyłki pojedynczego
     * egzemplarza e-maila.
     *
     * @param Modern_Mail_Transport_Smtp $transport
     * @return Modern_Mail
     */
    public function send($transport = null)
    {
        //Przygotowanie transportu e-maila.
        //Jeśli nie jest zdefiniowany transport to następuje próba pobrania
        //domyślnego transportera SMTP - oczekuje się że zdefiniowany został
        //on już wcześniej.
        if ($transport === null) {
            if (!self::$_defaultTransport instanceof Zend_Mail_Transport_Abstract) {
                throw new Modern_Mail_Exception('Nie można wysłać e-maila. Brak zdefiniowanego transportu SMTP.');
            } else {
                $transport = self::$_defaultTransport;
            }
        }
        if (is_null($this->_date)) {
            $this->setDate();
        }

        //Wysyłamy e-mail
        $transport->send($this);

        //Zapisujemy loga do właściwości klasy z wysyłki i resetujemy go,
        //aby już nie doklejał przy następnej wysyłce loga z tej wysyłki.
        $connection = $transport->getConnection();
        if($connection instanceof Zend_Mail_Protocol_Abstract) {
            $this->_log = $connection->getLog();
            $connection->resetLog();
        }

        return $this;
    }

    /**
     * Metoda zwraca log z wysyłki e-maila
     *
     * @return string
     */
    public function getLog()
    {
        return $this->_log;
    }

    /**
     * Metoda resetuje zawatość headera dla pola TO
     *
     * @return Modern_Mail
     */
    public function resetTo()
    {
        $this->_resetRecipientAndHeader('To');
        return $this;
    }

    /**
     * Metoda dodaje odbiorcę e-maila.
     *
     * @param string $email
     * @param string $name
     * @return Modern_Mail
     */
    public function addTo($email, $name = '')
    {
        $name = iconv('utf-8', $this->_charset, trim($name));
        return parent::addTo($email, $name);
    }

    /**
     * Metoda dodaje odbiorcę e-maila na CC.
     *
     * @param string $email
     * @param string $name
     * @return Modern_Mail
     */
    public function addCc($email, $name = '')
    {
        $name = iconv('utf-8', $this->_charset, trim($name));
        return parent::addCc($email, $name);
    }

    /**
     * Metoda dodaje odbiorcę e-maila na Bcc.
     *
     * @param string $email
     * @param string $name
     * @return Modern_Mail
     */
    public function addBcc($email, $name = '')
    {
        $name = iconv('utf-8', $this->_charset, trim($name));
        return parent::addBcc($email, $name);
    }

    /**
     * Metoda dodaje nadawcę e-maila.
     *
     * @param string $email
     * @param string $name
     * @return Modern_Mail
     */
    public function setFrom($email, $name = '')
    {
        if('' != $name) {
            $name = iconv('utf-8', $this->_charset, trim($name));
        }
        return parent::setFrom($email, $name);
    }

    /**
     * Metoda usuwa odbiorcę.
     *
     * Jeśli odbiorcy należą do zbioru z pola TO wartość parametru $to powinna być ustawiona na true.
     * Jeśli znajdują się w polu CC lub BCC wtedy wartość powinna być ustawiona na false.
     *
     * @param boolean $to
     */
    protected function _resetRecipient($to = false)
    {
        if($to) {
            for($i = 0, $cnt = count((array)$this->_to); $i < $cnt; $i++) {
                $email = $this->_to[$i];
                if(isset($this->_recipients[$email])) {
                    unset($this->_recipients[$email]);
                }
            }
            $this->_to = array();
        } else {
            foreach((array)$this->_recipients as $email => $value) {
                if(!in_array($email, $this->_to)) {
                    unset($this->_recipients[$email]);
                }
            }
        }
    }

    /**
     * Metoda usuwa header o nazwie $headerName
     *
     * @param string $headerName
     */
    protected function _resetHeader($headerName)
    {
        if(isset($this->_headers[$headerName])) {
            unset($this->_headers[$headerName]);
        }
    }

    /**
     * Metoda usuwa odbiorców oraz header o nazwie $headerName
     *
     * @param string $headerName
     */
    protected function _resetRecipientAndHeader($headerName)
    {
        $this->_resetRecipient('To' == $headerName);
        $this->_resetHeader($headerName);
    }

    /**
     * Metoda zwraca tablicę obrazków znalezionych w źródle e-maila w części HTMLowej,
     * o ile e-mail będzie ją zawierał.
     *
     * Zwracana tablica zawiera 3 podtablice:
     * 0 - nazwy obrazków z cudzysłowiami
     * 1 - nazwy obrazków bez cudzysłowiów
     * 2 - rozszerzenia tych obrazków
     *
     * @return array
     */
    private function _findImages() {
        $htmlBody = $this->_htmlContent;
        if($htmlBody) {
            $extensions = array();
            foreach($this->_allowedExt as $k => &$v)
                $extensions[] = $k;
            preg_match_all('/(?:"|\')([^"\']+\.('.implode('|', $extensions).'))(?:"|\')/iU', $htmlBody, $images);
        }

        //eliminacja duplikatów
        $this->_images = array(0 => array(), 1 => array(), 2 => array());
        $tmp = array_unique($images[0]);
        $i = 0;
        foreach($tmp as $k => $v) {
            for($c = 0; $c <= 2; $c++) {
                $this->_images[$c][$i] = $images[$c][$k];
            }
            $i++;
        }

        return $this->_images;
    }

    /**
     * Metoda dołączająca do e-maila obrazki jeżeli zostały znalezione.
     * Obrazki powinny znajdować się w katalogu o ścieżce $dir.
     *
     * @param string $dir
     */
    private function _addImages($dir)
    {
        $this->_findImages();
        $htmlBody = $this->_htmlContent;
        if(count($this->_images[0]) > 0 && $htmlBody) {
            $this->_images[3] = array();
            for($i = 0, $cnt = count($this->_images[1]); $i < $cnt; $i++) {
                $path = $dir . DIRECTORY_SEPARATOR . $this->_images[1][$i];

                if(false === ($content = file_get_contents($path))) {
                    /** Modern_Mail_Exception */
                    require_once('Modern/Mail/Exception.php');
                    throw new Modern_Mail_Exception("Obrazek '$path' użyty w szablonie nie istnieje");
                }

                $attachment = $this->createAttachment($content);
                $attachment->type = $this->_allowedExt[$this->_images[2][$i]];
                $attachment->disposition = Zend_Mime::DISPOSITION_INLINE;
                $attachment->filename = $this->_images[1][$i];
                $attachment->id = md5($attachment->filename);
                $this->_images[3][$i] = '"' . 'cid:' . $attachment->id . '"';
            }

            $this->_htmlContent = str_replace($this->_images[0], $this->_images[3], $htmlBody);
            $this->setType(Zend_Mime::MULTIPART_RELATED);
        }
    }
}