<?php

namespace Vongola\Imgur\Api;

use GuzzleHttp\Exception\GuzzleException;

/**
 * CRUD for Albums.
 * @package Vongola\Imgur\Api
 */
class Album extends BaseApi
{
    /**
     * Get information about a specific album.
     *
     * @param string $albumId
     * @return array Album (@see https://api.imgur.com/models/album)
     * @throws GuzzleException
     *
     */
    public function album(string $albumId): array
    {
        return $this->requestGet("album/{$albumId}");
    }

    /**
     * Return all of the images in the album.
     *
     * @param string $albumId
     * @return array Array of Image (@see https://api.imgur.com/models/image)
     * @throws GuzzleException
     *
     */
    public function albumImages(string $albumId): array
    {
        return $this->requestGet("album/{$albumId}/images");
    }

    /**
     * Get information about an image in an album, any additional actions found in Image Endpoint will also work.
     *
     * @param string $albumId
     * @param string $imageId
     * @return array Image (@see https://api.imgur.com/models/image)
     * @throws GuzzleException
     *
     */
    public function albumImage(string $albumId, string $imageId): array
    {
        return $this->requestGet("album/{$albumId}/image/{$imageId}");
    }

    /**
     * Create a new album.
     * Optional parameter of ids[] is an array of image ids to add to the album.
     * (if you're authenticated with an account)
     * This method is available without authenticating an account,
     * and may be used merely by sending "Authorization: Client-ID {client_id}"
     * in the request headers. Doing so will create an anonymous album which is not tied to an account.
     * Adding images to an anonymous album is only available during image uploading.
     *
     * @param array $data
     * @return bool
     * @throws GuzzleException
     *
     */
    public function create(array $data): bool
    {
        return $this->requestPost('album', $data);
    }

    /**
     * Update the information of an album.
     * For anonymous albums, {album} should be the deletehash that is returned at creation.
     *
     * @param string $deleteHashOrAlbumId
     * @param array $data
     * @return bool
     * @throws GuzzleException
     *
     */
    public function update(string $deleteHashOrAlbumId, array $data): bool
    {
        return $this->requestPost("album/{$deleteHashOrAlbumId}", $data);
    }

    /**
     * Delete an album with a given ID. You are required to be logged in as the user to delete the album.
     * Takes parameter, ids[], as an array of ids and removes from the album.
     * For anonymous albums, {album} should be the "deleteHash" that is returned at creation.
     *
     * @param string $deleteHashOrAlbumId
     * @return bool
     * @throws GuzzleException
     *
     */
    public function deleteAlbum(string $deleteHashOrAlbumId): bool
    {
        return $this->requestDelete("album/{$deleteHashOrAlbumId}");
    }

    /**
     * Favorite an album with a given ID. The user is required to be logged in to favorite the album.
     *
     * @param string $albumId
     * @return bool
     * @throws GuzzleException
     *
     */
    public function favoriteAlbum(string $albumId): bool
    {
        return $this->requestPost("album/{$albumId}/favorite");
    }

    /**
     * Sets the images for an album, removes all other images and only uses the images in this request.
     * (Not available for anonymous albums.).
     *
     * @param string $albumId
     * @param array $imageIds
     * @return bool
     * @throws GuzzleException
     */
    public function setAlbumImages(string $albumId, array $imageIds): bool
    {
        return $this->requestPost("album/{$albumId}", ['ids' => implode(',', $imageIds)]);
    }

    /**
     * Takes parameter, ids[], as an array of ids to add to the album.
     * (Not available for anonymous albums.
     * Adding images to an anonymous album is only available during image uploading.).
     *
     * @param string $albumId
     * @param array $imageIds
     * @return bool
     * @throws GuzzleException
     */
    public function addImages(string $albumId, array $imageIds): bool
    {
        return $this->requestPost("album/{$albumId}/add", ['ids' => implode(',', $imageIds)]);
    }

    /**
     * Takes parameter, ids[], as an array of ids and removes from the album.
     * For anonymous albums, $deletehashOrAlbumId should be the deletehash that is returned at creation.
     *
     * @param string $deleteHashOrAlbumId
     * @param array $imageIds
     * @return bool
     * @throws GuzzleException
     */
    public function removeImages(string $deleteHashOrAlbumId, array $imageIds): bool
    {
        return $this->requestDelete(
            "album/{$deleteHashOrAlbumId}/remove_images",
            [
                'ids' => implode(',', $imageIds),
            ]
        );
    }
}
