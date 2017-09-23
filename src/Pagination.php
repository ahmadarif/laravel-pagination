<?php

namespace AhmadArif\Pagination;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use JsonSerializable;

class Pagination implements JsonSerializable
{
    /**
     * @var array
     */
    public $data;

    /**
     * @var int
     */
    public $currentPage;

    /**
     * @var int
     */
    public $perPage;

    /**
     * @var int
     */
    public $total;

    /**
     * @var int
     */
    public $lastPage;

    /**
     * @var string
     */
    public $path;

    /**
     * @var string
     */
    public $nextPageUrl;

    /**
     * @var string
     */
    public $prevPageUrl;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $items;

    public function __construct($items, $perPage = 20) {
        $this->items = $items instanceof Collection ? $items : Collection::make($items);
        $this->currentPage = (int) Paginator::resolveCurrentPage() ?: 1;
        $this->path = Paginator::resolveCurrentPath();
        $this->total = $this->items->count();
        $this->perPage = $perPage;
        $this->lastPage = (int) ceil($this->total / $this->perPage);

        $this->data = [];
        for ($i = $this->from()-1; $i < $this->to() && $i < $this->total; $i++) {
            $this->data[] = $this->items[$i];
        }
    }

    private function from() {
        if ($this->total > 0 && $this->currentPage <= $this->lastPage) {
            return ($this->currentPage - 1) * $this->perPage + 1;
        }
        return null;
    }

    private function to() {
        if ($this->total > 0 && $this->currentPage <= $this->lastPage) {
            if (($this->from() + $this->perPage - 1) <= $this->total) {
                return $this->from() + $this->perPage - 1;
            } else {
                return $this->total;
            }
        }
        return null;
    }

    private function nextPageUrl() {
        if ($this->lastPage > $this->currentPage) {
            return $this->path . '?page=' . ($this->currentPage + 1);
        }
        return null;
    }

    private function previousPageUrl() {
        if ($this->currentPage > 1) {
            return $this->path . '?page=' . ($this->currentPage - 1);
        }
        return null;
    }

    public function toArray() {
        return [
            'current_page' => $this->currentPage,
            'last_page' => $this->lastPage,
            'per_page' => $this->perPage,
            'from' => $this->from(),
            'to' => $this->to(),
            'total' => $this->total,
            'prev_page_url' => $this->previousPageUrl(),
            'path' => $this->path,
            'next_page_url' => $this->nextPageUrl(),
            'data' => $this->data,
        ];
    }

    function jsonSerialize()
    {
        return $this->toArray();
    }
}