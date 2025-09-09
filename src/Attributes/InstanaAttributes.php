<?php

namespace Heena\OpenTelemetry\Attributes;

use OpenTelemetry\SDK\Trace\SpanProcessorInterface;
use OpenTelemetry\SDK\Trace\ReadWriteSpanInterface;
use OpenTelemetry\SDK\Common\Attribute\Attributes;

class InstanaAttributes implements SpanProcessorInterface
{
    public function onStart(ReadWriteSpanInterface $span, Context $parentContext): void
    {
        // Add custom attributes to every single span
        $span->setAttribute('deployment.environment', getenv('ENVIRONMENT') ?: 'production');
        $span->setAttribute('host.name', gethostname());
        $span->setAttribute('instana.distro', 'otel-bootstrap');
    }

    // These methods are required by the interface but can be empty.
    public function onEnd(ReadWriteSpanInterface $span): void {}
    public function shutdown(): bool { return true; }
    public function forceFlush(): bool { return true; }
}
