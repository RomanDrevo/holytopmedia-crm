<?php

use Illuminate\Support\HtmlString;

if (! function_exists('mix')) {
    /**
     * Get the path to a versioned Mix file.
     *
     * @param  string  $path
     * @param  string  $manifestDirectory
     * @return \Illuminate\Support\HtmlString
     *
     * @throws \Exception
     */
    function mix($path, $manifestDirectory = '')
    {
        static $manifest;

        if ($manifestDirectory && ! starts_with($manifestDirectory, '/')) {
            $manifestDirectory = "/{$manifestDirectory}";
        }

        if (! $manifest) {
            if (! file_exists($manifestPath = public_path($manifestDirectory.'/mix-manifest.json'))) {
                throw new Exception('The Mix manifest does not exist.');
            }

            $manifest = json_decode(file_get_contents($manifestPath), true);
        }

        if (! starts_with($path, '/')) {
            $path = "/{$path}";
        }

        if (! array_key_exists($path, $manifest)) {
            throw new Exception(
                "Unable to locate Mix file: {$path}. Please check your ".
                'webpack.mix.js output paths and try again.'
            );
        }

        return file_exists(public_path($manifestDirectory.'/hot'))
                    ? new HtmlString("http://localhost:8080{$manifest[$path]}")
                    : new HtmlString($manifestDirectory.$manifest[$path]);
    }
}