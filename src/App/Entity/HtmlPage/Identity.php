<?php
declare(strict_types = 1);

namespace App\Entity\HtmlPage;

use App\Entity\HttpResource\Identity as HttpResourceIdentity;
use Domain\Entity\HtmlPage\Identity as IdentityInterface;
use Innmind\Rest\Server\Identity as RestIdentity;

final class Identity extends HttpResourceIdentity implements IdentityInterface, RestIdentity
{
}
