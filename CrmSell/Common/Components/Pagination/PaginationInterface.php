<?php

namespace CrmSell\Common\Components\Pagination;

interface PaginationInterface
{
    /**
     * @param int $pageNumber
     * @return $this
     */
    public function setPageNumber(int $pageNumber): self;

    /**
     * @param int $amount
     * @return $this
     */
    public function setAmount(int $amount): self;

    /**
     * @param int $limit
     * @return $this
     */
    public function setLimit(int $limit): self;

    /**
     *
     */
    public function buildPagination(): void;

    /**
     * @return int
     */
    public function getOffset(): int;

    /**
     * @return int
     */
    public function getLimit(): int;

    /**
     * @return array
     */
    public function getPagination(): array;

    /**
     * @return int
     */
    public function getTotal(): int;
}
