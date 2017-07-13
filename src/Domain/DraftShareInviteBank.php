<?php
declare(strict_types=1);


namespace Link0\Bunq\Domain;

final class DraftShareInviteBank
{

    const STATUS_USED = 'USED';
    const STATUS_CANCELLED = 'CANCELLED';
    const STATUS_PENDING = 'PENDING';

    /**
     * @var array
     */
    private $userAliasCreated;

    /**
     * @var string
     */
    private $status;

    /**
     * @var \DateTimeImmutable
     */
    private $expiration;

    /**
     * @var Id
     */
    private $shareInviteBankResponseId;

    /**
     * @var string
     */
    private $draftShareUrl;

    /**
     * @var array
     */
    private $draftShareSettings;

    public function __construct(array $draftShareInviteBank)
    {
        $timezone = new \DateTimeZone('UTC');

        $this->userAliasCreated = $draftShareInviteBank['user_alias_created'];
        $this->status = $draftShareInviteBank['status'];
        $this->expiration = new \DateTimeImmutable($draftShareInviteBank['expiration'], $timezone);
        $this->shareInviteBankResponseId = $draftShareInviteBank['share_invite_bank_response_id'];
        $this->draftShareUrl = $draftShareInviteBank['draft_share_url'];
        $this->draftShareSettings = $draftShareInviteBank['draft_share_settings'];
    }

    /**
     * @param array $draftShareInviteBank
     * @return DraftShareInviteBank
     * @internal param array $monetaryBankAccount
     */
    public static function fromArray(array $draftShareInviteBank) : DraftShareInviteBank
    {
        return new self($draftShareInviteBank);
    }

    /**
     * @return array
     */
    public function getUserAliasCreated(): array
    {
        return $this->userAliasCreated;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getExpiration(): \DateTimeImmutable
    {
        return $this->expiration;
    }

    /**
     * @return Id
     */
    public function getShareInviteBankResponseId(): Id
    {
        return $this->shareInviteBankResponseId;
    }

    /**
     * @return string
     */
    public function getDraftShareUrl(): string
    {
        return $this->draftShareUrl;
    }

    /**
     * @return array
     */
    public function getDraftShareSettings(): array
    {
        return $this->draftShareSettings;
    }
}