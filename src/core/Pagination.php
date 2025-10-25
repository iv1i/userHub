<?php
namespace core;

final class Pagination {
    public int $current_page;
    public int $per_page;
    public int $total_count;

    public function __construct($page = 1, $per_page = 10, $total_count = 0) {
        $this->current_page = (int)$page;
        $this->per_page = (int)$per_page;
        $this->total_count = (int)$total_count;
    }
    
    /** @noinspection PhpUnused */
    public function offset(): float|int
    {
        return ($this->current_page - 1) * $this->per_page;
    }

    public function total_pages(): float
    {
        return ceil($this->total_count / $this->per_page);
    }

    public function previous_page(): int
    {
        return $this->current_page - 1;
    }

    public function next_page(): int
    {
        return $this->current_page + 1;
    }
    
    /** @noinspection PhpUnused */
    public function has_previous_page(): bool
    {
        return $this->previous_page() >= 1;
    }
    
    /** @noinspection PhpUnused */
    public function has_next_page(): bool
    {
        return $this->next_page() <= $this->total_pages();
    }
}
