<?php

/**
 * Configuration for enforcing unique attributes in models.
 *
 * This configuration defines the default values and behavior
 * for generating unique attribute values.
 */
return [

    /**
     * The prefix to add to the beginning of the generated unique value.
     */
    'prefix' => '',

    /**
     * The suffix to add to the end of the generated unique value.
     */
    'suffix' => '',

    /**
     * The separator to use between prefix, value, and suffix.
     */
    'separator' => '_',

    /**
     * The maximum number of retries to generate a unique value before increasing the length.
     */
    'max_retries' => 100,

    /**
     * The default length of the generated unique value.
     */
    'length' => 8,

    /**
     * The format of the unique value.
     *
     * Supported options:
     * - `numerify` - Replace `#` with random digits.
     * - `lexify` - Replace `?` with random letters.
     * - `bothify` - Replace `#` with digits and `?` with letters.
     *
     * @see https://github.com/cndrsdrmn/php-string-formatter
     */
    'format' => 'bothify',
];
