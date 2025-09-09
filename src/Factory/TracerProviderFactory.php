<?php

namespace Instana\OpenTelemetry\Factory;

use OpenTelemetry\API\Globals;
use OpenTelemetry\Context\Propagation\TextMapPropagatorInterface;
use OpenTelemetry\SDK\Common\Configuration\Configuration;
use OpenTelemetry\SDK\Common\Configuration\Variables;
use OpenTelemetry\SDK\Common\Export\Stream\StreamTransport;
use OpenTelemetry\SDK\Trace\SpanExporter\ConsoleSpanExporter;
use OpenTelemetry\SDK\Trace\SpanExporter\OtlpSpanExporter;
use OpenTelemetry\SDK\Trace\SpanProcessor\BatchSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;
use OpenTelemetry\SDK\Trace\TracerProviderBuilder;

class TracerProviderFactory
{
    public static function create(): TracerProvider
    {
        // Use the SDK's built-in configuration to read standard OTEL_* env vars
        $exporterType = Configuration::getString(Variables::OTEL_TRACES_EXPORTER);
        $spanExporter = self::createExporter($exporterType);

        // Build the provider with a batch processor (recommended for production)
  $tracerProvider = (new TracerProviderBuilder())
    ->addSpanProcessor(new InstanaAttributes()) // <-- Add custom attributes
    ->addSpanProcessor(new BatchSpanProcessor($spanExporter))
    ->build();
        // Register it as the global tracer provider
        Globals::setTracerProvider($tracerProvider);

        return $tracerProvider;
    }

    private static function createExporter(?string $exporterType): SpanExporterInterface
    {
        // Default to OTLP if not set, fallback to console for local dev
        switch ($exporterType) {
            case 'otlp':
                return new OtlpSpanExporter();
            case 'console':
                return new ConsoleSpanExporter(StreamTransport::create('php://stdout'));
            case 'none':
                return new NoopSpanExporter();
            default:
                // If OTEL_EXPORTER_OTLP_ENDPOINT is set, assume OTLP
                if (Configuration::getString(Variables::OTEL_EXPORTER_OTLP_ENDPOINT)) {
                    return new OtlpSpanExporter();
                }
                // Ultimate fallback: console
                return new ConsoleSpanExporter(StreamTransport::create('php://stdout'));
        }
    }
}
