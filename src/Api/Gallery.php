<?php

namespace Vongola\Imgur\Api;

use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use Vongola\Imgur\Exceptions\MissingArgumentException;

use function in_array;

/**
 * CRUD for Gallery.
 * @package Vongola\Imgur\Api
 */
class Gallery extends BaseApi
{
    /**
     * Returns the images in the gallery.
     * For example the main gallery is https://api.imgur.com/3/gallery/hot/viral/0.json.
     *
     * @param string $section (hot | top | user)
     * @param string $sort (viral | top | time | rising)
     * @param int $page
     * @param string $window (day | week | month | year | all)
     * @param bool $showViral
     * @return array Gallery Image (@see https://api.imgur.com/models/gallery_image) OR
     *               Gallery Album (@throws GuzzleException
     * @see https://api.imgur.com/models/gallery_album)
     */
    public function gallery(
        string $section = 'hot',
        string $sort = 'viral',
        int $page = 0,
        string $window = 'day',
        bool $showViral = true
    ): array {
        $this->validateSortArgument($sort, ['viral', 'top', 'time', 'rising']);
        $this->validateWindowArgument($window, ['day', 'week', 'month', 'year', 'all']);

        $sectionValues = ['hot', 'top', 'user'];
        if (!in_array($section, $sectionValues, true)) {
            throw new InvalidArgumentException(
                'Section parameter "' . $section . '" is wrong. Possible values are: ' . implode(', ', $sectionValues)
            );
        }

        $showViral = $showViral ? 'true' : 'false';

        return $this->requestGet("gallery/{$section}/{$sort}/{$window}/{$page}", ['showViral' => $showViral]);
    }

    /**
     * View gallery images for a sub-reddit.
     *
     * @param string $subreddit (e.g pics - A valid sub-reddit name)
     * @param string $sort (top | time)
     * @param int $page
     * @param string $window (day | week | month | year | all)
     * @return array Gallery Image (@see https://api.imgur.com/models/gallery_image)
     * @throws GuzzleException
     *
     */
    public function subredditGalleries(
        string $subreddit,
        string $sort = 'time',
        int $page = 0,
        string $window = 'day'
    ): array {
        $this->validateSortArgument($sort, ['top', 'time']);
        $this->validateWindowArgument($window, ['day', 'week', 'month', 'year', 'all']);

        return $this->requestGet("gallery/r/{$subreddit}/{$sort}/{$window}/{$page}");
    }

    /**
     * View a single image in the subreddit.
     *
     * @param string $subreddit (e.g pics - A valid sub-reddit name)
     * @param string $imageId
     * @return array Gallery Image (@see https://api.imgur.com/models/gallery_image)
     * @throws GuzzleException
     *
     */
    public function subredditImage(string $subreddit, string $imageId): array
    {
        return $this->requestGet("gallery/r/{$subreddit}/{$imageId}");
    }

    /**
     * View images for a gallery tag.
     *
     * @param string $name The name of the tag
     * @param string $sort (top | time | viral)
     * @param int $page
     * @param string $window (day | week | month | year | all)
     * @return array Tag (@see https://api.imgur.com/models/tag)
     * @throws GuzzleException
     *
     */
    public function galleryTag(string $name, string $sort = 'viral', int $page = 0, string $window = 'week'): array
    {
        $this->validateSortArgument($sort, ['top', 'time', 'viral']);
        $this->validateWindowArgument($window, ['day', 'week', 'month', 'year', 'all']);

        return $this->requestGet("gallery/t/{$name}/{$sort}/{$window}/{$page}");
    }

    /**
     * View a single image in a gallery tag.
     *
     * @param string $name The name of the tag
     * @param string $imageId The ID for the image
     * @return array Gallery Image (@see https://api.imgur.com/models/gallery_image)
     * @throws GuzzleException
     *
     */
    public function galleryTagImage(string $name, string $imageId): array
    {
        return $this->requestGet("gallery/t/{$name}/{$imageId}");
    }

    /**
     * View tags for a gallery item.
     *
     * @param string $imageOrAlbumId ID of the gallery item
     * @return array of Tag Votes (@see https://api.imgur.com/models/tag_vote)
     * @throws GuzzleException
     *
     */
    public function galleryItemTags(string $imageOrAlbumId): array
    {
        return $this->requestGet("gallery/{$imageOrAlbumId}/tags");
    }

    /**
     * Vote for a tag, 'up' or 'down' vote. Send the same value again to undo a vote.
     *
     * @param string $id ID of the gallery item
     * @param string $name Name of the tag (implicitly created, if doesn't exist)
     * @param string $vote 'up' or 'down'
     * @return bool
     * @throws GuzzleException
     *
     */
    public function galleryVoteTag(string $id, string $name, string $vote): bool
    {
        $this->validateVoteArgument($vote, ['up', 'down']);
        return $this->requestPost("gallery/{$id}/vote/tag/{$name}/{$vote}");
    }

    /**
     * Search the gallery with a given query string.
     *
     * @param string $query Query string (note: if advanced search parameters are set, this query string is ignored).
     *                      This parameter also supports boolean operators (AND, OR, NOT) and indices
     *                      (tag: user: title: ext: subreddit: album: meme:).
     *                      An example compound query would be 'title: cats AND dogs ext: gif'
     * @param string $sort (time | viral | top)
     * @param int $page
     * @return array Gallery Image (@see https://api.imgur.com/models/gallery_image) OR
     *               Gallery Album (@throws GuzzleException
     *
     * @see https://api.imgur.com/models/gallery_album)
     */
    public function search(string $query, string $sort = 'time', int $page = 0): array
    {
        $this->validateSortArgument($sort, ['viral', 'top', 'time']);

        return $this->requestGet("gallery/search/{$sort}/{$page}", ['q' => $query]);
    }

