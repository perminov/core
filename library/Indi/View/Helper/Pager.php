<?php
class Indi_View_Helper_Pager extends Indi_View_Helper_Abstract
{
    /**
     * Store current page number
     */
    public $pageNumber = null;
    
    /**
     * Display total rows, and pages list
     *
     * @param int $found (total rows count, like there was no LIMIT clause, by default it is got from rowset object)
     * @param int $limit    (rows on page)
     * @param int $pageNumber (current page number)
     * @return string
     */
    public function pager($found = null, $limit = null, $pageNumber = null, $display = 5, $js = '$(\'#indexParams\').submit();return false;', $style = '')
    {
        $found  = $found ? $found : $this->view->rowset->found();
        $limit      = $limit ? $limit : $this->view->indexParams['limit'];
        $pageNumber = $pageNumber ? $pageNumber : $this->view->indexParams['page'];
		$this->currentPage = $pageNumber;

        $this->pageNumber = $pageNumber;

        if ($found) {
            $xhtml  = '<div class="pager" style="' . $style . '">';

            $pagesNumber = ceil($found / $limit);
            if ($pagesNumber > 1) {
				if ($pageNumber > 1) {
					$xhtml .= '<a class="previous-link" href="#" onclick="' . $this->getPageUrl($pageNumber - 1, $js) . '">Предыдущая</a> ';
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
					$xhtml .= ' <a class="next-link" href="#" onclick="' . $this->getPageUrl($pageNumber + 1, $js) . '">Следующая</a>';
				}
            }
            $xhtml .= '</div>';
        }
        
        return $xhtml;
    }
    
    /**
     * Regurn url for a page from pages list 
     * This function returns current REQUEST_URI with replaced 
     * page parameter value if set, otherwise it append page 
     * parameter key and value to REQUEST_URI and return result string
     *
     * @param int $pageNumber
     * @return string
     */
    public function getPageUrl($pageNumber, $js)
    {
        $url = array();
        
        foreach ($this->view->params as $param => $value) {
            switch ($param) {
                case 'module':
                    $value == 'default' ? print_r('') : ($url[] = $value);
                    break;
                case 'controller';
                    $url[] = $value;
                    break;
                case 'action';
                    $url[] = $value;
                    break;
                case 'page':
                    $url[] = 'page';
                    $url[] = $pageNumber;
                    break;
                default:
                    $url[] = $param;
                    $url[] = $value;
                    break;
            }
        }
        // if 'page' is not in request parameters
        if (!in_array('page', array_keys($this->view->params))) {
            $url[] = 'page';
            $url[] = $pageNumber;
        }
        // append QUERY_STRING to the end, if not empty
//        return '/' . implode('/', $url) . '/' . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '');
		
		return '$(\'#indexPage\').val('. $pageNumber . '); ' . $js . ';';
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
			return '<a href="#" onclick="' . $this->getPageUrl($pageNumber, $js) .'"' . ($pageNumber == $this->pageNumber ? ' class="current"' : '') . '>' . ($text ? $text : $pageNumber) . '</a>';
		}
    }
}