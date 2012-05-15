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
 * @package     Modern_Log
 * @subpackage  Writer
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */

/**
 * @category    Modern
 * @package     Modern_Log
 * @subpackage  Writer
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Log_Writer_Mail extends Zend_Log_Writer_Mail {

	/**
     * Create a new instance of Zend_Log_Writer_Mail
     *
     * @param  array|Zend_Config $config
     * @return Zend_Log_Writer_Mail
     */
    static public function factory($config)
    {
        $config = self::_parseConfig($config);
        $mail = self::_constructMailFromConfig($config);
        $writer = new self($mail);

        if (isset($config['layout']) || isset($config['layoutOptions'])) {
            $writer->setLayout($config);
        }
        if (isset($config['layoutFormatter'])) {
            $layoutFormatter = new $config['layoutFormatter'];
            $writer->setLayoutFormatter($layoutFormatter);
        }
        if (isset($config['subjectPrependText'])) {
            $writer->setSubjectPrependText($config['subjectPrependText']);
        }

        return $writer;
    }

	/**
     * Sends mail to recipient(s) if log entries are present.  Note that both
     * plaintext and HTML portions of email are handled here.
     *
     * Extended to include the request variables (get/post/cookie/server).
     *
     * @return void
     */
    public function shutdown()
    {
        // If there are events to mail, use them as message body.  Otherwise,
        // there is no mail to be sent.
        if (empty($this->_eventsToMail)) {
            return;
        }

        if ($this->_subjectPrependText !== null) {
            // Tack on the summary of entries per-priority to the subject
            // line and set it on the Zend_Mail object.
            $numEntries = $this->_getFormattedNumEntriesPerPriority();
            $this->_mail->setSubject(
                "{$this->_subjectPrependText} ({$numEntries})");
        }


        // Always provide events to mail as plaintext.
		$body = '';
		if(isset($_SERVER['REQUEST_URI'])) {
			$body .= "URI: " . $_SERVER['REQUEST_URI'] . "\n\n";
		}
		$body .= implode('', $this->_eventsToMail)
			. "\n\$_GET: " .
			print_r($_GET, true)
			. "\n\$_POST: " .
			print_r($_POST, true)
			. "\n\$_COOKIE: " .
			print_r($_COOKIE, true)
			. "\n\$_SERVER: " .
			print_r($_SERVER, true);
        $this->_mail->setBodyText($body);

        // If a Zend_Layout instance is being used, set its "events"
        // value to the lines formatted for use with the layout.
        if ($this->_layout) {
            // Set the required "messages" value for the layout.  Here we
            // are assuming that the layout is for use with HTML.
            $this->_layout->events =
                implode('', $this->_layoutEventsToMail);

            // If an exception occurs during rendering, convert it to a notice
            // so we can avoid an exception thrown without a stack frame.
            try {
                $this->_mail->setBodyHtml($this->_layout->render());
            } catch (Exception $e) {
                trigger_error(
                    "exception occurred when rendering layout; " .
                        "unable to set html body for message; " .
                        "message = {$e->getMessage()}; " .
                        "code = {$e->getCode()}; " .
                        "exception class = " . get_class($e),
                    E_USER_NOTICE);
            }
        }

        // Finally, send the mail.  If an exception occurs, convert it into a
        // warning-level message so we can avoid an exception thrown without a
        // stack frame.
        try {
            $this->_mail->send();
        } catch (Exception $e) {
            trigger_error(
                "unable to send log entries via email; " .
                    "message = {$e->getMessage()}; " .
                    "code = {$e->getCode()}; " .
                        "exception class = " . get_class($e),
                E_USER_WARNING);
        }
    }

}
