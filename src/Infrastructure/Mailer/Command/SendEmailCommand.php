<?php

declare(strict_types=1);

namespace App\Infrastructure\Mailer\Command;

use App\Application\Shared\Command\CommandInterface;

readonly class SendEmailCommand implements CommandInterface
{
    public function __construct(
        private string $sendTo,
        private string $subject,
        private string $htmlTemplate,
        private array $params,
        private string $attachment,
        private string $otherAttachmentFile,
        private bool $invoice = false,
        private bool $otherAttachment = false,
        private string $attachmentName = ''

    ) {
    }

    public function getSendTo(): string
    {
        return $this->sendTo;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getHtmlTemplate(): string
    {
        return $this->htmlTemplate;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function hasInvoice(): bool
    {
        return $this->invoice;
    }

    public function getAttachment(): string
    {
        return $this->attachment;
    }

    public function hasOtherAttachment(): bool
    {
        return $this->otherAttachment;
    }

    public function getAttachmentName(): string
    {
        return $this->attachmentName;
    }

    public function getOtherAttachmentFile(): string
    {
        return $this->otherAttachmentFile;
    }
}