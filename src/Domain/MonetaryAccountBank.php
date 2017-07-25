<?php declare(strict_types = 1);

namespace Link0\Bunq\Domain;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Money\Currency;
use Money\Money;

final class MonetaryAccountBank
{
    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_CANCELLED = 'CANCELLED';
    const STATUS_PENDING_REOPEN = 'PENDING_REOPEN';

    const SUBSTATUS_REDEMPTION_VOLUNTARY = 'REDEMPTION_VOLUNTARY';
    const SUBSTATUS_NONE = 'NONE';
    const REASON_OTHER = 'OTHER';

    /**
     * @var Id
     */
    private $id;

    /**
     * @var string
     */
    private $description;

    /**
     * @var DateTimeInterface
     */
    private $created;

    /**
     * @var DateTimeInterface
     */
    private $updated;

    /**
     * @var Id
     */
    private $userId;

    /**
     * @var Alias[]
     */
    private $alias;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var float
     */
    private $balance;

    /**
     * @var Money
     */
    private $dailyLimit;

    /**
     * @var Money
     */
    private $dailySpent;

    /**
     * @var NotificationFilter[]
     */
    private $notificationFilters;

    /**
     * Private constructor
     */
    private function __construct(array $monetaryBankAccount)
    {
        $timezone = new DateTimeZone('UTC');

        $this->id = Id::fromInteger(intval($monetaryBankAccount['id']));
        $this->description = $monetaryBankAccount['description'];
        $this->created = new DateTimeImmutable($monetaryBankAccount['created'], $timezone);
        $this->updated = new DateTimeImmutable($monetaryBankAccount['updated'], $timezone);
        $this->userId = Id::fromInteger($monetaryBankAccount['user_id']);

        foreach ($monetaryBankAccount['alias'] as $aliasInfo) {
            $this->alias[] = new Alias(
                $aliasInfo['type'],
                $aliasInfo['value'],
                $aliasInfo['name']
            );
        }

        $this->currency = $monetaryBankAccount['balance']['currency'];
        $this->balance = (float) $monetaryBankAccount['balance']['value'];
        $this->dailyLimit = new Money($monetaryBankAccount['daily_limit']['value'], new Currency($this->currency));
        $this->dailySpent = new Money($monetaryBankAccount['daily_spent']['value'], new Currency($this->currency));

        foreach ($monetaryBankAccount['notification_filters'] as $notificationFilter) {
            $this->notificationFilters[] = NotificationFilter::fromArray($notificationFilter);
        }
    }

    /**
     * @param array $monetaryBankAccount
     * @return MonetaryAccountBank
     */
    public static function fromArray(array $monetaryBankAccount)
    {
        return new self($monetaryBankAccount);
    }

    /**
     * @return Id
     */
    public function id(): Id
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function description(): string
    {
        return $this->description;
    }

    /**
     * @return DateTimeInterface
     */
    public function created(): DateTimeInterface
    {
        return $this->created;
    }

    /**
     * @return DateTimeInterface
     */
    public function updated(): DateTimeInterface
    {
        return $this->updated;
    }

    /**
     * @return Alias[]
     */
    public function alias(): array
    {
        return $this->alias;
    }

    /**
     * @return float
     */
    public function balance(): float
    {
        return $this->balance;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }



    /**
     * @return Id
     */
    public function userId(): Id
    {
        return $this->userId;
    }

    /**
     * @return NotificationFilter[]
     */
    public function notificationFilters()
    {
        return $this->notificationFilters;
    }
}
