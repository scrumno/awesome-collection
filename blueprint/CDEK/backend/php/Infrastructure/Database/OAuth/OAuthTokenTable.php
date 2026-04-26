<?php

namespace DigitalCollective\CDEK\Infrastructure\Database\OAuth;

use Doctrine\DBAL\Schema\Table;

class OAuthTokenTable extends Table
{
    public function getName(): string
    {
        return 'dc_cdek_oauth_token';
    }

    // TODO:
}
