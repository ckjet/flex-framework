<?php

namespace Hypilon\Composer;

class Handler
{
    public static function checkDirectoryStructure()
    {
        $root = str_replace('/vendor/hypilon/framework/Composer', '', dirname(__FILE__));
        $directories = [
            '/app', '/app/Config', '/app/Controller', '/app/DependencyInjection',
            '/app/Model', '/app/Resources', '/app/Resources/public', '/app/Resources/view',
            '/app/Routing', '/app/Service', '/web'
        ];
        foreach($directories as $directory) {
            $new_directory = $root . $directory;
            if(!is_dir($new_directory)) {
                echo "Creating {$new_directory}\n";
                mkdir($new_directory);
            }
        }
        if(!file_exists($root . '/web/assets')) {
            echo "Creating symlink for {$root}/web/assets\n";
            symlink($root . '/app/Resources/public', $root . '/web/assets');
        }
    }
}