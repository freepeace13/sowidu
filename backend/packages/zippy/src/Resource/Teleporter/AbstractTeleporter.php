<?php

/*
 * This file is part of Zippy.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alchemy\Zippy\Resource\Teleporter;

use Alchemy\Zippy\Exception\IOException;
use Alchemy\Zippy\Resource\Resource as ZippyResource;

/**
 * Class AbstractTeleporter
 *
 * @deprecated Typehint against TeleporterInterface instead and use GenericTeleporter
 *  with custom reader/writers instead. This class will be removed in v0.5.x
 */
abstract class AbstractTeleporter implements TeleporterInterface
{
    /**
     * Writes the target
     *
     * @param  string  $data
     * @param  string  $context
     * @return TeleporterInterface
     *
     * @throws IOException
     */
    protected function writeTarget($data, ZippyResource $resource, $context)
    {
        $target = $this->getTarget($context, $resource);

        if (!file_exists(dirname($target)) && mkdir(dirname($target)) === false) {
            throw new IOException(sprintf('Could not create parent directory %s', dirname($target)));
        }

        if (file_put_contents($target, $data) === false) {
            throw new IOException(sprintf('Could not write to %s', $target));
        }

        return $this;
    }

    /**
     * Returns the relative target of a Resource
     *
     * @param  string  $context
     * @return string
     */
    protected function getTarget($context, ZippyResource $resource)
    {
        return sprintf('%s/%s', rtrim($context, '/'), $resource->getTarget());
    }
}
