<?php
declare(strict_types=1);

namespace Link0\Bunq;

use Link0\Bunq\Domain\Keypair\PublicKey;

final class ResponseMapper
{
    /**
     * @param array $response
     *
     * @return \Generator
     */
    public function mapResponse(array $response): \Generator
    {
        $mapped = [];

        foreach ($response as $key => $item) {
            if (is_numeric($key)) {
                // Often only a single associative entry here
                foreach ($item as $type => $data) {
                    yield $key => $this->mapResponseItem($type, $data);
                }
            }
        }

        return $mapped;
    }

    /**
     * @param string $type
     * @param array  $data
     *
     * @return mixed
     *
     * @throws \Exception
     */
    private function mapResponseItem(string $type, array $data)
    {
        switch ($type) {
            case 'DeviceServer':
                return Domain\DeviceServer::fromArray($data);
            case 'MonetaryAccountBank':
                return Domain\MonetaryAccountBank::fromArray($data);
            case 'UserPerson':
                return Domain\UserPerson::fromArray($data);
            case 'UserCompany':
                return Domain\UserCompany::fromArray($data);
            case 'Id':
                return Domain\Id::fromInteger($data['id']);
            case 'CertificatePinned':
                return Domain\Certificate::fromArray($data);
            case 'Payment':
                return Domain\Payment::fromArray($data);
            case 'ServerPublicKey':
                return PublicKey::fromServerPublicKey($data);
            case 'Token':
                return Domain\Token::fromArray($data);
            case 'DraftShareInviteBank':
                return Domain\DraftShareInviteBank::fromArray($data);
            default:
                throw new \Exception("Unknown struct type: " . $type);
        }
    }
}
