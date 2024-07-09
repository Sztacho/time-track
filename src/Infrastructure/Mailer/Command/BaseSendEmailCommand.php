<?php

declare(strict_types=1);

namespace App\Infrastructure\Mailer\Command;

use App\Application\Shared\Command\CommandInterface;

readonly class BaseSendEmailCommand implements CommandInterface
{
    public function __construct(
        private string $sendTo,
        private string $subject,
        private string $htmlTemplate,
        private array $params,
        private bool $hasAttachment,
        private string $attachment,
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

    public function hasAttachment(): bool
    {
        return $this->hasAttachment;
    }

    public function getAttachment(): string
    {
        return $this->attachment;
    }

    public function getAttachmentName(): string
    {
        return $this->attachmentName;
    }
}