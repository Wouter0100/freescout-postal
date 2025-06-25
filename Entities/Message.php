<?php

namespace Modules\Postal\Entities;

use Webklex\IMAP\Support\AttachmentCollection;
use ZBateson\MailMimeParser\Header\HeaderConsts;
use ZBateson\MailMimeParser\MailMimeParser;

class Message
{
    /** @var \ZBateson\MailMimeParser\Message */
    private $message;

    /** @var string */
    private $raw;

    public function __construct($raw)
    {
        $this->message = (new MailMimeParser())->parse($this->raw = $raw);
    }

    public function getId()
    {
        return $this->message->getHeaderValue(HeaderConsts::MESSAGE_ID);
    }

    public function getReplyTo()
    {
        return $this->_getEmailHeader(HeaderConsts::REPLY_TO);
    }

    public function getInReplyTo()
    {
        return $this->message->getHeaderValue(HeaderConsts::IN_REPLY_TO);
    }

    public function getFrom()
    {
        return $this->_getEmailHeader(HeaderConsts::FROM);
    }

    public function getTo()
    {
        return $this->_getEmailHeader(HeaderConsts::TO);
    }

    public function getCc()
    {
        return $this->_getEmailHeader(HeaderConsts::CC);
    }

    public function getBcc()
    {
        return $this->_getEmailHeader(HeaderConsts::BCC);
    }

    public function getReferences()
    {
        return $this->message->getHeaderValue(HeaderConsts::REFERENCES);
    }

    public function getAttachments()
    {
        $attachments = AttachmentCollection::make([]);

        foreach ($this->message->getAllAttachmentParts() as $attachmentPart) {
            $attachment = new Attachment($attachmentPart);

            if ($attachment->getName() !== null) {
                if ($attachment->getId() !== null) {
                    $attachments->put($attachment->getId(), $attachment);
                } else {
                    $attachments->push($attachment);
                }
            }
        }

        return $attachments;
    }

    public function hasAttachments()
    {
        return $this->message->getAttachmentCount() !== 0;
    }

    /**
     * Get HTML content and convert to UTF-8 if necessary.
     */
    public function getHTMLBody()
    {
        $html = $this->message->getHtmlContent();
        $charset = $this->message->getHtmlCharset();

        // If a charset is defined and it's not already UTF-8, convert it.
        if ($html && $charset && strcasecmp($charset, 'UTF-8') != 0) {
            $html = mb_convert_encoding($html, 'UTF-8', $charset);
        }

        return $html;
    }

    /**
     * Get text content and convert to UTF-8 if necessary.
     */
    public function getTextBody()
    {
        $text = $this->message->getTextContent();
        $charset = $this->message->getTextCharset();

        // If a charset is defined and it's not already UTF-8, convert it.
        if ($text && $charset && strcasecmp($charset, 'UTF-8') != 0) {
            $text = mb_convert_encoding($text, 'UTF-8', $charset);
        }

        return $text;
    }

    public function getHeader()
    {
        $headers = '';

        foreach ($this->message->getRawHeaders() as $header) {
            $headers .= $header[0] . ': ' . $header[1] . PHP_EOL;
        }

        return trim($headers);
    }

    public function getRawBody()
    {
        return $this->raw;
    }

    /**
     * Get subject and ensure it is valid UTF-8.
     */
    public function getSubject()
    {
        $subject = $this->message->getHeaderValue(HeaderConsts::SUBJECT);

        // Ensure the final output is valid UTF-8, replacing invalid characters.
        if ($subject && !mb_check_encoding($subject, 'UTF-8')) {
            return mb_convert_encoding($subject, 'UTF-8', 'auto');
        }

        return $subject;
    }

    private function _getEmailHeader($name)
    {
        $header = $this->message->getHeader($name);

        return is_null($header) ? [] : array_map(function($address) {
            $return = new \stdClass();
            $return->mail = $address->getValue();
            $return->personal = $address->getName();
            $return->full = ($return->personal) ? $return->personal . ' <' . $return->mail . '>' : $return->mail;
            return $return;
        }, $header->getAddresses());
    }

    /**
     * Stub for setFlagss
     */
    public function setFlag()
    {
        // Nope
    }
}
