<?php

use Heena\OpenTelemetry\Factory\TracerProviderFactory;

// Check if OpenTelemetry is enabled
if (getenv('OTEL_PHP_AUTO_ENABLE') !== 'false') {
    
    // 1. BOOTSTRAP THE SDK FIRST!
    // This creates the TracerProvider and registers it globally.
    $tracerProvider = TracerProviderFactory::create();
    $loggerProvider = LoggerProviderFactory::create(); // You would create this
   //$meterProvider = MeterProviderFactory::create(); // And this    
    // 2. Optional but recommended: Initialize other components
    // For example, set a global LoggerProvider or MeterProvider if you have them.
    // LoggerProviderFactory::create();
    // MeterProviderFactory::create();

    // 3. MOST IMPORTANT PART: Manually run the instrumentation initializers.
    // Some instrumentation packages have static initializers that need to be called.
    // This ensures their hooks are registered immediately.

    // Example: If you installed PSR-18 (HTTP Client) instrumentation
    if (class_exists(\OpenTelemetry\Instrumentation\Psr18\Instrumentation::class)) {
        \OpenTelemetry\Instrumentation\Psr18\Instrumentation::register();
    }

    // Example: If you installed PSR-15 (Middleware) instrumentation
    if (class_exists(\OpenTelemetry\Instrumentation\Psr15\Instrumentation::class)) {
        \OpenTelemetry\Instrumentation\Psr15\Instrumentation::register();
    }

    // Example: If you installed Guzzle instrumentation (note: it might use the PSR-18 hook)
    if (class_exists(\OpenTelemetry\Instrumentation\Guzzle\Instrumentation::class)) {
        \OpenTelemetry\Instrumentation\Guzzle\Instrumentation::register();
    }

    // 4. Log a message for clarity during development
    if (getenv('APP_ENV') === 'dev') {
        error_log('YourCompany OpenTelemetry Bootstrapper: SDK and auto-instrumentation enabled.');
    }
}
