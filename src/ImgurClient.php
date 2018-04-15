<?php

namespace Vongola\Imgur;



use Illuminate\Http\UploadedFile;
use Vongola\Imgur\Exceptions\ImgurException;

class ImgurClient
{
    private $client;

    /**
     * ImgurClient constructor.
     * @param string $client_id
     * @param string $client_secret
     */
    public function __construct($client_id, $client_secret)
    {
        $this->client = new \Imgur\Client();
        $this->client->setOption('client_id', $client_id);
        $this->client->setOption('client_secret', $client_secret);
    }

    /**
     * Upload Image
     * @param UploadedFile|string $image
     * @return bool|ImgurImage
     * @throws ImgurException
     */
    public function upload($image) {
        if ($image instanceof UploadedFile) {
            $imageData = [
                'image' => $image->getRealPath(),
                'type'  => 'file'
            ];
        } else {
            if (filter_var($image, FILTER_VALIDATE_URL)) {
                $imageData = [
                    'image' => $image,
                    'type'  => 'url'
                ];
            } else {
                // This should not happen!!
                throw new ImgurException('Can not upload image without url or file.');
            }
        }
        $res = $this->client->api('image')->upload($imageData);
        if ($res['success'] === true) {
            return new ImgurImage($res['data']);
        } else {
            return false;
        }
    }

    /**
     * Delete Image by Hash
     * @param $hash
     * @return bool
     */
    public function delete($hash)
    {
        $res = $this->client->api('image')->deleteImage($hash);
        return $this->is_success($res);
    }

    /**
     * Check Return status
     * @param array $res
     * @return bool
     */
    public function is_success($res)
    {
        return $res['success'] === true;
    }

}