<?php

namespace frappacchio\DOSpaces;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\Filesystem as Flysystem;

/**
 * Class Filesystem
 *
 *
 * @property \League\Flysystem\Filesystem $fileSystem
 * @property string $key
 * @property string $secret
 * @property string $endpoint
 * @property string $container
 * @property string $storagePath
 * @property string $storageFileOnly
 * @property string $storageFileDelete
 * @property string $filter
 * @property string $uploadUrlPath
 * @property string $uploadPath
 * @property bool $fileVisibility
 */
class Filesystem
{
    /**
     * @var Flysystem
     */
    public $fileSystem;

    /**
     * @var string
     */
    public $key;

    /**
     * @var string
     */
    public $secret;

    /**
     * @var string
     */
    public $endpoint;

    /**
     * @var string
     */
    public $container;

    /**
     * @var string
     */
    public $storagePath;

    /**
     * @var string
     */
    public $filter;

    /**
     * @var string
     */
    public $fileVisibility = 'public';

    /**
     * Filesystem constructor.
     *
     * @param  string  $key
     * @param  string  $secret
     * @param  string  $container
     * @param  string  $endpoint
     * @param  string  $storagePath
     * @param  string  $filter
     */
    public function __construct(
        $key,
        $secret,
        $container,
        $endpoint,
        $storagePath,
        $filter
    ) {
        $this->key = $key;
        $this->secret = $secret;
        $this->endpoint = $endpoint;
        $this->container = $container;
        $this->storagePath = $storagePath;
        $this->filter = $filter;

        $client = new S3Client([
            'credentials' => [
                'key' => $this->key,
                'secret' => $this->secret,
            ],
            'region' => 'your-region',
            'version' => 'latest',
            'endpoint' => $this->endpoint,
        ]);

        $adapter = new AwsS3V3Adapter($client, $this->container);
        $this->fileSystem = new Flysystem($adapter);
    }

    /**
     * @param  string  $fileName
     * @return bool
     */
    public function upload($fileUpload, $fileName = '')
    {
        if (empty($fileName)) {
            $fileName = $fileUpload;
        }
        if (! empty($fileName)) {
            if (! empty($this->filter) && ($this->filter !== '*' && ! preg_match($this->filter, $fileName))) {
                return false;
            }
            $uploadResult = $this->fileSystem->write($fileName, file_get_contents($fileUpload), [
                'visibility' => $this->fileVisibility,
            ]);

            return $uploadResult;
        } else {
            return false;
        }
    }

    public function exists($file)
    {
        // Implement the exists method
    }

    /**
     * @return bool
     */
    public function delete($file)
    {
        try {
            return $this->fileSystem->delete($file);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function testConnection()
    {
        try {
            $settings = new \frappacchio\DOSWordpress\PluginSettings();
            $upload = $settings->get('dos_storage_path').'/test.txt';
            $url = $settings->get('upload_url_path').'/test.txt';

            $this->fileSystem->write($upload, 'test', ['visibility' => 'public']);

            $headers = @get_headers($url);

            if ($headers && strpos($headers[0], '200') !== false) {
                $this->fileSystem->delete('test.txt');

                return true;
            } else {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @return bool|\League\Flysystem\Filesystem|null
     */
    public function __get($name)
    {
        if ($name === 'fileSystem' && empty($this->fileSystem) && ! empty($this->key) && ! empty($this->container) && ! empty($this->endpoint)) {
            return $this->fileSystem = Space::getInstance($this->key, $this->secret, $this->container,
                $this->endpoint);
        } elseif ($name === 'fileSystem') {
            return false;
        }

        return ! empty($this->$name) ? $this->$name : null;
    }
}
