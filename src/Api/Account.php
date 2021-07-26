<?php

namespace Vongola\Imgur\Api;

use GuzzleHttp\Exception\GuzzleException;

/**
 * CRUD for Accounts.
 * @package Vongola\Imgur\Api
 */
class Account extends BaseApi
{
    /**
     * Request standard user information.
     *
     * @param string $username
     * @return array Account (@see https://api.imgur.com/models/account)
     * @throws GuzzleException
     *
     */
    public function base(string $username = 'me'): array
    {
        return $this->requestGet("account/{$username}");
    }

    /**
     * Delete a user account, you can only access this if you're logged in as the user.
     *
     * @param string $username
     * @return bool
     * @throws GuzzleException
     */
    public function delete(string $username): bool
    {
        return $this->requestDelete("account/{$username}");
    }

    /**
     * Return the images the user has favorited in the gallery.
     *
     * @param string $username
     * @param int $page
     * @param string $sort 'oldest', or 'newest'. Defaults to 'newest'
     * @return array Gallery Image (@see https://api.imgur.com/models/gallery_image) OR
     *               Gallery Album (@see https://api.imgur.com/models/gallery_album)
     * @throws GuzzleException
     *
     */
    public function galleryFavorites(string $username = 'me', int $page = 0, string $sort = 'newest'): array
    {
        $this->validateSortArgument($sort, ['oldest', 'newest']);

        return $this->requestGet("account/{$username}/gallery_favorites/{$page}/{$sort}");
    }

    /**
     * Returns the users favorited images, only accessible if you're logged in as the user.
     *
     * @param string $username
     * @return array Gallery Image (@see https://api.imgur.com/models/gallery_image) OR
     *               Gallery Album (@see https://api.imgur.com/models/gallery_album)
     * @throws GuzzleException
     *
     */
    public function favorites(string $username = 'me'): array
    {
        return $this->requestGet("account/{$username}/favorites");
    }

    /**
     * Return the images a user has submitted to the gallery.
     *
     * @param string $username
     * @param int $page
     * @return array Gallery Image (@see https://api.imgur.com/models/gallery_image) OR
     *               Gallery Album (@see https://api.imgur.com/models/gallery_album)
     * @throws GuzzleException
     *
     */
    public function submissions(string $username = 'me', int $page = 0): array
    {
        return $this->requestGet("account/{$username}/submissions/{$page}");
    }

    /**
     * Returns the account settings, only accessible if you're logged in as the user.
     *
     * @param string $username
     * @return array Account Settings (@see https://api.imgur.com/models/account_settings)
     * @throws GuzzleException
     *
     */
    public function settings(string $username = 'me'): array
    {
        return $this->requestGet("account/{$username}/settings");
    }

    /**
     * Updates the account settings for a given user, the user must be logged in.
     *
     * @param array $parameters
     * @return bool
     * @throws GuzzleException
     *
     */
    public function changeAccountSettings(array $parameters): bool
    {
        return $this->requestPost('account/me/settings', $parameters);
    }

    /**
     * Returns the totals for the gallery profile.
     *
     * @param string $username
     * @return array Gallery Profile (@see https://api.imgur.com/models/gallery_profile)
     * @throws GuzzleException
     *
     */
    public function accountGalleryProfile(string $username = 'me'): array
    {
        return $this->requestGet("account/{$username}/gallery_profile");
    }

    /**
     * Checks to see if user has verified their email address.
     *
     * @param string $username
     * @return bool
     * @throws GuzzleException
     *
     */
    public function verifyUsersEmail(string $username = 'me'): bool
    {
        return $this->requestGet("account/{$username}/verifyemail");
    }

    /**
     * Sends an email to the user to verify that their email is valid to upload to gallery.
     * Must be logged in as the user to send.
     *
     * @param string $username
     * @return bool
     * @throws GuzzleException
     *
     */
    public function sendVerificationEmail(string $username = 'me'): bool
    {
        return $this->requestPost("account/{$username}/verifyemail");
    }

    /**
     * Get all the albums associated with the account. Must be logged in as the user to see secret and hidden albums.
     *
     * @param string $username
     * @param int $page
     * @return array Array of Album (@see https://api.imgur.com/models/album)
     * @throws GuzzleException
     *
     */
    public function albums(string $username = 'me', int $page = 0): array
    {
        return $this->requestGet("account/{$username}/albums/{$page}");
    }

    /**
     * Get additional information about an album, this endpoint works the same as the Album Endpoint.
     * You can also use any of the additional routes that are used on an album in the album endpoint.
     *
     * @param string $username
     * @param string $albumId
     * @return array Album (@see https://api.imgur.com/models/album)
     * @throws GuzzleException
     *
     */
    public function album(string $albumId, string $username = 'me'): array
    {
        return $this->requestGet("account/{$username}/album/{$albumId}");
    }

    /**
     * Return an array of all of the album IDs.
     *
     * @param string $username
     * @param int $page
     * @return array<int>
     * @throws GuzzleException
     *
     */
    public function albumIds(string $username = 'me', int $page = 0): array
    {
        return $this->requestGet("account/{$username}/albums/ids/{$page}");
    }

