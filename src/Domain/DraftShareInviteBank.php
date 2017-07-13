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
        $this->shareInviteBankResponseId = Id::fromInteger($draftShareInviteBank['"share_invite_bank_response_id"']);
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

}