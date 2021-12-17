<?php

namespace Modules\Postal\Entities;

use ZBateson\MailMimeParser\Message\Part\MessagePart;

class Attachment
{
    /** @var string */
    public $id;

    /** @var string */
    public $img_src;

    /** @var MessagePart */
    private $attachmentPart;

    public function __construct($attachmentPart)
    {
        $this->attachmentPart = $attachmentPart;

        $this->id = $this->attachmentPart->getContentId();
        $this->img_src = $this->attachmentPart->getFilename();
    }

    public function getName()
    {
        if ($this->getType() === \App\Attachment::TYPE_MESSAGE) {
            return $this->attachmentPart->getContentId();
        }

        return $this->attachmentPart->getFilename();
    }

    public function getId()
    {
        return $this->attachmentPart->getContentId();
    }

    public function getType()
    {
        return \App\Attachment::detectType($this->getMimeType());
    }

    public function getMimeType()
    {
        return $this->attachmentPart->getContentType();
    }

    public function getContent()
    {
        return $this->attachmentPart->getContent();
    }


}
