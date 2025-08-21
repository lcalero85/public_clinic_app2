<?php
class Paginator {
    private $totalRecords;
    private $recordsPerPage;
    private $currentPage;
    private $totalPages;
    private $url;

    public function __construct($totalRecords, $recordsPerPage = 10, $currentPage = 1, $url = '?page=') {
        $this->totalRecords   = $totalRecords;
        $this->recordsPerPage = $recordsPerPage;
        $this->currentPage    = (int) $currentPage;
        $this->totalPages     = ceil($totalRecords / $recordsPerPage);
        $this->url            = $url;
    }

    public function offset() {
        return ($this->currentPage - 1) * $this->recordsPerPage;
    }

    public function limit() {
        return $this->recordsPerPage;
    }

    public function render() {
        if ($this->totalPages <= 1) return '';

        $html = '<div class="pagination">';
        
        // Botón anterior
        if ($this->currentPage > 1) {
            $html .= '<a href="'.$this->url.($this->currentPage - 1).'" class="prev">&laquo; Prev</a>';
        } else {
            $html .= '<span class="disabled">&laquo; Prev</span>';
        }

        // Números de página
        for ($i = 1; $i <= $this->totalPages; $i++) {
            if ($i == $this->currentPage) {
                $html .= '<span class="active">'.$i.'</span>';
            } else {
                $html .= '<a href="'.$this->url.$i.'">'.$i.'</a>';
            }
        }

        // Botón siguiente
        if ($this->currentPage < $this->totalPages) {
            $html .= '<a href="'.$this->url.($this->currentPage + 1).'" class="next">Next &raquo;</a>';
        } else {
            $html .= '<span class="disabled">Next &raquo;</span>';
        }

        $html .= '</div>';
        return $html;
    }
}
