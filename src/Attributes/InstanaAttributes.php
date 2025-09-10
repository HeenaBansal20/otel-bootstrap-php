<?php

namespace Heena\OpenTelemetry\Attributes;

use OpenTelemetry\SDK\Trace\SpanProcessorInterface;
use OpenTelemetry\SDK\Trace\ReadWriteSpanInterface;
use OpenTelemetry\SDK\Common\Attribute\Attributes;
use OpenTelemetry\Context\ContextInterface;
use OpenTelemetry\SDK\Trace\ReadableSpanInterface;
use OpenTelemetry\SDK\Common\Future\CancellationInterface;
class InstanaAttributes implements SpanProcessorInterface
{
    public function onStart(ReadWriteSpanInterface $span, ?ContextInterface $parentContext = null): void
    {
        // Add custom attributes to every single span
        $span->setAttribute('deployment.environment', getenv('ENVIRONMENT') ?: 'production');
        $span->setAttribute('host.name', gethostname());
        $span->setAttribute('instana.distro', 'otel-bootstrap');
    }

    // These methods are required by the interface but can be empty.
    public function onEnd(ReadableSpanInterface $span): void {}
    public function forceFlush(?CancellationInterface $cancellation = null): bool
    {
        // Your implementation logic here
        // Return true if successful, false otherwise
        return true;
    }

    /**
     * @inheritDoc
     */
    public function shutdown(?CancellationInterface $cancellation = null): bool
    {
        // Your implementation logic here
        // Return true if successful, false otherwise
        return true;
    }

}
