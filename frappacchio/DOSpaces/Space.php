<?php

namespace frappacchio\DOSpaces;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\Filesystem as FS;

/**
 * Class Space
 *
 * Create e Space instance in order to connect to DOSpaces
 */
class Space
{
    /**
     * @param  string  $key  container key
     * @param  string  $secret  container secret
     * @param  string  $container  container name
     * @param  string  $endpoint  container endpoint (including scheme)
     * @return FS
     */
    public static function getInstance($key, $secret, $container, $endpoint)
    {
        $client = new S3Client([
            'credentials' => [
                'key' => $key,
                'secret' => $secret,
            ],
            'bucket' => 'do-spaces',
            'endpoint' => $endpoint,
            'version' => 'latest',
            'region' => 'us-east-1',
        ]);

        $connection = new AwsS3V3Adapter($client, $container);
        $filesystem = new FS($connection);

        return $filesystem;
    }
}
