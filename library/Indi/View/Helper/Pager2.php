<?php
class Indi_View_Helper_Pager2 extends Indi_View_Helper_Abstract
{
    /**
     * Store current page number
     */
    public $pageNumber = null;
	
	public $rowsetAlias = '';
    
    /**
     * Display total rows, and pages list
     *
     * @param int $found (total rows count, like there was no LIMIT clause, by default it is got from rowset object)
     * @param int $limit    (rows on page)
     * @param int $pageNumber (current page number)
     * @return string
     */
    public function pager2($rowsetAlias, $limit = null, $pageNumber = null, $style = 'padding-bottom:0px;')
    {
		$this->rowsetAlias = $rowsetAlias;
		$display = 5;
        $found  = $found ? $found : $this->view->independentRowsets[$rowsetAlias]->found();
        $limit      = $limit ? $limit : 10;
        $pageNumber = $pageNumber ? $pageNumber : $_SESSION['rowsetParams'][$this->view->section->alias][$this->view->action->alias]['independent'][$rowsetAlias]['page'];
		if (!$pageNumber) $pageNumber = 1;
		$this->currentPage = $pageNumber;
        
        $this->pageNumber = $pageNumber;

        if ($found) {
            $xhtml  = '<div class="pager" style="' . $style . '">';

            $pagesNumber = ceil($found / $limit);
            if ($pagesNumber > 1) {
				if ($pageNumber > 1) {
					$xhtml .= '<a class="previous-link" href="#" onclick="' . $this->getPageUrl($pageNumber - 1) . '">Предыдущая</a>';
				}
				$xhtml .= '<span class="pages">';

                // first page
                $pages[] = $this->getPageLink(1, null, $js);
                
                // set up $display count
                $display = $display > $pagesNumber ? $pagesNumber : $display;
                
                $centerCount = $display - 4 - 1;

                // get start page index for center
                $start = $pageNumber - ceil($centerCount / 2);
                
                // shift $start if too small
                if ($start < 3) $start = 3;
                
                // get end page index for center
                $end = $start + $centerCount;

                // shift $end if too big
                if ($end > $pagesNumber - 2) {
                    $end = $pagesNumber - 2;
                    $start = $end - $centerCount;
                }
                
                // prev page
                if ($pagesNumber > 2) {
                    $pages[] = $this->getPageLink($start - 1, ($start - 2 == 1? $start - 1 : '...'), $js);
                }
                
                // current
                if ($pagesNumber > 4) {
                    for ($i = $start; $i <= $end; $i++) {
                        $pages[] = $this->getPageLink($i, null, $js);
                    }
                }

                // next page
                if ($pagesNumber > 3) {
                    $pages[] = $this->getPageLink($end + 1, ($end + 2 == $pagesNumber ? $end + 1 : '...'), $js);
                }
                
                // last page
                $pages[] = $this->getPageLink($pagesNumber, null, $js);
                
                $xhtml .= implode(' ', $pages);
				$xhtml .= '</span>';
				
				if ($pageNumber < $pagesNumber) {
					$xhtml .= '<a class="next-link" href="#" onclick="' . $this->getPageUrl($pageNumber + 1) . '">Следующая</a>';
				}
            }
            $xhtml .= '</div>';
        }
        
        return $xhtml;
    }
    
    public function getPageUrl($pageNumber)
    {
		return 'page(\'' . $this->rowsetAlias . '\',' . $pageNumber . '); return false;';
    }
    
    /**
     * get a tag for page item
     *
     * @param int $pageNumber
     * @param string $text
     * @return string
     */
    public function getPageLink($pageNumber, $text = null, $js)
    {
		if ($pageNumber == $this->currentPage) {
			return '<span>' . $pageNumber . '</span>';
		} else {
			return '<a href="#" onclick="' . $this->getPageUrl($pageNumber) .'"' . ($pageNumber == $this->pageNumber ? ' class="current"' : '') . '>' . ($text ? $text : $pageNumber) . '</a>';
		}
    }
}