    /**
     * Return the total number of albums associated with the account.
     *
     * @param string $username
     * @return int
     * @throws GuzzleException
     *
     */
    public function albumCount(string $username = 'me'): int
    {
        return $this->requestGet("account/{$username}/albums/count");
    }

    /**
     * Delete an Album with a given id.
     *
     * @param string $username
     * @param string $albumId
     * @return bool
     * @throws GuzzleException
     *
     */
    public function albumDelete(string $albumId, string $username = 'me'): bool
    {
        return $this->requestDelete("account/{$username}/album/{$albumId}");
    }

    /**
     * Return the comments the user has created.
     *
     * @param string $username
     * @param int $page
     * @param string $sort 'best', 'worst', 'oldest', or 'newest'. Defaults to 'newest'
     * @return array Array of Comment (@see https://api.imgur.com/models/comment)
     * @throws GuzzleException
     *
     */
    public function comments(string $username = 'me', int $page = 0, string $sort = 'newest'): array
    {
        $this->validateSortArgument($sort, ['best', 'worst', 'oldest', 'newest']);

        return $this->requestGet("account/{$username}/comments/{$sort}/{$page}");
    }

    /**
     * Return information about a specific comment. This endpoint works the same as the Comment Endpoint.
     * You can use any of the additional actions that the comment endpoint allows on this end point.
     *
     * @param string $commentId
     * @param string $username
     * @return array Comment (@see https://api.imgur.com/models/comment)
     * @throws GuzzleException
     *
     */
    public function comment(string $commentId, string $username = 'me'): array
    {
        return $this->requestGet("account/{$username}/comment/{$commentId}");
    }

    /**
     * Return an array of all of the comment IDs.
     *
     * @param string $username
     * @param int $page
     * @param string $sort 'best', 'worst', 'oldest', or 'newest'. Defaults to 'newest'
     * @return array<int>
     * @throws GuzzleException
     *
     */
    public function commentIds(string $username = 'me', int $page = 0, string $sort = 'newest'): array
    {
        $this->validateSortArgument($sort, ['best', 'worst', 'oldest', 'newest']);

        return $this->requestGet("account/{$username}/comments/ids/{$sort}/{$page}");
    }

    /**
     * Return a count of all of the comments associated with the account.
     *
     * @param string $username
     * @return int
     * @throws GuzzleException
     *
     */
    public function commentCount(string $username = 'me'): int
    {
        return $this->requestGet("account/{$username}/comments/count");
    }

    /**
     * Delete a comment. You are required to be logged in as the user whom created the comment.
     *
     * @param string $commentId
     * @param string $username
     * @return bool
     * @throws GuzzleException
     *
     */
    public function commentDelete(string $commentId, string $username = 'me'): bool
    {
        return $this->requestDelete("account/{$username}/comment/{$commentId}");
    }

    /**
     * Return all of the images associated with the account.
     * You can page through the images by setting the page, this defaults to 0.
     *
     * @param string $username
     * @param int $page
     * @return array Array of Image (@see https://api.imgur.com/models/image)
     * @throws GuzzleException
     *
     */
    public function images(string $username = 'me', int $page = 0): array
    {
        return $this->requestGet("account/{$username}/images/{$page}");
    }

    /**
     * Return information about a specific image.
     * This endpoint works the same as the Image Endpoint.
     * You can use any of the additional actions that the image endpoint with this endpoint.
     *
     * @param string $imageId
     * @param string $username
     * @return array Image (@see https://api.imgur.com/models/image)
     * @throws GuzzleException
     *
     */
    public function image(string $imageId, string $username = 'me'): array
    {
        return $this->requestGet("account/{$username}/image/{$imageId}");
    }

    /**
     * Returns an array of Image IDs that are associated with the account.
     *
     * @param string $username
     * @param int $page
     * @return array<int>
     * @throws GuzzleException
     *
     */
    public function imageIds(string $username = 'me', int $page = 0): array
    {
        return $this->requestGet("account/{$username}/images/ids/{$page}");
    }

    /**
     * Returns the total number of images associated with the account.
     *
     * @param string $username
     * @return int
     * @throws GuzzleException
     *
     */
    public function imageCount(string $username = 'me'): int
    {
        return $this->requestGet("account/{$username}/images/count");
    }

    /**
     * Deletes an Image. This requires a delete hash rather than an ID.
     *
     * @param string $deleteHash
     * @param string $username
     * @return bool
     * @throws GuzzleException
     *
     */
    public function imageDelete(string $deleteHash, string $username = 'me'): bool
    {
        return $this->requestDelete("account/{$username}/image/{$deleteHash}");
    }

    /**
     * Returns all of the reply notifications for the user. Required to be logged in as that user.
     *
     * @param string $username
     * @param bool $onlyNew
     * @return array Array of Notification (@see https://api.imgur.com/models/notification)
     * @throws GuzzleException
     *
     */
    public function replies(string $username = 'me', bool $onlyNew = false): array
    {
        return $this->requestGet("account/{$username}/notifications/replies", ['new' => $onlyNew]);
    }
}
