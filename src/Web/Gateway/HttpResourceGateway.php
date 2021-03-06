<?php
declare(strict_types = 1);

namespace Web\Gateway;

use Innmind\Rest\Server\{
    Gateway,
    ResourceListAccessor,
    ResourceAccessor,
    ResourceCreator,
    ResourceUpdater,
    ResourceRemover,
    ResourceLinker,
    ResourceUnlinker,
    Exception\ActionNotImplemented
};

final class HttpResourceGateway implements Gateway
{
    private HttpResourceGateway\ResourceCreator $resourceCreator;
    private HttpResourceGateway\ResourceAccessor $resourceAccessor;
    private HttpResourceGateway\ResourceLinker $resourceLinker;

    public function __construct(
        HttpResourceGateway\ResourceCreator $resourceCreator,
        HttpResourceGateway\ResourceAccessor $resourceAccessor,
        HttpResourceGateway\ResourceLinker $resourceLinker
    ) {
        $this->resourceCreator = $resourceCreator;
        $this->resourceAccessor = $resourceAccessor;
        $this->resourceLinker = $resourceLinker;
    }

    public function resourceListAccessor(): ResourceListAccessor
    {
        throw new ActionNotImplemented;
    }

    public function resourceAccessor(): ResourceAccessor
    {
        return $this->resourceAccessor;
    }
    public function resourceCreator(): ResourceCreator
    {
        return $this->resourceCreator;
    }

    public function resourceUpdater(): ResourceUpdater
    {
        throw new ActionNotImplemented;
    }

    public function resourceRemover(): ResourceRemover
    {
        throw new ActionNotImplemented;
    }

    public function resourceLinker(): ResourceLinker
    {
        return $this->resourceLinker;
    }

    public function resourceUnlinker(): ResourceUnlinker
    {
        throw new ActionNotImplemented;
    }
}
