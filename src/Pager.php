<?php namespace Framework\Pagination;

use Framework\HTTP\URL;
use Framework\Language\Language;

/**
 * Class Pager.
 */
class Pager implements \JsonSerializable
{
	/**
	 * @var int
	 */
	protected $currentPage;
	/**
	 * @var int|null
	 */
	protected $previousPage;
	/**
	 * @var int|null
	 */
	protected $nextPage;
	/**
	 * @var int
	 */
	protected $totalPages;
	/**
	 * @var int
	 */
	protected $totalItems;
	/**
	 * @var int
	 */
	protected $itemsPerPage;
	/**
	 * @var array
	 */
	protected $items;
	/**
	 * @var int
	 */
	protected $surround = 3;
	/**
	 * @var array
	 */
	protected $views = [
		// HTML Head
		'head' => __DIR__ . '/Views/head.php',
		// HTTP Header
		'header' => __DIR__ . '/Views/header.php',
		// HTML Previous and Next
		'pager' => __DIR__ . '/Views/pager.php',
		// HTML Full
		'pagination' => __DIR__ . '/Views/pagination.php',
	];
	/**
	 * @var URL
	 */
	protected $url;
	/**
	 * @var string
	 */
	protected $query = 'page';
	/**
	 * @var Language
	 */
	protected $language;

	/**
	 * Pager constructor.
	 *
	 * @param int           $current_page
	 * @param int           $items_per_page
	 * @param int           $total_items
	 * @param array         $items          Current page items
	 * @param Language|null $language       Language instance
	 */
	public function __construct(
		// int
		$current_page,
		int $items_per_page,
		int $total_items,
		array $items,
		Language $language = null
	) {
		$this->setLanguage($language ?? new Language('en'));
		$this->currentPage = $this->sanitizePageNumber($current_page);
		$this->itemsPerPage = $this->sanitizePerPageNumber($items_per_page);
		$this->totalPages = (int) \ceil($total_items / $this->itemsPerPage);
		if ($this->currentPage > 1 && $this->currentPage - 1 <= $this->totalPages) {
			$this->previousPage = $this->currentPage - 1;
		} elseif ($this->currentPage > 1 && $this->totalPages > 1) {
			$this->previousPage = $this->totalPages;
		}
		if ($this->currentPage < $this->totalPages) {
			$this->nextPage = $this->currentPage + 1;
		}
		$this->totalItems = $total_items;
		$this->items = $items;
	}

	public function __toString() : string
	{
		return $this->render();
	}

	protected function sanitizePageNumber($number) : int
	{
		$number = $number < 1 || ! \is_numeric($number) ? 1 : $number;
		return $number > 1000000000000000 ? 1000000000000000 : (int) $number;
	}

	protected function sanitizePerPageNumber($number) : int
	{
		$number = $number < 1 ? 1 : $number;
		return $number > 1000 ? 1000 : $number;
	}

	protected function setLanguage(Language $language)
	{
		$language->setDirectories(\array_merge([
			__DIR__ . '/Languages',
		], $language->getDirectories()));
		$this->language = $language;
		return $this;
	}

	public function getLanguage() : Language
	{
		return $this->language;
	}

	public function setView(string $name, string $filepath)
	{
		if ( ! \is_file($filepath)) {
			throw new \InvalidArgumentException('Invalid Pager view filepath: ' . $filepath);
		}
		$this->views[$name] = $filepath;
		return $this;
	}

	/**
	 * Get a view filepath.
	 *
	 * @param string $name The view name. Default names are: head, header, pager and pagination
	 *
	 * @return string
	 */
	public function getView(string $name) : string
	{
		if (empty($this->views[$name])) {
			throw new \InvalidArgumentException('Pager view not found: ' . $name);
		}
		return $this->views[$name];
	}

	public function getViews() : array
	{
		return $this->views;
	}

	/**
	 * @return int
	 */
	public function getSurround() : int
	{
		return $this->surround;
	}

	/**
	 * @param int $surround
	 *
	 * @return $this
	 */
	public function setSurround(int $surround)
	{
		$this->surround = $surround < 0 ? 0 : $surround;
		return $this;
	}

	public function setQuery(string $query = 'page')
	{
		$this->query = $query;
		return $this;
	}

	public function getQuery() : string
	{
		return $this->query;
	}

	public function getItems() : array
	{
		return $this->items;
	}

	/**
	 * @param string     $current_page_url
	 * @param array|null $allowed_queries
	 *
	 * @return $this
	 */
	public function setURL(string $current_page_url, array $allowed_queries = null)
	{
		$allowed_queries[] = $this->getQuery();
		$current_page_url = new URL($current_page_url);
		$current_page_url->setQuery($current_page_url->getQuery() ?? '', $allowed_queries);
		$this->url = $current_page_url;
		return $this;
	}

	public function getPageURL(?int $page) : ?string
	{
		if (empty($this->url)) {
			throw new \LogicException('The paginated URL was not set');
		}
		if (empty($page)) {
			return null;
		}
		return $this->url->addQuery($this->getQuery(), $page)->getURL();
	}

	public function getCurrentPageURL() : string
	{
		return $this->getPageURL($this->currentPage);
	}

	public function getFirstPageURL() : ?string
	{
		return $this->getPageURL(1);
	}

	public function getLastPageURL() : ?string
	{
		return $this->getPageURL($this->totalPages);
	}

	public function getPreviousPageURL() : ?string
	{
		return $this->getPageURL($this->previousPage);
	}

	public function getNextPageURL() : ?string
	{
		return $this->getPageURL($this->nextPage);
	}

	public function getPreviousPagesURLs() : array
	{
		$urls = [];
		if ($this->currentPage > 1 && $this->currentPage <= $this->totalPages) {
			$range = \range($this->currentPage - $this->surround, $this->currentPage - 1);
			foreach ($range as $page) {
				if ($page < 1) {
					continue;
				}
				$urls[$page] = $this->getPageURL($page);
			}
		}
		return $urls;
	}

	public function getNextPagesURLs() : array
	{
		$urls = [];
		if ($this->currentPage < $this->totalPages) {
			$range = \range($this->currentPage + 1, $this->currentPage + $this->surround);
			foreach ($range as $page) {
				if ($page > $this->totalPages) {
					break;
				}
				$urls[$page] = $this->getPageURL($page);
			}
		}
		return $urls;
	}

	public function get(bool $with_urls = false) : array
	{
		return [
			'firstPage' => $with_urls ? $this->getFirstPageURL() : 1,
			'previousPage' => $with_urls ? $this->getPreviousPageURL() : $this->previousPage,
			'currentPage' => $with_urls ? $this->getCurrentPageURL() : $this->currentPage,
			'nextPage' => $with_urls ? $this->getNextPageURL() : $this->nextPage,
			'lastPage' => $with_urls ? $this->getLastPageURL() : $this->totalPages,
			'totalPages' => $this->totalPages,
			'itemsPerPage' => $this->itemsPerPage,
			'totalItems' => $this->totalItems,
			'currentPageItems' => $this->items,
		];
	}

	public function render(string $view = 'pagination') : string
	{
		$view = $this->getView($view);
		\ob_start();
		require $view;
		return \ob_get_clean();
	}

	public function jsonSerialize()
	{
		return $this->get(true);
	}
}
