<?php

namespace ActiveRecord\Paginator;

class Result implements \Iterator
{

    protected $items;
    protected $key;
    protected $currentPage;
    protected $perPage;
    protected $totalItems;
    protected $pages;
    protected $next;
    protected $previous;

    /**
     * Constructor
     * @param array $items
     * @param int $totalItems numero total de items sin paginar
     * @param int $currentPage pagina actual
     * @param int $perPage items a mostrar por página
     */
    public function __construct(array $items, $totalItems, $currentPage, $perPage = 10)
    {
        $this->items = $items;
        $this->currentPage = $currentPage;
        $this->perPage = $perPage;
        $this->totalItems = $totalItems;
        $this->pages = ceil($totalItems / $perPage);

        $offset = ($currentPage - 1) * $perPage;

        $this->next = ($offset + $perPage) < $totalItems ? ($currentPage + 1) : false;
        $this->previous = ($currentPage > 1) ? ($currentPage - 1) : false;
    }

    public function current()
    {
        return $this->valid() ? $this->items[$this->key] : null;
    }

    public function key()
    {
        return $this->key;
    }

    public function next()
    {
        ++$this->key;
    }

    public function rewind()
    {
        $this->key = 0;
    }

    public function valid()
    {
        return isset($this->items[$this->key]);
    }

    /**
     * Devuelve el arreglo con los items
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Devuelve la página actual
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * Devuelve el numero de registros a mostrar por oágina
     * @return int
     */
    public function getPerPage()
    {
        return $this->perPage;
    }

    /**
     * Devuelve el número de registro sin paginar
     * @return int
     */
    public function getTotalItems()
    {
        return $this->totalItems;
    }

    /**
     * Devuelve el número de paginas que hay
     * @return int
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * devuelve el numero de la página siguiente si la hay, sino devuelve false
     * @return int|boolean
     */
    public function getNext()
    {
        return $this->next;
    }

    /**
     * devuelve el numero de la página anterior si la hay, sino devuelve false
     * @return int|boolean
     */
    public function getPrevious()
    {
        return $this->previous;
    }

}
