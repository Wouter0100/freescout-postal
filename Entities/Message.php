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
        $raw = preg_replace('/(charset\s*=\s*"?)(windows-1256)("?)/i', '$1utf-8$3', $raw);

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

    public function getHTMLBody()
    {
        return $this->message->getHtmlContent();
    }

    public function getTextBody()
    {
        return $this->message->getTextContent();
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

    public function getSubject()
    {
        return $this->message->getHeaderValue(HeaderConsts::SUBJECT);
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
