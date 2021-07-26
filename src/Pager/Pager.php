<?php

namespace Vongola\Imgur\Pager;

/**
 * Basic Pager.
 *
 * @see https://api.imgur.com/#paging_results
 *
 * @author Adrian Ghiuta <adrian.ghiuta@gmail.com>
 */
class Pager
{
    private int $page;
    private int $resultsPerPage;

    public function __construct($page = 1, $resultsPerPage = 10)
    {
        $this->setPage($page ?: 1);
        $this->setResultsPerPage($resultsPerPage ?: 10);

        return $this;
    }

    /**
     * Get the page number to be retrieved.
     *
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * Get the number of results per page.
     *
     * @return int
     */
    public function getResultsPerPage(): int
    {
        return $this->resultsPerPage;
    }

    /**
     * Set the page number to be retrieved.
     *
     * @param int $page
     *
     * @return Pager
     */
    public function setPage(int $page): Pager
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Set the number of results per page.
     *
     * @param int $resultsPerPage
     *
     * @return Pager
     */
    public function setResultsPerPage(int $resultsPerPage): Pager
    {
        $this->resultsPerPage = $resultsPerPage;

        return $this;
    }
}
