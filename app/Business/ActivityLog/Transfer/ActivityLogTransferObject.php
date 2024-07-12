<?php

namespace App\Business\ActivityLog\Transfer;

use App\Business\ActivityLog\Config\ActivityLogConstants;

class ActivityLogTransferObject
{
    private const AVAILABLE_LOG_TITLES = [
        ActivityLogConstants::INFO_LOG,
        ActivityLogConstants::WARNING_LOG,
        ActivityLogConstants::DANGER_LOG,
    ];

    private string $user;
    private string $title;

    private string $oldData;
    private string $newData;

    public function getUser(): string
    {
        return $this->user;
    }

    public function setUser(string $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        if (!in_array($title, self::AVAILABLE_LOG_TITLES)) {
            throw new \Exception('Invalid log title');
        }

        $this->title = $title;

        return $this;
    }

    public function getOldData(): string
    {
        return $this->oldData;
    }

    public function setOldData(string $oldData): self
    {
        $this->oldData = $oldData;

        return $this;
    }

    public function getNewData(): string
    {
        return $this->newData;
    }

    public function setNewData(string $newData): self
    {
        $this->newData = $newData;

        return $this;
    }

    public function getFormattedDescription(): string
    {
        if (!$this->newData) {
            return '';
        }

        return isset($this->oldData) && $this->oldData
            ? sprintf('%s updated to %s', $this->oldData, $this->newData)
            : sprintf('Added new %s', $this->newData);
    }
}
