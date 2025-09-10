<?php

namespace Heena\OpenTelemetry\Factory;

use OpenTelemetry\API\Globals;
use OpenTelemetry\Context\Propagation\TextMapPropagatorInterface;
use OpenTelemetry\SDK\Common\Configuration\Configuration;
use OpenTelemetry\SDK\Common\Configuration\Variables;
use OpenTelemetry\SDK\Common\Export\Stream\StreamTransport;
use OpenTelemetry\SDK\Trace\SpanExporter\ConsoleSpanExporter;
use OpenTelemetry\Contrib\Otlp\SpanExporter;
use OpenTelemetry\SDK\Trace\SpanProcessor\BatchSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;
use OpenTelemetry\SDK\Trace\TracerProviderBuilder;
use Heena\OpenTelemetry\Attributes\InstanaAttributes;
class TracerProviderFactory
{
    public static function create(): TracerProvider
    {
        // Use the SDK's built-in configuration to read standard OTEL_* env vars
        $spanExporter = self::createExporter();

        // Build the provider with a batch processor (recommended for production)
$tracerProvider = TracerProvider::builder()
   ->addSpanProcessor(new InstanaAttributes()) // <-- Add custom attributes
	    ->build();
   /* $tracerProvider = (new TracerProviderBuilder())
    ->addSpanProcessor(new InstanaAttributes()) // <-- Add custom attributes
    ->addSpanProcessor(new BatchSpanProcessor($spanExporter))
    ->build();
     */   // Register it as the global tracer provider

        return $tracerProvider;
    }

    private static function createExporter(?string $exporterType = null): SpanExporter
    {
	        $exporterType = $exporterType ?? 'otlp';

        // Default to OTLP if not set, fallback to console for local dev
        switch ($exporterType) {
	case 'otlp':
		$transport = (new \OpenTelemetry\Contrib\Otlp\OtlpHttpTransportFactory())->create('http://localhost:4318', 'application/json');
                return new SpanExporter($transport);
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
