<?php

namespace Vongola\Imgur\Api;

use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use Vongola\Imgur\Exceptions\MissingArgumentException;

use function in_array;

/**
 * CRUD for Images.
 * @package Vongola\Imgur\Api
 */
class Image extends BaseApi
{
    /**
     * Get information about an image.
     *
     * @param string $imageId
     * @return array (@see https://api.imgur.com/models/image)
     * @throws GuzzleException
     */
    public function image(string $imageId): array
    {
        return $this->requestGet("image/{$imageId}");
    }

    /**
     * Upload a new image.
     *
     * @param array $data
     * @return bool
     * @throws GuzzleException
     */
    public function upload(array $data): bool
    {
        if (!isset($data['image'])) {
            throw new MissingArgumentException(['image']);
        }

        $typeValues = ['file', 'base64', 'url'];
        if (isset($data['type']) && !in_array(strtolower($data['type']), $typeValues, true)) {
            throw new InvalidArgumentException(
                'Type parameter "' . $data['type'] . '" is wrong. Possible values are: ' . implode(', ', $typeValues)
            );
        }

        if ('file' === $data['type']) {
            $data['image'] = fopen($data['image'], 'r');
        }

        return $this->requestPost('image', $data);
    }

    /**
     * Deletes an image. For an anonymous image, $imageIdOrDeleteHash must be the image's deletehash.
     * If the image belongs to your account then passing the ID of the image is sufficient.
     *
     * @param string $imageIdOrDeleteHash
     * @return bool
     * @throws GuzzleException
     */
    public function delete(string $imageIdOrDeleteHash): bool
    {
        return $this->requestDelete("image/{$imageIdOrDeleteHash}");
    }

    /**
     * Updates the title or description of an image.
     * You can only update an image you own and is associated with your account.
     * For an anonymous image, {id} must be the image's deletehash.
     *
     * @param string $imageIdOrDeleteHash
     * @param array $data
     * @return bool
     * @throws GuzzleException
     */
    public function update(string $imageIdOrDeleteHash, array $data): bool
    {
        return $this->requestPost("image/{$imageIdOrDeleteHash}", $data);
    }

    /**
     * Favorite an image with the given ID. The user is required to be logged in to favorite the image.
     *
     * @param string $imageIdOrDeleteHash
     * @return bool
     * @throws GuzzleException
     */
    public function favorite(string $imageIdOrDeleteHash): bool
    {
        return $this->requestPost("image/{$imageIdOrDeleteHash}/favorite");
    }
}
