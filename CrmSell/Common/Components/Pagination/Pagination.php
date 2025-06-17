<?php

namespace CrmSell\Common\Components\Pagination;

class Pagination implements PaginationInterface
{
    const DEFAULT_LIMIT = 25;

    private int $limit;
    private int $pageNumber = 1;

    private int $amount;
    private int $total;

    private int $lastPage;
    private int $offset = 0;

    private int $previousPage;
    private int $nextPage;

    public function __construct()
    {
        $this->limit = self::DEFAULT_LIMIT;
    }

    /**
     * @param int $pageNumber
     * @return $this
     */
    public function setPageNumber(int $pageNumber): self
    {
        $this->pageNumber = $pageNumber;

        return $this;
    }

    /**
     * @param int $amount
     * @return $this
     */
    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function setLimit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function buildPagination(): void
    {
        $this->offset = ($this->pageNumber - 1) * $this->limit;
        $this->initTotal();

        $this->lastPage = $this->total;

        $previousPage = $this->pageNumber - 1;
        $this->previousPage = $previousPage === 0 ? 1 : $previousPage;

        $nextPage = $this->pageNumber + 1;
        $this->nextPage = $nextPage >= $this->total ? $this->total : $nextPage;
    }

    private function initTotal(): void
    {
        $this->total = ceil($this->amount / $this->limit);
    }

    public function getOffset(): int {
        return $this->offset;
    }

    public function getLimit(): int {
        return $this->limit;
    }

    public function getTotal(): int {
        return $this->total;
    }

    public function getNextPage(): ?int {
        return $this->nextPage;
    }

    public function getPagination(): array
    {
        return [
            "pages" => [
                "total_pages" => $this->total,
                "current_page" => $this->pageNumber,
                "first_page" => 1,
                "second_page" => $this->pageNumber === 1 && $this->total > 1 ? 2 : null,
                "third_page" => $this->pageNumber === 1 && $this->total > 1 ? 3 : null,
                "previous_page" => ($this->pageNumber !== $this->previousPage) ? $this->previousPage : null,
                "next_page" => ($this->pageNumber !== $this->nextPage) ? $this->nextPage : null,
                "last_page" => $this->lastPage
            ],
            "records" => [
                "all" => $this->amount,
                "from" => $this->offset + 1,
                "to" => ($this->offset + $this->limit) > $this->amount ? $this->amount : $this->offset + $this->limit
            ]
        ];
    }
}
