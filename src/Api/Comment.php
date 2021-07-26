<?php

namespace Vongola\Imgur\Api;

use GuzzleHttp\Exception\GuzzleException;
use Vongola\Imgur\Exceptions\MissingArgumentException;

/**
 * CURD of Comment
 * @package Vongola\Imgur\Api
 */
class Comment extends BaseApi
{
    /**
     * Get information about a specific comment.
     *
     * @param string $commentId
     * @return array Comment (@see https://api.imgur.com/endpoints/gallery#gallery-comments)
     * @throws GuzzleException
     */
    public function comment(string $commentId): array
    {
        return $this->requestGet("comment/{$commentId}");
    }

    /**
     * Creates a new comment, returns the ID of the comment.
     *
     * @param array $data
     * @return bool
     * @throws GuzzleException
     */
    public function create(array $data): bool
    {
        if (!isset($data['image_id'], $data['comment'])) {
            throw new MissingArgumentException(['image_id', 'comment']);
        }

        return $this->requestPost('comment', $data);
    }

    /**
     * Delete a comment by the given id.
     *
     * @param string $commentId
     * @return bool
     * @throws GuzzleException
     */
    public function delete(string $commentId): bool
    {
        return $this->requestDelete("comment/{$commentId}");
    }

    /**
     * Get the comment with all of the replies for the comment.
     *
     * @param string $commentId
     * @return array Comment (@see https://api.imgur.com/endpoints/gallery#gallery-comments)
     * @throws GuzzleException
     *
     */
    public function replies(string $commentId): array
    {
        return $this->requestGet("comment/{$commentId}/replied");
    }

    /**
     * Create a reply for the given comment.
     *
     * @param string $commentId
     * @param array $data
     * @return bool
     * @throws MissingArgumentException
     * @throws GuzzleException
     *
     */
    public function createReply(string $commentId, array $data): bool
    {
        if (!isset($data['image_id'], $data['comment'])) {
            throw new MissingArgumentException(['image_id', 'comment']);
        }
        return $this->requestPost("comment/{$commentId}", $data);
    }

    /**
     * Vote on a comment. The $vote variable can only be set as "up" or "down".
     *
     * @param string $commentId
     * @param string $vote
     * @return bool
     * @throws GuzzleException
     *
     */
    public function vote(string $commentId, string $vote): bool
    {
        $this->validateVoteArgument($vote, ['up', 'down']);
        return $this->requestPost("comment/{$commentId}/vote/{$vote}");
    }

    /**
     * Report a comment for being inappropriate.
     *
     * @param string $commentId
     * @return bool
     * @throws GuzzleException
     *
     */
    public function report(string $commentId): bool
    {
        return $this->requestPost("comment/{$commentId}/report");
    }
}