    /**
     * Returns a random set of gallery images.
     *
     * @param int $page
     * @return array Gallery Image (@see https://api.imgur.com/models/gallery_image) OR
     *               Gallery Album (@throws GuzzleException
     * @see https://api.imgur.com/models/gallery_album)
     */
    public function randomGalleryImages(int $page = 0): array
    {
        return $this->requestGet("gallery/random/random/{$page}");
    }

    /**
     * Share an Album or Image to the Gallery.
     *
     * @param string $imageOrAlbumId
     * @param array $data
     * @return bool
     *
     * @throws GuzzleException
     */
    public function submitToGallery(string $imageOrAlbumId, array $data): bool
    {
        if (!isset($data['title'])) {
            throw new MissingArgumentException('title');
        }

        return $this->requestPost("gallery/{$imageOrAlbumId}", $data);
    }

    /**
     * Remove an image from the gallery. You must be logged in as the owner of the item to do this action.
     *
     * @param string $imageOrAlbumId
     * @return bool
     * @throws GuzzleException
     *
     */
    public function removeFromGallery(string $imageOrAlbumId): bool
    {
        return $this->requestDelete("gallery/{$imageOrAlbumId}");
    }

    /**
     * Get additional information about an album in the gallery.
     *
     * @param string $albumId
     * @return array Gallery Album (@see https://api.imgur.com/models/gallery_album)
     * @throws GuzzleException
     */
    public function album(string $albumId): array
    {
        return $this->requestGet("gallery/album/{$albumId}");
    }

    /**
     * Get additional information about an image in the gallery.
     *
     * @param string $imageId
     * @return array Gallery Image (@see https://api.imgur.com/models/gallery_image)
     * @throws GuzzleException
     */
    public function image(string $imageId): array
    {
        return $this->requestGet("gallery/image/{$imageId}");
    }

    /**
     * Report an Image in the gallery.
     *
     * @param string $imageOrAlbumId
     * @return bool
     * @throws GuzzleException
     *
     */
    public function report(string $imageOrAlbumId): bool
    {
        return $this->requestPost("gallery/{$imageOrAlbumId}/report");
    }

    /**
     * Get the vote information about an image or album.
     *
     * @param string $imageOrAlbumId
     * @return array Vote (@see https://api.imgur.com/models/vote)
     * @throws GuzzleException
     *
     */
    public function votes(string $imageOrAlbumId): array
    {
        return $this->requestGet("gallery/{$imageOrAlbumId}/votes");
    }

    /**
     * Vote for an image, 'up' or 'down' vote. Send 'veto' to undo a vote.
     *
     * @param string $imageOrAlbumId
     * @param string $vote (up | down | veto)
     * @return bool
     * @throws GuzzleException
     *
     */
    public function vote(string $imageOrAlbumId, string $vote): bool
    {
        $this->validateVoteArgument($vote, ['up', 'down', 'veto']);

        return $this->requestPost("gallery/{$imageOrAlbumId}/vote/{$vote}");
    }

    /**
     * Retrieve comments on an image or album in the gallery.
     *
     * @param string $imageOrAlbumId
     * @param string $sort (best | top | new)
     * @return array Array of Comment (@see https://api.imgur.com/endpoints/gallery#gallery-comments)
     * @throws GuzzleException
     *
     */
    public function comments(string $imageOrAlbumId, string $sort = 'best'): array
    {
        $this->validateSortArgument($sort, ['best', 'top', 'new']);

        return $this->requestGet("gallery/{$imageOrAlbumId}/comments/{$sort}");
    }

    /**
     * Information about a specific comment.
     *
     * @param string $imageOrAlbumId
     * @param string $commentId
     * @return array Comment (@see https://api.imgur.com/endpoints/gallery#gallery-comments)
     * @throws GuzzleException
     *
     */
    public function comment(string $imageOrAlbumId, string $commentId): array
    {
        return $this->requestGet("gallery/{$imageOrAlbumId}/comment/{$commentId}");
    }

    /**
     * Create a comment for an image/album.
     *
     * @param string $imageOrAlbumId
     * @param array $data
     * @return bool
     * @throws GuzzleException
     *
     */
    public function createComment(string $imageOrAlbumId, array $data): bool
    {
        if (!isset($data['comment'])) {
            throw new MissingArgumentException('comment');
        }

        return $this->requestPost("gallery/{$imageOrAlbumId}/comment", $data);
    }

    /**
     * Reply to a comment that has been created for an image.
     *
     * @param string $imageOrAlbumId
     * @param string $commentId
     * @param array $data
     * @return bool
     * @throws GuzzleException
     *
     */
    public function createReply(string $imageOrAlbumId, string $commentId, array $data): bool
    {
        if (!isset($data['comment'])) {
            throw new MissingArgumentException('comment');
        }

        return $this->requestPost("gallery/{$imageOrAlbumId}/comment/{$commentId}", $data);
    }

    /**
     * List all of the IDs for the comments on an image/album.
     *
     * @param string $imageOrAlbumId
     * @return array<int>
     * @throws GuzzleException
     *
     */
    public function commentIds(string $imageOrAlbumId): array
    {
        return $this->requestGet("gallery/{$imageOrAlbumId}/comments/ids");
    }

    /**
     * The number of comments on an Image.
     *
     * @param string $imageOrAlbumId
     * @return int
     * @throws GuzzleException
     *
     */
    public function commentCount(string $imageOrAlbumId): int
    {
        return $this->requestGet("gallery/{$imageOrAlbumId}/comments/count");
    }
}
