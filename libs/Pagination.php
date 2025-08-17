<?php
class Pagination {
	
    public int $record_count;
 
    public bool $show_page_count = false;
    public bool $show_record_count = false;
    public bool $show_page_limit = false;
    public bool $show_pagination = false;

    public int $limit = 10;

    public int $page = 1;
    public int $total_records = 0;
    public int $total_pages = 0;

    /** @var mixed Puede ser array, object o null */
    public $request = null;

    public function __construct($request = null) {
        // Si es int, lo ignoramos y usamos $_GET
        if (is_int($request)) {
            $this->request = (object) $_GET;
        } 
        // Si es array, lo convertimos en objeto
        elseif (is_array($request)) {
            $this->request = (object) $request;
        } 
        // Si ya es objeto, lo usamos directamente
        elseif (is_object($request)) {
            $this->request = $request;
        } 
        // Si no viene nada, usamos $_GET
        else {
            $this->request = (object) $_GET;
        }

        // Página desde GET
        if (isset($this->request->page) && is_numeric($this->request->page)) {
            $this->page = max(1, (int)$this->request->page);
        }
    }

    public function setLimit(int $limit): void {
        $this->limit = $limit > 0 ? $limit : 10;
    }

    public function setTotalRecords(int $total): void {
        $this->total_records = $total;
        $this->total_pages = (int) ceil($this->total_records / $this->limit);

        if ($this->page > $this->total_pages) {
            $this->page = $this->total_pages > 0 ? $this->total_pages : 1;
        }
    }

    public function getOffset(): int {
        return ($this->page - 1) * $this->limit;
    }

    public function render(): string {
        if (!$this->show_pagination || $this->total_pages <= 1) {
            return '';
        }

        $html = '<nav aria-label="Page navigation"><ul class="pagination">';

        if ($this->page > 1) {
            $prev = $this->page - 1;
            $html .= '<li class="page-item"><a class="page-link" href="?page=' . $prev . '">Previous</a></li>';
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">Previous</span></li>';
        }

        for ($i = 1; $i <= $this->total_pages; $i++) {
            if ($i == $this->page) {
                $html .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
            } else {
                $html .= '<li class="page-item"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
            }
        }

        if ($this->page < $this->total_pages) {
            $next = $this->page + 1;
            $html .= '<li class="page-item"><a class="page-link" href="?page=' . $next . '">Next</a></li>';
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">Next</span></li>';
        }

        $html .= '</ul></nav>';
        return $html;
    }
}


