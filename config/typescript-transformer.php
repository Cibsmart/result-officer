<?php

declare(strict_types=1);

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Spatie\LaravelData\Support\TypeScriptTransformer\DataTypeScriptCollector;
use Spatie\LaravelTypeScriptTransformer\Transformers\DtoTransformer;
use Spatie\LaravelTypeScriptTransformer\Transformers\SpatieStateTransformer;
use Spatie\TypeScriptTransformer\Collectors\DefaultCollector;
use Spatie\TypeScriptTransformer\Collectors\EnumCollector;
use Spatie\TypeScriptTransformer\Transformers\EnumTransformer;
use Spatie\TypeScriptTransformer\Writers\TypeDefinitionWriter;

return [
    /*
     * The paths where typescript-transformer will look for PHP classes
     * to transform, this will be the `app` path by default.
     */

    'auto_discover_types' => [
        app_path(),
    ],

    /*
     * Collectors will search for classes in the `auto_discover_types` paths and choose the correct
     * transformer to transform them. By default, we include a DefaultCollector which will search
     * for @typescript annotated and #[TypeScript] attributed classes to transform.
     */

    'collectors' => [
        DefaultCollector::class,
        EnumCollector::class,
        DataTypeScriptCollector::class,
    ],

    /*
     * In your classes, you sometimes have types that should always be replaced
     * by the same TypeScript representations. For example, you can replace a
     * Datetime always with a string. You define these replacements here.
     */

    'default_type_replacements' => [
        Carbon::class => 'string',
        CarbonImmutable::class => 'string',
        CarbonInterface::class => 'string',
        DateTimeImmutable::class => 'string',
    ],

    /*
     * The generated TypeScript file can be formatted. We ship a Prettier formatter
     * out of the box: `PrettierFormatter` but you can also implement your own one.
     * The generated TypeScript will not be formatted when no formatter was set.
     */

    'formatter' => null,

    /*
     * The package will write the generated TypeScript to this file.
     */

    'output_file' => resource_path('js/types/generated.d.ts'),

    /*
     * Transformers take PHP classes(e.g., enums) as an input and will output
     * a TypeScript representation of the PHP class.
     */

    'transformers' => [
        SpatieStateTransformer::class,
        EnumTransformer::class,
        DtoTransformer::class,
    ],

    /*
     * Enums can be transformed into types or native TypeScript enums, by default
     * the package will transform them to types.
     */

    'transform_to_native_enums' => false,

    /*
     * When the package is writing types to the output file, a writer is used to
     * determine the format. By default, this is the `TypeDefinitionWriter`.
     * But you can also use the `ModuleWriter` or implement your own.
     */

    'writer' => TypeDefinitionWriter::class,
];
