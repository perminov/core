<?php
class Indi_Controller_Admin extends Indi_Controller {

    /**
     * Flag for set up whether rowset data should be fetched ONLY 
     * as a result of a separate http request
     * 
     * @var bool
     */
    protected $_isRowsetSeparate = false;

    /**
     * Array of section ids, starting from current section and up to the top.
     *
     * @var array
     */
    private $_routeA = array();

    /**
     * Modes and views for typical actions. Can be adjusted with adjustActionCfg() method
     *
     * @var array
     */
    public $actionCfg = array(
        'mode' => array(
            'index' => 'rowset',
            'form' => 'row',
            'up' => 'row',
            'down' => 'row',
            'toggle' => 'row',
            'print' => 'row'
        ),
        'view' => array(
            'index' => 'grid',
            'form' => 'form',
            'print' => 'print'
        )
    );

    /**
     * If the purpose of current request is to build an excel spreadsheet - this variable will be used
     * for collecting filters usage information, and retrieving that information in the process of building
     * spreadsheet
     *
     * @var array
     */
    protected $_excelA = array();

    /**
     * Init all general cms features
     */
    public function preDispatch() {

        // Set locale
        if (Indi::ini()->lang->admin == 'ru')
            setlocale(LC_TIME, 'ru_RU.UTF-8', 'ru_utf8', 'Russian_Russia.UTF8', 'ru_RU', 'Russian');

        // Adjust action mode and view config.
        $this->adjustActionCfg();

        // Perform authentication
        $this->auth();

        // Jump, if need
        $this->jump();

        // Adjust params of certain scope, if $_REQUEST['forScope'] param exists
        $this->adjustCertainScope();

        // If we are in some section, mean not in just '/admin/', but at least in '/admin/somesection/'
        if (Indi::trail(true) && Indi::trail()->model) {

            // If action is 'index'
            if (Indi::uri('action') == 'index') {

                // Get the primary WHERE clause
                $primaryWHERE = $this->primaryWHERE();

                // Set 'hash' scope param at least. Additionally, set scope info about primary hash and row index,
                // related to parent section, if these params are passed within the uri.
                $applyA = array('hash' => Indi::trail()->section->primaryHash);
                if (Indi::uri()->ph) $applyA['upperHash'] = Indi::uri()->ph;
                if (Indi::uri()->aix) $applyA['upperAix'] = Indi::uri()->aix;
                if (Indi::get()->stopAutosave) $applyA['toggledSave'] = false;
                Indi::trail()->scope->apply($applyA);

                // If there was no 'format' param passed within the uri
                // we extract all fetch params from current scope
                if (!Indi::uri()->format && !$this->_isRowsetSeparate) {

                    // Prepare search data for $this->filtersWHERE()
                    Indi::get()->search = Indi::trail()->scope->filters;

                    // Prepare search data for $this->keywordWHERE()
                    Indi::get()->keyword = urlencode(Indi::trail()->scope->keyword);

                    // Prepare sort params for $this->finalORDER()
                    Indi::get()->sort = Indi::trail()->scope->order == '[]'
                        ? Indi::trail()->section->jsonSort()
                        : Indi::trail()->scope->order;

                    // Prepare sort params for $this->finalORDER()
                    Indi::get()->page = Indi::trail()->scope->page ? Indi::trail()->scope->page : 1;

                    // Prepare sort params for $this->finalORDER()
                    Indi::get()->limit = Indi::trail()->section->rowsOnPage;
                }

                // If 'format' uri's param was specified
                if (Indi::uri()->format || !$this->_isRowsetSeparate) {

                    // Get final WHERE clause, that will implode primaryWHERE, filterWHERE and keywordWHERE
                    $finalWHERE = $this->finalWHERE($primaryWHERE);

                    // Get final ORDER clause, built regarding column name and sorting direction
                    $finalORDER = $this->finalORDER($finalWHERE, Indi::get()->sort);

                    // Get the rowset, fetched using WHERE and ORDER clauses, and with built LIMIT clause,
                    // constructed with usage of Indi::get('limit') and Indi::get('page') params
                    $this->rowset = Indi::trail()->model->{
                    'fetch'. (Indi::trail()->model->treeColumn() && !$this->actionCfg['misc']['index']['ignoreTreeColumn'] ? 'Tree' : 'All')
                    }($finalWHERE, $finalORDER,
                        Indi::uri()->format == 'json' || !Indi::uri()->format ? (int) Indi::get('limit') : null,
                        Indi::uri()->format == 'json' || !Indi::uri()->format ? (int) Indi::get('page') : null);

                    /**
                     * Remember current rowset properties SQL - WHERE, ORDER, LIMIT clauses - to be able to apply these properties in cases:
                     *
                     * 1. We were in one section, made some search by filters and/or keyword, did sorting by some column, went to some page.
                     *    After that we went to another section, and then decide to return to the first. So, by this function, system will be
                     *    able to retrieve first section's params from $_SESSION, and display the grid in the exact same way as it was when
                     *    we had left it.
                     * 2. We were in one section, made some search by filters and/or keyword, did sorting by some column, went to some page and
                     *    clicked 'Details' on some row on that page, so the details form was displayed. So, this function is one of providing
                     *    an ability to navigate/jump to current row's siblings - go to prev/next rows. Foк example if we have a States section,
                     *    and we go to it, and types 'Ala' in fast-keyword-search field, so the corresponding results were displayed. Then, if we
                     *    go to 'Alabama' form screen, there will be buttons titled "Prev" and "Next". For example, by clicking Next, the Alaska's
                     *    editing form will be displayed instead of Alabama's. But there will be certainly no 'Ohio'.
                     *
                     * Actually, the only one param is stored as SQL-string - $primary param. This params includes all parts of WHERE clause, that
                     * was used to retrieve a current rowset, but except parts, related to filters/keyword search usage. There parts are
                     * stored as JSON-string, because it is much more easier to get last used filters's values from JSON rather than SQL.
                     *
                     * Function creates a hash-key (md5 from $primary param) to place the array of scope params under this key in $_SESSION
                     *
                     * $order param is stored in JSON format too, because it will be passed to Ext.grid
                     */
                    Indi::trail()->scope->apply(array(
                        'primary' => $primaryWHERE, 'filters' => Indi::get()->search, 'keyword' => Indi::get()->keyword,
                        'order' => Indi::get()->sort, 'page' => Indi::get()->page, 'found' => $this->rowset->found(),
                        'WHERE' => $finalWHERE, 'ORDER' => $finalORDER, 'hash' => Indi::trail()->section->primaryHash
                    ));
                }

            // Else if where is some another action
            } else {

                // Apply some scope params
                $applyA = array('hash' => Indi::uri()->ph, 'aix' => Indi::uri()->aix);
                if (Indi::get()->stopAutosave) $applyA['toggledSave'] = false;
                Indi::trail()->scope->apply($applyA);

                // If we are here for just check of row availability, do it
                if (Indi::uri()->check) jflush(true, $this->checkRowIsInScope());

                // Set last accessed row
                $this->setScopeRow();
            }
        }
    }

    /**
     * Provide default downAction (Move down) for Admin Sections controllers
     *
     * @param string $where
     */
    public function downAction($where = null) {
        $this->move('down', $where);
    }

    /**
     * Provide default upAction (Move up) for Admin Sections controllers
     *
     * @param string $where
     */
    public function upAction($where = null) {
        $this->move('up', $where);
    }

    /**
     * Gets $within param and call $row->move() method with that param.
     * This was created just for use in in $controller->downAction() and $controller->upAction()
     *
     * @param $direction
     */
    public function move($direction) {

        // Get the scope of rows to move within
        $within = $this->primaryWHERE();

        // If row move was successful
        if ($this->row->move($direction, $within)) {

            // Get the page of results, that we were at
            $wasPage = Indi::trail()->scope->page;

            // If current model has a tree-column, detect new row index by a special algorithm
            if (Indi::trail()->model->treeColumn()) Indi::uri()->aix = Indi::trail()->model->detectOffset(
                Indi::trail()->scope->WHERE, Indi::trail()->scope->ORDER, $this->row->id);

            // Else just shift current row index by inc/dec-rementing
            else Indi::uri()->aix += $direction == 'up' ? -1 : 1;

            // Apply new index
            $this->setScopeRow();

            // Flush json response, containing new page index, in case if now row
            // index change is noticeable enough for rowset current page was shifted
            jflush(true, $wasPage != ($nowPage = Indi::trail()->scope->page) ? array('page' => $nowPage) : array());
        }

        // Flush json response
        jflush(false);
    }

    /**
     * Provide delete action
     */
    public function deleteAction($redirect = true) {

        // Do pre delete maintenance
        $this->preDelete();

        // Delete row
        if ($deleted = (int) $this->row->delete()) {

            // Get the page of results, that we were at
            $wasPage = Indi::trail()->scope->page;

            // Decrement row index. This line, and one line below - are required to provide an ability to shift
            // the selection within rowset (grid, tile, etc) panel, after current row deletion
            Indi::uri()->aix -= 1;

            // Apply new index
            $this->setScopeRow();
        }

        // Do post delete maintenance
        $this->postDelete($deleted);

        // Flush json response, containing new page index, in case if now row
        // index change is noticeable enough for rowset current page was shifted
        jflush((bool) $deleted, $wasPage != ($nowPage = Indi::trail()->scope->page) ? array('page' => $nowPage) : array());
    }

    /**
     * Provide form action
     */
    public function formAction() {
        if (Indi::trail()->disabledFields->count()) {
            Indi::trail()->disabledFields->foreign('fieldId');
            if (!$this->row->id) foreach (Indi::trail()->disabledFields as $disabledFieldR) {
                if (strlen($disabledFieldR->defaultValue)) {
                    $this->row->{$disabledFieldR->foreign('fieldId')->alias} = $disabledFieldR->compiled('defaultValue');
                }
            }
        }
    }

    /**
     * Set scope last accessed row
     *
     * @param bool $upper
     * @return null
     */
    public function setScopeRow($upper = false) {

        // If no primary hash param passed within the uri - return
        if (!Indi::uri()->ph) return;

        // Get the current state of scope
        $original = $_SESSION['indi']['admin'][Indi::trail((int) $upper)->section->alias][Indi::uri()->ph];

        // If there is no current state yet - return
        if (!is_array($original)) return;

        // If current action deals with row, that is not yet exists in database - return
        if (!$this->row->id) return;

        // Setup $modified array with 'aix' param as first item in. This array may be additionally fulfilled with
        // 'page' param, if passed 'aix' value is too large or too small to match initial results page number (this
        // mean that page number should be recalculated, so 'page' param will store recalculated page number). After
        // all necessary operations will be done - valued from this ($modified) array will replace existing values
        // in scope
        $modified = array('aix' => Indi::uri()->aix);

        // Start and end indexes. We calculate them, because we should know, whether page number should be changed or no
        $start = ($original['page'] - 1) * Indi::trail((int) $upper)->section->rowsOnPage + 1;
        $end = ($original['page']) * Indi::trail((int) $upper)->section->rowsOnPage;

        // If last accessed row index is out of latest fetched rowset page bounds
        if ($modified['aix'] < $start || $modified['aix'] > $end) {

            // Calculate new page number, so when user will return to grid panel - an appropriate page
            // will be displayed, and last accessed row will be within it
            $modified['page'] = ceil($modified['aix']/Indi::trail((int) $upper)->section->rowsOnPage);
        }

        // Remember all scope params in $_SESSION under a hash
        Indi::trail((int) $upper)->scope->apply($modified);
    }

    /**
     * Builds an array of WHERE clauses, that will be imploded with AND, and used to determine a possible border limits
     * of scope of rows that section will be dealing with, and that are allowed for section to deal with them.
     * While building, it handles:
     *
     * 1. Childs-by-parent logic
     * 2. Alternates logic
     * 3. Custom additional adjustments (adjustments of WHERE clauses stack)
     *
     * After an array is built, function calcs a hash for imploded array, and assigns that hash as a temporary property
     * of current section, for hash to be accessbile within View object.
     *
     * primaryWHERE = parentWHERE + alternates handling, adjusted with adjustPrimaryWHERE()
     *
     * @return array
     */
    public function primaryWHERE() {

        // Define an array for WHERE clauses
        $where = array();

        // Append a childs-by-parent clause to primaryWHERE stack
        if ($parentWHERE = $this->parentWHERE()) $where['parent'] = $parentWHERE;

        // If a special section's primary filter was defined, add it to primary WHERE clauses stack
        if (strlen(Indi::trail()->section->compiled('filter'))) $where['static'] = Indi::trail()->section->compiled('filter');

        // Owner control. There can be a situation when some cms users are not stored in 'admin' db table - these users
        // called 'alternates'. Example: we have 'Experts' cms section (rows are fetched from 'expert' db table) and
        // public (mean non-cms) area logic allow any visitor to ask a questions to certain expert. So, if we want to
        // provide an ability for experts to answer these questions, and if we do not want to create a number of special
        // web-pages in public area that will handle all related things, we can provide a cms access for experts instead.
        // So we can create a 'Questions' section within cms, and if `question` table will contain `expertId` column
        // (it will contain - we will create it for that purpose) - the only questions, addressed to curently logged-in
        // expert will be available for view and answer.
        if ($alternateWHERE = $this->alternateWHERE()) $where['alternate'] =  $alternateWHERE;

        // Adjust primary WHERE clauses stack - apply some custom adjustments
        $where = $this->adjustPrimaryWHERE($where);

        if (Indi::uri('action') == 'index') {

            // Get a string version of WHERE stack
            $whereS = count($where) ? implode(' AND ', $where) : null;

            // Set a hash
            Indi::trail()->section->primaryHash = substr(md5($whereS), 0, 10);
        }

        // Return primary WHERE clauses stack
        return $where;
    }

    /**
     * Get part of WHERE clause, that provide owner control access restriction
     *
     * @param int $trailStepsUp
     * @return string
     */
    public function alternateWHERE($trailStepsUp = 0) {
        if (Indi::admin()->alternate && $alternateFieldR = Indi::trail($trailStepsUp)->model->fields(Indi::admin()->alternate . 'Id'))
            return $alternateFieldR->storeRelationAbility == 'many'
                ? 'FIND_IN_SET("' . Indi::admin()->id . '", `' . Indi::admin()->alternate . 'Id' . '`)'
                : '`' . Indi::admin()->alternate . 'Id' . '` = "' . Indi::admin()->id . '"';
    }

    /**
     * Builds a SQL string from an array of clauses, imploded with OR. String will be enclosed by round brackets, e.g.
     * '(`column1` LIKE "%keyword%" OR `column2` LIKE "%keyword%" OR `columnN` LIKE "%keyword%")'. Result string will
     * not contain search clauses for columns, that are involved in building of set of another kind of WHERE clauses -
     * related to grid filters
     *
     * @param $keyword
     * @return string
     */
    public function keywordWHERE($keyword = '') {

        // If $keyword param is not passed we pick Indi::get()->keyword as $keyword
        if (strlen($keyword) == 0) $keyword = Indi::get()->keyword;

        // Exclusions array - we will be not trying to find a keyword in columns, that will be involved in search process
        // in $this->filtersWHERE() function, so one column can be used to find either selected-grid-filter-value or keyword,
        // not both at the same time
        $exclude = array_keys(Indi::obar());

        // Use keywordWHERE() method call on fields rowset to obtain a valid WHERE clause for the given keyword
        return Indi::trail()->gridFields->keywordWHERE($keyword, $exclude);
    }

    /**
     * Build and return a final WHERE clause, that will be passed to fetchAll() method, for fetching section's main
     * rowset. Function use a $primaryWHERE, merge it with $this->filtersWHERE() and append to it $this->keywordWHERE()
     * if return values of these function are not null
     *
     * @param string|array $primaryWHERE
     * @param string|array $customWHERE
     * @param bool $merge
     * @return null|string|array
     */
    public function finalWHERE($primaryWHERE, $customWHERE = null, $merge = true) {

        // Empty array yet
        $finalWHERE = array();

        // If there was a primaryHash passed instead of $primaryWHERE param - then we extract all scope params from
        if (is_string($primaryWHERE) && preg_match('/^[0-9a-zA-Z]{10}$/', $primaryWHERE)) {

            // Prepare $primaryWHERE
            $primaryWHERE = Indi::trail()->scope->primary;

            // Prepare search data for $this->filtersWHERE()
            Indi::get()->search = Indi::trail()->scope->filters;

            // Prepare search data for $this->keywordWHERE()
            Indi::get()->keyword = urlencode(Indi::trail()->scope->keyword);

            // Prepare sort params for $this->finalORDER()
            Indi::get()->sort = Indi::trail()->scope->order;
        }

        // Push primary part
        if ($primaryWHERE || $primaryWHERE == '0') $finalWHERE['primary'] = $primaryWHERE;

        // Get a WHERE stack of clauses, related to filters search and push it into $finalWHERE under 'filters' key
        if (count($filtersWHERE = $this->filtersWHERE())) $finalWHERE['filters'] = $filtersWHERE;

        // Get a WHERE clause, related to keyword search and push it into $finalWHERE under 'keyword' key
        if ($keywordWHERE = $this->keywordWHERE()) $finalWHERE['keyword'] = $keywordWHERE;

        // Append custom WHERE
        if ($customWHERE || $customWHERE == '0') $finalWHERE['custom'] = $customWHERE;

        // If WHERE clause should be a string
        if ($merge) {

            // Force $finalWHERE to be single-dimension array
            foreach ($finalWHERE as $part => $where) if (is_array($where)) $finalWHERE[$part] = im($where, ' AND ');

            // Stringify
            $finalWHERE = implode(' AND ', $finalWHERE);
        }

        // Return
        return $finalWHERE;
    }

    /**
     * @return null
     */
    public function checkRowIsInScope(){

        // If row should be detected by it's index, within the current scope
        if (Indi::uri()->aix && !Indi::uri()->id) {

            // Get row by it's index
            $R = Indi::trail()->model->fetchRow(
                Indi::trail()->scope->WHERE,
                Indi::trail()->scope->ORDER,
                Indi::uri()->aix - 1
            );

            // Return basic data
            return array('id' => $R->id, 'title' => $R->title);

        // Else if row index should be found by it's id within the current scope
        } else if (Indi::uri()->id) {

            // Prepare checking-WHERE clause (for checking the row existence)
            $where = array('`id` = "' . Indi::uri()->id . '"');

            // Append current scope's WHERE clause to checking-WHERE clause
            if (strlen(Indi::trail()->scope->WHERE)) $where[] = Indi::trail()->scope->WHERE;

            // Check that row exists with such an id within the current scope
            $R = Indi::trail()->model->fetchRow($where);

            // If row index should be additionally detected
            if (Indi::post()->forceOffsetDetection && $R)

                // Get that offset and return it along with row title
                return array(
                    'aix' => Indi::trail()->model->detectOffset(
                        Indi::trail()->scope->WHERE,
                        Indi::trail()->scope->ORDER,
                        $R->id
                    ),
                    'title' => $R->title()
                );

            // Or just return the id, as an ensurement, that such row exists
            else return $R ? array('id' => $R->id, 'title' => $R->title()) : array();
        }
    }

    /**
     * Provide a download of a excel spreadsheet
     *
     * @param $data
     * @param $format
     */
    public function export($data, $format = 'excel'){

        /** Include path **/
        ini_set('include_path', ini_get('include_path').';../Classes/');

        /** PHPExcel */
        include 'PHPExcel.php';

        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set properties
        $objPHPExcel->getProperties()->setCreator($_SESSION['admin']['title']);
        $objPHPExcel->getProperties()->setLastModifiedBy($_SESSION['admin']['title']);
        $objPHPExcel->getProperties()->setTitle(Indi::trail()->section->title);

        $font = 'Tahoma';

        // Set up $noBorder variable, containing style definition to be used as an argument while applyFromArray() calls
        // in cases then no borders should be displayed
        $_noBorder = array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => 'ffffff'));
        $noBorder = array('borders' => array('right' => $_noBorder, 'left' => $_noBorder, 'top' => $_noBorder));

        // Set active sheet by index
        $objPHPExcel->setActiveSheetIndex(0);

        // Get the columns, that need to be presented in a spreadsheet
        $columnA = json_decode(Indi::get()->columns, true);

        // Setup a row index, which data rows are starting from
        $currentRowIndex = 1;

        // Calculate last row index
        $lastRowIndex =
            1 /* bread crumbs row*/ +
                1 /* row with total number of results found */ +
                (is_array($this->_excelA) && count($this->_excelA) ? count($this->_excelA) + 1 : 0) /* filters count */ +
                (bool) (Indi::get()->keyword || (is_array($this->_excelA) && count($this->_excelA) > 1)) +
                1 /* data header row */+
                count($data) + /* data rows*/
                (Indi::get()->summary ? 1 : 0);

        // Set default row height
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(15.75);

        // Apply general styles for the whole spreadsheet
        foreach ($columnA as $n => $columnI) {

            // Get column letter
            $columnL = PHPExcel_Cell::stringFromColumnIndex($n);

            // Apply styles for all rows within current column (font and alignment)
            $objPHPExcel->getActiveSheet()
                ->getStyle($columnL . '1:' . $columnL . $lastRowIndex)
                ->applyFromArray(array(
                    'font' => array(
                        'size' => 8,
                        'name' => $font,
                        'color' => array(
                            'rgb' => '04408C'
                        )
                    ),
                    'alignment' => array(
                        'vertical' => 'center'
                    )
                )
            );
        }

        // Capture last column letter(s)
        $lastColumnLetter = $columnL;

        // Merge all cell at first row, as bread crumbs will be placed here
        $objPHPExcel->getActiveSheet()->mergeCells('A1:' . $lastColumnLetter . '1');

        // Write bread crumbs, where current spreadsheet was got from
        $crumbA = Indi::trail(true)->toString(false);

        // Defined a PHPExcel_RichText object
        $objRichText = new PHPExcel_RichText();

        // For each crumb
        for ($i = 0; $i < count($crumbA); $i++) {

            // Set font name, size and color
            $objSelfStyled = $objRichText->createTextRun(strip_tags($crumbA[$i]));
            $objSelfStyled->getFont()->setName($font)->setSize('9')->getColor()->setRGB('04408C');

            // Check if crubs contains html-code
            if (mb_strlen($crumbA[$i], 'utf-8') != mb_strlen(strip_tags($crumbA[$i]), 'utf-8')) {

                // Set italic if detected
                if (preg_match('/<\/i>/', $crumbA[$i])) $objSelfStyled->getFont()->setItalic(true);

                // Set color if detected
                if (preg_match('/color[:=][ ]*[\'"]{0,1}([#a-zA-Z0-9]+)/i', $crumbA[$i], $c))

                    // If we find a hex equivalent for found color definition (if it's not already in hex format)
                    if ($hex = Indi::hexColor($c[1]))

                        // We set font color
                        $objSelfStyled->getFont()->getColor()->setRGB(ltrim($hex, '#'));

            }

            // Append separator
            if ($i < count($crumbA) -1) {
                $objSelfStyled = $objRichText->createTextRun(' » ');
                $objSelfStyled->getFont()->setName($font)->setSize('9');
                $objSelfStyled->getFont()->getColor()->setRGB('04408C');
            }
        }

        // Write prepared rich text object to first row
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', $objRichText);
        if (Indi::uri()->format == 'pdf') $objPHPExcel->getActiveSheet()->getStyle('A' . $currentRowIndex)->applyFromArray($noBorder);

        // Here we set row height, because OpenOffice Writer (unlike Microsoft Excel)
        // ignores previously set default height definition
        $objPHPExcel->getActiveSheet()->getRowDimension($currentRowIndex)->setRowHeight(15.75);

        // Increment current row index as we need to keep it actual after each new row added to the spreadsheet
        $currentRowIndex++;

        // Set total number of $data items
        $objPHPExcel->getActiveSheet()->SetCellValue('A' . $currentRowIndex, I_TOTAL . ': ' . count($data));
        $objPHPExcel->getActiveSheet()->mergeCells('A' . $currentRowIndex . ':' . $lastColumnLetter . $currentRowIndex);
        if (Indi::uri()->format == 'pdf') $objPHPExcel->getActiveSheet()->getStyle('A' . $currentRowIndex)->applyFromArray($noBorder);
        $objPHPExcel->getActiveSheet()->getRowDimension($currentRowIndex)->setRowHeight(15.75);
        $currentRowIndex++;

        // If filters were used
        if (is_array($this->_excelA) && count($this->_excelA)) {

            // Before shifting current row index to provide a empty row for visual separation between bread
            // crumbs row and filters rows, we check if export type is 'pdf', and if so
            if (Indi::uri()->format == 'pdf') {

                // Insert a whitespace into first cell of that visual-separator-row, as DomPDF ignores height
                // definition for empty row, and makes it tiny, so, by inserting a whitespace - we ensure that
                // visual separation will be properly displayed
                $objPHPExcel->getActiveSheet()->SetCellValue('A' . $currentRowIndex, ' ');

                // Merge all cell within that separator-row before no-border-style apply
                $objPHPExcel->getActiveSheet()->mergeCells('A' . $currentRowIndex . ':' . $lastColumnLetter . $currentRowIndex);

                // Apply no-border style
                $objPHPExcel->getActiveSheet()->getStyle('A' . $currentRowIndex)->applyFromArray($noBorder);
            }

            // We shift current row index to provide a empty row for visual separation between bread crumbs row and filters rows,
            $currentRowIndex++;

            // Info about filters was prepared to $this->filtersWHERE() method, as an array of used filters
            // For each used filter:
            foreach ($this->_excelA as $alias => $excelI) {

                // Create rich text object
                $objRichText = new PHPExcel_RichText();

                // Merge all cell within current row
                $objPHPExcel->getActiveSheet()->mergeCells('A' . $currentRowIndex . ':' . $lastColumnLetter . $currentRowIndex);

                // Write a filter title and setup a font name, size and color for it
                $objSelfStyled = $objRichText->createTextRun($excelI['title'] . ' -» ');
                $objSelfStyled->getFont()->setName($font)->setSize('8')->getColor()->setRGB('04408C');

                // If filter type is 'date' (or 'datetime'. There is no difference at this case)
                if ($excelI['type'] == 'date') {

                    // Get the format
                    foreach (Indi::trail()->fields as $fieldR) {
                        if ($fieldR->alias == $alias) {
                            $dformat = $fieldR->params['display' . ($fieldR->elementId == 12 ? '' : 'Date') . 'Format'];
                        }
                    }

                    // If start point for date range specified
                    if (isset($excelI['value']['gte'])) {

                        // Write the 'from ' string before actual filter date
                        $objSelfStyled = $objRichText->createTextRun(I_ACTION_INDEX_FILTER_TOOLBAR_DATE_FROM . ' ');
                        $objSelfStyled->getFont()->setName($font)->setSize('8');
                        $objSelfStyled->getFont()->getColor()->setRGB('04408C');

                        // Deal with date converstion
                        if (preg_match(Indi::rex('date'), $excelI['value']['gte'])) {
                            if ($excelI['value']['gte'] == '0000-00-00' && $dformat == 'd.m.Y') {
                                $excelI['value']['gte'] = '00.00.0000';
                            } else if ($excelI['value']['gte'] != '0000-00-00'){
                                $excelI['value']['gte'] = date($dformat, strtotime($excelI['value']['gte']));
                                if ($excelI['value']['gte'] == '30.11.-0001') $excelI['value']['gte'] = '00.00.0000';
                            }
                        }

                        // Write the converted date
                        $objSelfStyled = $objRichText->createTextRun($excelI['value']['gte'] . ' ');
                        $objSelfStyled->getFont()->setName($font)->setSize('8');
                        $objSelfStyled->getFont()->getColor()->setRGB('7EAAE2');
                    }

                    // If end point for date range specified
                    if (isset($excelI['value']['lte'])) {

                        // Write the 'until ' string before actual filter date
                        $objSelfStyled = $objRichText->createTextRun(I_ACTION_INDEX_FILTER_TOOLBAR_DATE_TO . ' ');
                        $objSelfStyled->getFont()->setName($font)->setSize('8');
                        $objSelfStyled->getFont()->getColor()->setRGB('04408C');

                        // Deal with date converstion
                        if (preg_match(Indi::rex('date'), $excelI['value']['lte'])) {
                            if ($excelI['value']['lte'] == '0000-00-00' && $dformat == 'd.m.Y') {
                                $excelI['value']['lte'] = '00.00.0000';
                            } else if ($excelI['value']['gte'] != '0000-00-00'){
                                $excelI['value']['lte'] = date($dformat, strtotime($excelI['value']['lte']));
                                if ($excelI['value']['lte'] == '30.11.-0001') $excelI['value']['lte'] = '00.00.0000';
                            }
                        }

                        // Write the converted date
                        $objSelfStyled = $objRichText->createTextRun($excelI['value']['lte'] . ' ');
                        $objSelfStyled->getFont()->setName($font)->setSize('8');
                        $objSelfStyled->getFont()->getColor()->setRGB('7EAAE2');
                    }

                    // If filter type is 'number'
                } else if ($excelI['type'] == 'number') {

                    // If start point for number range specified
                    if (isset($excelI['value']['gte'])) {

                        // Write the 'from ' string before actual filter value
                        $objSelfStyled = $objRichText->createTextRun(I_ACTION_INDEX_FILTER_TOOLBAR_NUMBER_FROM . ' ');
                        $objSelfStyled->getFont()->setName($font)->setSize('8');
                        $objSelfStyled->getFont()->getColor()->setRGB('04408C');

                        // Write the actual filter start point value
                        $objSelfStyled = $objRichText->createTextRun($excelI['value']['gte'] . ' ');
                        $objSelfStyled->getFont()->setName($font)->setSize('8');
                        $objSelfStyled->getFont()->getColor()->setRGB('7EAAE2');
                    }

                    // If start point for number range specified
                    if (isset($excelI['value']['lte'])) {

                        // Write the 'to ' string before actual filter value
                        $objSelfStyled = $objRichText->createTextRun(I_ACTION_INDEX_FILTER_TOOLBAR_NUMBER_TO . ' ');
                        $objSelfStyled->getFont()->setName($font)->setSize('8');
                        $objSelfStyled->getFont()->getColor()->setRGB('04408C');

                        // Write the actual filter end point value
                        $objSelfStyled = $objRichText->createTextRun($excelI['value']['lte'] . ' ');
                        $objSelfStyled->getFont()->setName($font)->setSize('8');
                        $objSelfStyled->getFont()->getColor()->setRGB('7EAAE2');
                    }


                    // If filter type is 'number'
                } else if ($excelI['type'] == 'color') {

                    // Create the GD canvas image for hue background and thumbs to be placed there
                    $canvasIm = imagecreatetruecolor(197, 15);
                    imagecolortransparent($canvasIm, imagecolorallocate($canvasIm, 0, 0, 0));

                    // Pick hue bg and place it on canvas
                    $hueFn = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . STD . '/core/i/admin/i-color-slider-bg.png';
                    $hueIm = imagecreatefrompng($hueFn);
                    imagecopy($canvasIm, $hueIm, 7, 2, 0, 0, 183, 11);

                    // Pick first thumb and place it on canvas
                    $firstThumbFn = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . STD . '/core/i/admin/i-color-slider-thumb-first.png';
                    $firstThumbIm = imagecreatefrompng($firstThumbFn);
                    imagecopy($canvasIm, $firstThumbIm, floor(183/360*$excelI['value'][0]), 0, 0, 0, 15, 15);

                    // Pick last thumb and place it on canvas
                    $firstThumbFn = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . STD . '/core/i/admin/i-color-slider-thumb-last.png';
                    $firstThumbIm = imagecreatefrompng($firstThumbFn);
                    imagecopy($canvasIm, $firstThumbIm, floor(183/360*$excelI['value'][1]), 0, 0, 0, 15, 15);

                    //  Add the GD image to a spreadsheet
                    $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
                    $objDrawing->setCoordinates('A' . $currentRowIndex);
                    $objDrawing->setImageResource($canvasIm);
                    $objDrawing->setHeight(11)->setWidth(183)->setOffsetY(4)->setOffsetX($excelI['offset'] + 5);
                    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

                // If filter type is combo, with foreign table relation
                } else if ($excelI['table']) {

                    // Prepare the array of keys
                    if (!is_array($excelI['value'])) $excelI['value'] = array($excelI['value']);

                    // Fetch rows by keys
                    $rs = Indi::model($excelI['table'])->fetchAll('`id` IN (' . implode(',', $excelI['value']) . ')')->toArray();

                    // Foreach row
                    for ($i = 0; $i < count($rs); $i++) {

                        // Set default color
                        $color = '7EAAE2';

                        // Check if row title contains color definition
                        if (preg_match('/color[:=][ ]*[\'"]{0,1}([#a-zA-Z0-9]+)/i', $rs[$i]['title'], $c)) {

                            // If we find a hex equivalent for found color definition (if it's not already in hex format)
                            if ($hex = Indi::hexColor($c[1])) {

                                // Strip html from row title
                                $rs[$i]['title'] = strip_tags($rs[$i]['title']);

                                // Capture color, for being available to setup as a font color
                                $color = ltrim($hex, '#');
                            }
                        }

                        // Write row title
                        $objSelfStyled = $objRichText->createTextRun($rs[$i]['title']);
                        $objSelfStyled->getFont()->setName($font)->setSize('8');
                        $objSelfStyled->getFont()->getColor()->setRGB($color);

                        // Write separator, if needed
                        if ($i < count($rs) - 1) {
                            $objSelfStyled = $objRichText->createTextRun(', ');
                            $objSelfStyled->getFont()->setName($font)->setSize('8');
                            $objSelfStyled->getFont()->getColor()->setRGB('7EAAE2');
                        }
                    }

                    // If filter type if also combo, but keys are from 'enumset' table
                } else if ($excelI['fieldId']) {

                    // Prepare the array of keys
                    if (!is_array($excelI['value'])) $excelI['value'] = array($excelI['value']);

                    // Fetch rows by keys
                    $rs = Indi::model('Enumset')->fetchAll(array(
                        '`fieldId` = "' . $excelI['fieldId'] . '"',
                        '`alias` IN ("' . implode('","', $excelI['value']) . '")'
                    ))->toArray();

                    // Foreach row
                    for ($i = 0; $i < count($rs); $i++) {

                        // Set default color
                        $color = '7EAAE2';

                        // Check if row title contains color definition
                        if (preg_match('/color[:=][ ]*[\'"]{0,1}([#a-zA-Z0-9]+)/i', $rs[$i]['title'], $c)) {

                            // If we find a hex equivalent for found color definition (if it's not already in hex format)
                            if ($hex = Indi::hexColor($c[1])) {

                                // Strip html from row title
                                $rs[$i]['title'] = strip_tags($rs[$i]['title']);

                                // Capture color, for being available to setup as a font color
                                $color = ltrim($hex, '#');
                            }
                        }

                        // Write row title
                        $objSelfStyled = $objRichText->createTextRun($rs[$i]['title']);
                        $objSelfStyled->getFont()->setName($font)->setSize('8');
                        $objSelfStyled->getFont()->getColor()->setRGB($color);

                        // Write separator, if needed
                        if ($i < count($rs) - 1) {
                            $objSelfStyled = $objRichText->createTextRun(', ');
                            $objSelfStyled->getFont()->setName($font)->setSize('8');
                            $objSelfStyled->getFont()->getColor()->setRGB('7EAAE2');
                        }
                    }

                    // Else if filter type is 'text' or 'html' (simple text search)
                } else {

                    // Write the filter value
                    $objSelfStyled = $objRichText->createTextRun($excelI['value']);
                    $objSelfStyled->getFont()->setName($font)->setSize('8');
                    $objSelfStyled->getFont()->getColor()->setRGB('7EAAE2');
                }

                // Set rich text object as a cell value
                $objPHPExcel->getActiveSheet()->SetCellValue('A' . $currentRowIndex, $objRichText);
                if (Indi::uri()->format == 'pdf') $objPHPExcel->getActiveSheet()->getStyle('A' . $currentRowIndex)->applyFromArray($noBorder);

                // Here we set row height, because OpenOffice Writer (unlike Excel) ignores previously setted default height
                $objPHPExcel->getActiveSheet()->getRowDimension($currentRowIndex)->setRowHeight(15.75);

                // Increment current row index as we need to keep it actual after each new row added to the spreadsheet
                $currentRowIndex++;
            }
        }

        // Append row with keyword, if keyword search was used
        if (Indi::get()->keyword) {

            // Setup new rich text object for keyword search usage mention
            $objRichText = new PHPExcel_RichText();

            // Merge current row sells and set alignment as 'right'
            $objPHPExcel->getActiveSheet()->mergeCells('A' . $currentRowIndex . ':' . $lastColumnLetter . $currentRowIndex);
            $objPHPExcel->getActiveSheet()->getStyle('A' . $currentRowIndex)->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

            // Write the keyword search method title, with a separator
            $objSelfStyled = $objRichText->createTextRun('Искать -» ');
            $objSelfStyled->getFont()->setName($font)->setSize('8');
            $objSelfStyled->getFont()->getColor()->setRGB('04408C');

            // Write used keyword
            $objSelfStyled = $objRichText->createTextRun(urldecode(Indi::get()->keyword));
            $objSelfStyled->getFont()->setName($font)->setSize('8');
            $objSelfStyled->getFont()->getColor()->setRGB('7EAAE2');

            // Set rich text object as cell value
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $currentRowIndex, $objRichText);

            // Set no-border style
            if (Indi::uri()->format == 'pdf') $objPHPExcel->getActiveSheet()->getStyle('A' . $currentRowIndex)->applyFromArray($noBorder);

            // Here we set row height, because OpenOffice Writer (unlike Excel) ignores previously setted default height
            $objPHPExcel->getActiveSheet()->getRowDimension($currentRowIndex)->setRowHeight(15.75);

            // Increment current row index as we need to keep it actual after each new row added to the spreadsheet
            $currentRowIndex++;


        // If no keyword search was used, but number of filters, involved in search is more than 1
        // we provide an empty row, as a separator between filters mentions and found data
        } else if (is_array($this->_excelA) && count($this->_excelA) > 1) {

            // Here we set row height, because OpenOffice Writer (unlike Excel) ignores previously setted default height
            $objPHPExcel->getActiveSheet()->getRowDimension($currentRowIndex)->setRowHeight(15.75);

            // Set no-border style
            if (Indi::uri()->format == 'pdf') {

                // Merge all cell within that separator-row before no-border-style apply
                $objPHPExcel->getActiveSheet()->mergeCells('A' . $currentRowIndex . ':' . $lastColumnLetter . $currentRowIndex);

                // Apply no-border style
                $objPHPExcel->getActiveSheet()->getStyle('A' . $currentRowIndex)->applyFromArray($noBorder);
            }

            // Increment current row index
            $currentRowIndex++;
        }

        // Get the order column alias and direction
        $order = @array_shift(json_decode(Indi::get()->sort));

        // For each column
        foreach ($columnA as $n => $columnI) {

            // Get column letter
            $columnL = PHPExcel_Cell::stringFromColumnIndex($n);

            // Setup column width
            $m = Indi::uri()->format == 'excel' ? 8.43 : 6.4;
            $objPHPExcel->getActiveSheet()->getColumnDimension($columnL)->setWidth(ceil($columnI['width']/$m));

            // Replace &nbsp;
            $columnI['title'] = str_replace('&nbsp;', ' ', $columnI['title']);

            // Write header title of a certain column to a header cell
            $objPHPExcel->getActiveSheet()->SetCellValue($columnL . $currentRowIndex, $columnI['title']);

            if ($columnI['dataIndex'] == $order->property) {

                // Create the GD canvas image for hue background and thumbs to be placed there
                $canvasIm = imagecreatetruecolor(13, 5);
                imagecolortransparent($canvasIm, imagecolorallocate($canvasIm, 0, 0, 0));

                // Pick hue bg and place it on canvas
                $iconFn = DOC . STD . '/core/library/extjs4/resources/themes/images/default/grid/sort_' . strtolower($order->direction) . '.gif';
                $iconIm = imagecreatefromgif($iconFn);
                imagecopy($canvasIm, $iconIm, 0, 0, 0, 0, 13, 5);

                //  Add the GD image to a spreadsheet
                $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
                $objDrawing->setCoordinates($columnL . $currentRowIndex);
                $objDrawing->setImageResource($canvasIm);
                $objDrawing->setWidth(13)->setHeight(5)->setOffsetY(10)->setOffsetX($columnI['titleWidth'] + 6);
                $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
            }

            // Apply header row style
            $objPHPExcel->getActiveSheet()
                ->getStyle($columnL . $currentRowIndex)
                ->applyFromArray(
                array(
                    'borders' => array(
                        'right' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array(
                                'rgb' => ($columnI['dataIndex'] == $order->property ? 'aaccf6':'c5c5c5')
                            )
                        ),
                        'top' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array(
                                'rgb' => ($columnI['dataIndex'] == $order->property ? 'BDD5F1':'d5d5d5')
                            )
                        ),
                        'bottom' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array(
                                'rgb' => ($columnI['dataIndex'] == $order->property ? 'A7C7EE':'c5c5c5')
                            )
                        ),
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                        'rotation' => 90,
                        'startcolor' => array(
                            'rgb' => ($columnI['dataIndex'] == $order->property ? 'ebf3fd' : 'F9F9F9'),
                        ),
                        'endcolor' => array(
                            'rgb' => ($columnI['dataIndex'] == $order->property ? 'd9e8fb' : 'E3E4E6'),
                        ),
                    )
                )
            );

            // Apply style for first cell within header row
            if (!$n && Indi::uri()->format == 'pdf') $objPHPExcel->getActiveSheet()->getStyle($columnL . $currentRowIndex)
                ->applyFromArray(array(
                    'borders' => array(
                        'left' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array(
                                'rgb' => ($columnI['dataIndex'] == $order->property ? 'BDD5F1':'d5d5d5')
                            )
                        ),
                    )
                )
            );

            // Apply align for all rows within current column, except header rows
            $objPHPExcel->getActiveSheet()
                ->getStyle($columnL . ($currentRowIndex + 1) . ':' . $columnL . $lastRowIndex)
                ->applyFromArray(array(
                    'alignment' => array(
                        'horizontal' => $columnI['align']
                    )
                )
            );
        }

        // Here we set row height, because OpenOffice Writer (unlike Excel) ignores previously setted default height
        $objPHPExcel->getActiveSheet()->getRowDimension($currentRowIndex)->setRowHeight(15.75);
        $currentRowIndex++;

        // We remember a current row index at this moment, because it is the index which data rows are starting from
        $dataStartAtRowIndex = $currentRowIndex;

        // Foreach item in $data array
        for ($i = 0; $i < count($data); $i++) {

            // Here we set row height, because OpenOffice Writer (unlike Excel) ignores previously setted default height
            $objPHPExcel->getActiveSheet()->getRowDimension($currentRowIndex)->setRowHeight(15.75);

            // Foreach column
            foreach ($columnA as $n => $columnI) {

                // Convert the column index to excel column letter
                $columnL = PHPExcel_Cell::stringFromColumnIndex($n);

                // Get the value
                $value = $data[$i][$columnI['dataIndex']];

                // If cell value contain a .i-color-box item, we replaced it with same-looking GD image box
                if (preg_match('/<span class="i-color-box" style="[^"]*background:\s*([^;]+);">/', $value, $c)) {

                    // If color was detected
                    if ($h = trim(Indi::hexColor($c[1]), '#')) {

                        // Create the GD image
                        $gdImage = @imagecreatetruecolor(14, 11) or die('Cannot Initialize new GD image stream');
                        imagefill($gdImage, 0, 0, imagecolorallocate(
                            $gdImage, hexdec(substr($h, 0, 2)), hexdec(substr($h, 2, 2)), hexdec(substr($h, 4, 2)))
                        );

                        // Setup additional x-offset for color-box, for it to be centered within the cell
                        $additionalOffsetX = ceil(($columnI['width']-14)/2) - 2;

                        //  Add the image to a worksheet
                        $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
                        $objDrawing->setCoordinates($columnL . $currentRowIndex);
                        $objDrawing->setImageResource($gdImage);
                        $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
                        $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
                        $objDrawing->setHeight(11);
                        $objDrawing->setWidth(14);
                        $objDrawing->setOffsetY(5)->setOffsetX($additionalOffsetX);
                        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

                        // Replace .i-color-box item from value, and prepend it with 6 spaces to provide an indent,
                        // because gd image will override cell value otherwise
                        $value = str_pad('', 6, ' ') . strip_tags($value);
                    }

                // Else if cell value contain a color definition within 'color' attribute,
                // or as a 'color: xxxxxxxx' expression within 'style' attribute, we extract that color definition
                } else if (preg_match('/color[:=][ ]*[\'"]{0,1}([#a-zA-Z0-9]+)/i', $value, $c)) {

                    // If we find a hex equivalent for found color definition (if it's not already in hex format)
                    if ($hex = Indi::hexColor($c[1]))

                        // Set cell's color
                        $objPHPExcel->getActiveSheet()->getStyle($columnL . $currentRowIndex)
                            ->getFont()->getColor()->setRGB(ltrim($hex, '#'));
                }

                // If cell contains an 'a' tag with href attribute, we set a hyperlink to current cell
                if (preg_match('/<a/', $value) && preg_match('/href="([^"]+)"/', $value, $a)) {


                    // If there is no protocol definition within href, we set it as 'http://'
                    $protocol = preg_match('/:\/\//', $a[1]) ? '' : 'http://';

                    // If href start with a '/', it means that there is no hostname specified, so we define default
                    $server = preg_match('/^\/[^\/]{0,1}/', $a[1]) ? $_SERVER['HTTP_HOST'] : '';

                    // Prepend href with protocol and hostname
                    $a[1] = $protocol . $server . $a[1];

                    // Set cell value as hyperlink
                    $objPHPExcel->getActiveSheet()->getCell($columnL . $currentRowIndex)->getHyperlink()->setUrl($a[1]);
                    $objPHPExcel->getActiveSheet()->getStyle($columnL . $currentRowIndex)->getFont()->setUnderline(true);
                }

                // Strip html content from $value
                $value = strip_tags($value);

                // Replace some special characters definitions to its actual symbols
                $value = str_replace(
                    array('&nbsp;','&laquo;','&raquo;','&mdash;','&quot;','&lt;','&gt;'),
                    array(' ','«','»','—','"','<','>'), $value);

                // Set right and bottom border, because cell fill will hide default Excel's ot OpenOffice Write's cell borders
                $objPHPExcel->getActiveSheet()
                    ->getStyle($columnL . $currentRowIndex)
                    ->applyFromArray(
                    array(
                        'borders' => array(
                            'right' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN,
                                'color' => array(
                                    'rgb' => 'D0D7E5'
                                )
                            ),
                            'bottom' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN,
                                'color' => array(
                                    'rgb' => 'D0D7E5'
                                )
                            )
                        )
                    )
                );

                // Apply style for first cell within header row
                if (!$n && Indi::uri()->format == 'pdf') $objPHPExcel->getActiveSheet()->getStyle($columnL . $currentRowIndex)
                    ->applyFromArray(array(
                        'borders' => array(
                            'left' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN,
                                'color' => array(
                                    'rgb' => 'c5c5c5'
                                )
                            ),
                        )
                    )
                );

                // Get the control element
                $el = Indi::trail()->model->fields($columnI['dataIndex'])->foreign('elementId')->alias;

                // If control element is 'price' or 'number'
                if (in($el, 'price,number')) {

                    // Display zero-values only if `displayZeroes` flag for current column is `true`
                    if ($value == 0 && !$columnI['displayZeroes']) $value = '';

                    // Set format
                    if ($el == 'price') $objPHPExcel->getActiveSheet()->getStyle($columnL . $currentRowIndex)->getNumberFormat()
                        ->setFormatCode('#,##0.00');
                    else $objPHPExcel->getActiveSheet()->getStyle($columnL . $currentRowIndex)->getNumberFormat()
                        ->setFormatCode('#,##0');

                }

                // Set cell value
                $objPHPExcel->getActiveSheet()->SetCellValue($columnL . $currentRowIndex, $value);

                // Set odd-even rows background difference
                if ($i%2) {
                    $objPHPExcel->getActiveSheet()
                        ->getStyle($columnL . $currentRowIndex)
                        ->getFill()
                        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('FAFAFA');
                }
            }

            // Increment current row index;
            $currentRowIndex++;
        }

        // Apply right border for the most right column
        $objPHPExcel->getActiveSheet()
            ->getStyle($lastColumnLetter . $dataStartAtRowIndex . ':' . $lastColumnLetter . $lastRowIndex)
            ->applyFromArray(array(
                'borders' => array(
                    'right' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array(
                            'rgb' => 'c5c5c5'
                        )
                    )
                )
            )
        );

        // Apply last row style (bottom border)
        $objPHPExcel->getActiveSheet()
            ->getStyle('A' . (count($data) + $dataStartAtRowIndex - 1) . ':' . $columnL . (count($data) + $dataStartAtRowIndex - 1))
            ->applyFromArray(
            array(
                'borders' => array(
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array(
                            'rgb' => 'c5c5c5'
                        )
                    )
                )
            )
        );

        // Append summary row
        if ($summary = $this->rowsetSummary()) {

            // Foreach column
            foreach ($columnA as $n => $columnI) {

                // Convert the column index to excel column letter
                $columnL = PHPExcel_Cell::stringFromColumnIndex($n);

                // Get the value
                $value = $summary->{$columnI['dataIndex']};

                // Get the control element
                $el = Indi::trail()->model->fields($columnI['dataIndex'])->foreign('elementId')->alias;

                // If control element is 'price' or 'number'
                if (in($el, 'price,number')) {

                    // Display zero-values only if `displayZeroes` flag for current column is `true`
                    if ($value == 0 && !$columnI['displayZeroes']) $value = '';

                    // Set format
                    if ($el == 'price') $objPHPExcel->getActiveSheet()->getStyle($columnL . $currentRowIndex)->getNumberFormat()
                        ->setFormatCode('#,##0.00');
                    else $objPHPExcel->getActiveSheet()->getStyle($columnL . $currentRowIndex)->getNumberFormat()
                        ->setFormatCode('#,##0');

                }

                // Set cell value
                $objPHPExcel->getActiveSheet()->SetCellValue($columnL . $currentRowIndex, $value);

                // Set up no-border style
                if (Indi::uri()->format == 'pdf') $objPHPExcel->getActiveSheet()->getStyle($columnL . (count($data) + $dataStartAtRowIndex - 1 + 1))
                    ->applyFromArray($noBorder)
                    ->applyFromArray(array(
                    'borders' => array(
                        'bottom' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array(
                                'rgb' => 'ffffff'
                            )
                        )
                    )
                ));
            }

            // Increment current row index
            $currentRowIndex++;
        }

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle(Indi::trail()->section->title);

        // Freeze header
        $objPHPExcel->getActiveSheet()->freezePane('A' . ($dataStartAtRowIndex));

        // Primary Excel document custom adjustments
        $this->adjustExport($objPHPExcel);

        // Apply adjustments, especially for a given kind of format
        if (in(Indi::uri()->format, 'pdf,excel')) $this->{'adjust' . ucfirst(Indi::uri()->format) . 'Export'}($objPHPExcel);

        // Possible formats details
        $formatCfg = array(
            'excel' => array(
                'ext' => 'xlsx',
                'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'writer' => 'Excel2007'
            ),
            'pdf' => array(
                'ext' => 'pdf',
                'mime' => 'application/pdf',
                'writer' => 'PDF_DomPDF',
                'renderer' => PHPExcel_Settings::PDF_RENDERER_DOMPDF
            ),
        );

        // Output
        $file = $this->exportFname() . '.' . $formatCfg[$format]['ext'];
        if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT'])) $file = iconv('utf-8', 'windows-1251', $file);
        header('Content-Type: ' . $formatCfg[$format]['mime']);
        header('Content-Disposition: attachment; filename="' . $file . '"');

        // Load PHPExcel's IO Factory class
        require_once 'PHPExcel/IOFactory.php';

        // If export format is 'pdf'
        if ($format == 'pdf') {

            // Setup $rName and $rPath
            $rName = $formatCfg[$format]['renderer'];
            $rPath = DOC . STD . '/core/library/' . $rName;

            // Try to set up pdf renderer
            if (!PHPExcel_Settings::setPdfRenderer($rName, $rPath))
                jflush(false, 'Can\'t set up pdf renderer with such name and/or path');
        }

        // Create writer
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $formatCfg[$format]['writer']);

        // Buffer raw data
        ob_start(); $objWriter->save('php://output'); $raw = ob_get_clean();

        // Flush Content-Length header
        header('Content-Length: ' . strlen($raw));

        // Flush raw
        echo $raw;

        // Exit
        die();
    }

    /**
     * Default export file name builder
     */
    public function exportFname() {
        return Indi::trail()->section->title;
    }

    /**
     * Export file name builder for excel spreadsheets
     */
    public function exportFnameExcel() {
        return $this->exportFname();
    }

    /**
     * Export file name builder for pdf documents
     */
    public function exportFnamePdf() {
        return $this->exportFname();
    }

    /**
     * Empty function. To be redeclared in child classes in case of a need for an export document adjustments
     *
     * @param $objPHPExcel
     */
    public function adjustExport(&$objPHPExcel) {

    }

    /**
     * Empty function. To be redeclared in child classes in case of a need for an excel-export document adjustments
     *
     * @param $objPHPExcel
     */
    public function adjustExcelExport(&$objPHPExcel) {

    }

    /**
     * Empty function. To be redeclared in child classes in case of a need for an pdf-export document adjustments
     *
     * @param $objPHPExcel
     */
    public function adjustPdfExport(&$objPHPExcel) {

    }

    /**
     * Empty function. To be redeclared in child classes in case of a need for an json-export adjustments
     *
     * @param $json
     */
    public function adjustJsonExport(&$json) {

    }

    /**
     * Try to find user data in certain place (database table), identified by $place argument
     *
     * @param $username
     * @param $password
     * @param string $place
     * @param null $profileId
     * @param array $level1ToggledSectionIdA
     * @return array|mixed
     */
    protected function _findSigninUserData($username, $password, $place = 'admin', $profileId = null,
                                           $level1ToggledSectionIdA = array()) {

        $profileId = Indi::model($place)->fields('profileId') ? '`a`.`profileId`' : '"' . $profileId . '"';
        $adminToggle = Indi::model($place)->fields('toggle') ? '`a`.`toggle` = "y"' : '1';
        return Indi::db()->query('
            SELECT
                `a`.*,
                `a`.`password` = "' . $password . '"
                    OR `a`.`password` = PASSWORD("' . $password . '")
                    OR `a`.`password` = OLD_PASSWORD("' . $password . '")
                        AS `passwordOk`,
                '. $adminToggle . ' AS `adminToggle`,
                `p`.`toggle` = "y" AS `profileToggle`,
                `p`.`title` AS `profileTitle`,
                COUNT(`sa`.`sectionId`) > 0 AS `atLeastOneSectionAccessible`
            FROM `' . $place . '` `a`
                LEFT JOIN `profile` `p` ON (`p`.`id` = ' . $profileId . ')
                LEFT JOIN `section2action` `sa` ON (
                    FIND_IN_SET(' . $profileId . ', `sa`.`profileIds`)
                    AND `sa`.`actionId` = "1"
                    AND `sa`.`toggle` = "y"
                    AND FIND_IN_SET(`sa`.`sectionId`, "' . implode(',', $level1ToggledSectionIdA) . '")
                )
            WHERE `a`.`email` = "' . $username . '"
            LIMIT 1
        ')->fetch();
    }

    /**
     * Perform first level of authentication - check that:
     * 1. User exists
     * 2. Password is ok
     * 3. User is not disabled
     * 4. User have a type, that is not disabled
     * 5. There is at least one toggled 'On' section at level 1, that user have access to
     *
     * @param $username
     * @param $password
     * @return array|mixed|string
     */
    private function _authLevel1($username, $password) {

        // Get array of most top toggled 'On' sections ids
        $level0ToggledOnSectionIdA = Indi::db()->query('
            SELECT `id`
            FROM `section`
            WHERE `sectionId` = "0" AND `toggle` = "y"
        ')->fetchAll(PDO::FETCH_COLUMN);

        // Get array of level 1 toggled 'On' sections ids.
        $level1ToggledOnSectionIdA = Indi::db()->query('
            SELECT `id`
            FROM `section`
            WHERE FIND_IN_SET(`sectionId`, "' . implode(',', $level0ToggledOnSectionIdA) . '") AND `toggle` = "y"
        ')->fetchAll(PDO::FETCH_COLUMN);

        // Try to find user data in primary place - `admin` table
        $data = $this->_findSigninUserData($username, $password, 'admin', null, $level1ToggledOnSectionIdA);

        // If not found
        if (!$data['id']) {

            // Get the list of other possible places, there user with given credentials can be found
            $profile2tableA = Indi::db()->query('
                SELECT `e`.`table`, `p`.`id` AS `profileId`
                FROM `entity` `e`, `profile` `p`
                WHERE `p`.`entityId` != "0"
                    AND `p`.`entityId` = `e`.`id`
            ')->fetchAll();

            // Foreach possible place - try to find
            foreach ($profile2tableA as $profile2tableI)
                if (current($data = $this->_findSigninUserData($username, $password, $profile2tableI['table'],
                    $profile2tableI['profileId'], $level1ToggledOnSectionIdA)))
                    break;

            // If found - assign some additional info to found data
            if ($profile2tableI) {
                $data['alternate'] = $profile2tableI['table'];
                $data['profileId'] = $profile2tableI['profileId'];
            }
        }

        // Set approriate error messages if:
        // 1. User data is still not found
        if (!$data) $error = I_LOGIN_ERROR_NO_SUCH_ACCOUNT;

        // 2. Given password is wrong
        else if (!$data['passwordOk']) $error = I_LOGIN_ERROR_WRONG_PASSWORD;

        // 3. User's signin ability is turned off
        else if (!$data['adminToggle']) $error = I_LOGIN_ERROR_ACCOUNT_IS_OFF;

        // 4. User's profile is turned off (So all users with such profile are unable to signin)
        else if (!$data['profileToggle']) $error = I_LOGIN_ERROR_PROFILE_IS_OFF;

        // 5. User have no accessbile sections
        else if (!$data['atLeastOneSectionAccessible']) $error = I_LOGIN_ERROR_NO_ACCESSIBLE_SECTIONS;

        return $error ? $error : $data;
    }

    /**
     * Check is user has access to perform $action action within $section section. Checks include:
     * 1. Section exists
     * 2. Section is switched on
     * 3. Action exists
     * 4. Action is switched on
     * 5. Action exists within section
     * 6. Action is switched in within section
     * 7. User has access to action within section
     * 8. Section is a child of a section that is switched on, and all parents up to the top are switched on too
     *
     * @param $section
     * @param $action
     * @return array|mixed|string
     */
    private function _authLevel2($section, $action) {

        // If action name is not valid - return an error message
        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9]*$/', $action)) return I_URI_ERROR_ACTION_FORMAT;

        // Try to find use data
        $data = Indi::db()->query('
            SELECT
                `s`.`id`,
                `s`.`toggle` = "y" AS `sectionToggle`,
                `a`.`id` > 0 AS `actionExists`,
                `a`.`toggle` = "y" AS `actionToggle`,
                `sa`.`id` > 0 AS `section2actionExists`,
                `sa`.`toggle` = "y" AS `section2actionToggle`,
                FIND_IN_SET("' . $_SESSION['admin']['profileId'] . '", `sa`.`profileIds`) > 0 AS `granted`,
                `s`.`sectionId` as `sectionId`
            FROM `section` `s`
               LEFT JOIN `action` `a` ON (`a`.`alias` = "' . $action . '")
               LEFT JOIN `section2action` `sa` ON (`sa`.`actionId` = `a`.`id` AND `sa`.`sectionId` = `s`.`id`)
            WHERE 1
                AND `s`.`alias` = "' . $section . '"
                AND `s`.`sectionId` != "0"
        ')->fetch();

        // Set approriate error messages if:
        // 1. Section was not found
        if (!$data) $error = I_ACCESS_ERROR_NO_SUCH_SECTION;

        // 2. Section is switched off
        else if (!$data['sectionToggle']) $error = I_ACCESS_ERROR_SECTION_IS_OFF;

        // 3. Action does not exist at all
        else if (!$data['actionExists']) $error = I_ACCESS_ERROR_NO_SUCH_ACTION;

        // 4. Action is switched off
        else if (!$data['actionToggle']) $error = I_ACCESS_ERROR_ACTION_IS_OFF;

        // 5. Action does not exits in that section
        else if (!$data['section2actionExists']) $error = I_ACCESS_ERROR_NO_SUCH_ACTION_IN_SUCH_SECTION;

        // 6. Action is switched off in that section
        else if (!$data['section2actionToggle']) $error = I_ACCESS_ERROR_ACTION_IS_OFF_IN_SUCH_SECTION;

        // 7. User have no rights on that action in that section
        else if (!$data['granted'] && !Indi::uri('consider')) $error = I_ACCESS_ERROR_ACTION_IS_NOT_ACCESSIBLE;

        // 8. One of parent sections for current section - is switched off
        else {

            // Start fulfil section id stack
            $this->_routeA = array($data['id'], $data['sectionId']);

            // Setup initial id of parent section
            $parent = array('sectionId' => $data['sectionId']);

            // Navigate through parent sections up to the root
            while ($parent = Indi::db()->query('
                SELECT `sectionId`, `toggle` FROM `section` WHERE `id` = "' . $parent['sectionId'] . '" LIMIT 1
            ')->fetch()) {

                // If any of parent sections if switched off - setup an error and break the loop
                if ($parent['toggle'] == 'n') {
                    $error = I_ACCESS_ERROR_ONE_OF_PARENT_SECTIONS_IS_OFF;
                    break;

                // Else push new item in $this->_routeA stack
                } else if ($parent['sectionId']) $this->_routeA[] = $parent['sectionId'];

                // Else stop loop, as $parent['sectionId'] = 0, so there is no sense to find a section with such an `id`
                else break;
            }
        }

        // If $error was set - return error, or return $data otherwise
        return $error ? $error : $data;
    }

    /**
     * Perform authentication by 3 levels
     */
    public function auth() {

        // If visitor is a visitor, e.g. he has not signed in yet
        if (!$_SESSION['admin']) {

            // If 'consider' param is passed within the uri
            if (Indi::uri('consider')) {

                // Do the second level access check
                $data = $this->_authLevel2(Indi::uri()->section, Indi::uri()->action);

                // If $data is not an array, e.g some error there, output it as json with that error
                if (!is_array($data)) jflush(false, $data);

                // Else go further and perform last auth check, within Indi_Trail_Admin::__construct()
                else Indi::trail($this->_routeA, $this)->authLevel3();

            // Else do ordinary check
            } else {

                // If he is trying to do that
                if (Indi::post()->enter && Indi::uri('section') == 'index' && Indi::uri('action') == 'index') {

                    // If no username given
                    if (!Indi::post()->username) $data = I_LOGIN_ERROR_ENTER_YOUR_USERNAME;

                    // Else if no password given
                    else if (!Indi::post()->password) $data = I_LOGIN_ERROR_ENTER_YOUR_PASSWORD;

                    // Else try to find user's data
                    else $data = $this->_authLevel1(Indi::post()->username, Indi::post()->password);

                    // If $data is not an array, e.g some error there, output it as json with that error
                    if (!is_array($data)) jflush(false, $data);

                    // Else start a session for user and report that sing-in was ok
                    $allowedA = array('id', 'title', 'email', 'password', 'profileId', 'profileTitle', 'alternate');
                    foreach ($allowedA as $allowedI) $_SESSION['admin'][$allowedI] = $data[$allowedI];
                    jflush(true, array('ok' => '1'));
                }

                // If user was thrown out from the system, assign a throwOutMsg to Indi::view() object, for this message
                // to be available for picking up and usage as Ext.MessageBox message, as a reason of throw out
                if ($_SESSION['indi']['throwOutMsg']) {
                    Indi::view()->throwOutMsg = $_SESSION['indi']['throwOutMsg'];
                    unset($_SESSION['indi']['throwOutMsg']);
                }

                // Render login page
                $out = Indi::view()->render('login.php');

                // Do paths replacements, if current project runs within webroot subdirectory
                if (STD) {
                    $out = preg_replace('/(<link[^>]+)(href)=("|\')\//', '$1$2=$3' . STD . '/', $out);
                    $out = preg_replace('/(<script[^>]+)(src)=("|\')\//', '$1$2=$3' . STD . '/', $out);
                    $out = preg_replace('/(<img[^>]+)(src)=("|\')\//', '$1$2=$3' . STD . '/', $out);
                }

                // Flush the login page
                die($out);
            }

        // Else if user is already signed in, and is trying to perform some action in some section
        } else {

            // Do the first level access check
            $data = $this->_authLevel1($_SESSION['admin']['email'], $_SESSION['admin']['password']);

            // If $data is not an array, e.g some error there, output it as json with that error
            if (!is_array($data)) {

                // Change the error message to it's version for case then user was thrown out
                if ($data == I_LOGIN_ERROR_NO_SUCH_ACCOUNT) $data = I_THROW_OUT_ACCOUNT_DELETED;
                else if ($data == I_LOGIN_ERROR_WRONG_PASSWORD) $data = I_THROW_OUT_PASSWORD_CHANGED;
                else if ($data == I_LOGIN_ERROR_ACCOUNT_IS_OFF) $data = I_THROW_OUT_ACCOUNT_IS_OFF;
                else if ($data == I_LOGIN_ERROR_PROFILE_IS_OFF) $data = I_THROW_OUT_PROFILE_IS_OFF;
                else if ($data == I_LOGIN_ERROR_NO_ACCESSIBLE_SECTIONS) $data = I_THROW_OUT_NO_ACCESSIBLE_SECTIONS;

                // Save error message in session, for ability to display it in message box
                $_SESSION['indi']['throwOutMsg'] = $data;

                // Logout
                if (Indi::uri()->section == 'index') die(header('Location: ' . PRE . '/logout/'));
                else if (!Indi::uri()->format) die('<script>top.window.location="' . PRE .'/logout/"</script>');
                else jflush(false, array('trowOutMsg' => $data));

            // Else if current section is 'index', e.g we are in the root of interface
            } else if (Indi::uri()->section != 'index') {

                // Do the second level access check
                $data = $this->_authLevel2(Indi::uri()->section, Indi::uri()->action);

                // If $data is not an array, e.g some error there, output it as json with that error
                if (!is_array($data)) jflush(false, $data);

                // Else go further and perform last auth check, within Indi_Trail_Admin::__construct()
                else Indi::trail($this->_routeA, $this)->authLevel3();
            }
        }

        // If current request had a only aim to check access - report that all is ok
        if (array_key_exists('check', Indi::get())) die('ok');
    }

    /**
     * Provide default index action
     */
    public function indexAction() {

        // If data should be got as json or excel
        if (Indi::uri('format') || (!$this->_isRowsetSeparate && Indi::trail(true))) {

            // Adjust rowset, before using it as a basement of grid data
            $this->adjustGridDataRowset();

            // Build the grid data, based on current rowset
            $data = $this->rowset->toGridData(Indi::trail());

            // Adjust grid data
            $this->adjustGridData($data);

            // If data is gonna be used in the excel spreadsheet building process, pass it to a special function
            if (in(Indi::uri('format'), 'excel,pdf')) $this->export($data, Indi::uri('format'));

            // Else if data is needed as json for extjs grid store - we convert $data to json with a proper format and flush it
            else {

                // Get scope
                $scope = Indi::trail()->scope->toArray();

                // Unset tabs definitions from json-encoded scope data, as we'd already got it previously
                unset($scope['actionrowset']['south']['tabs']);

                // Setup basic data
                $pageData = array(
                    'totalCount' => $this->rowset->found(),
                    'blocks' => $data,
                    'scope' => $scope
                );

                // Append summary data
                if ($summary = $this->rowsetSummary()) $pageData['summary'] = $summary;

                // Provide combo filters consistency
                foreach (Indi::trail()->filters as $filter)
                    if ($filter->foreign('fieldId')->relation || $filter->foreign('fieldId')->columnTypeId == 12) {
                        $alias = $filter->foreign('fieldId')->alias;
                        Indi::view()->filterCombo($filter, 'extjs');
                        $pageData['filter'][$alias] = array_pop(Indi::trail()->filtersSharedRow->view($alias));
                    }

                // Adjust json export
                $this->adjustJsonExport($pageData);

                // If uri's 'format' param is specified, and it is 'json' - flush json-encoded $pageData
                if (Indi::uri('format') == 'json') jflush(true, $pageData); 
                
                // Else assign that data into scope's `pageData` prop
                else Indi::trail()->scope->pageData = $pageData;
            }
        }
    }

    /**
     * Calculate summary data to be included in the json output
     *
     * @return mixed
     */
    function rowsetSummary() {

        // If there is no 'summary' key within $_GET params - return
        if (!$summary = Indi::get('summary')) return;

        // If $summary is not json-decodable - return
        if (!($summary = json_decode($summary, true))) return;

        // If all possible results are already fetched, and if section view type is grid - return,
        // as in such sutuation we can fully rely on grid's own summary feature, built on javascript
        if (Indi::trail()->section->rowsOnPage >= Indi::trail()->scope->found && !Indi::trail()->model->treeColumn())
            if ($this->actionCfg['view']['index'] == 'grid' && !in(Indi::uri('format'), 'excel,pdf')) return;

        // Define an array containing extjs summary types and their sql representatives
        $js2sql = array('sum' => 'SUM', 'min' => 'min', 'max' => 'MAX', 'average' => 'AVG');//, 'count' => 'COUNT');

        // Get grid columns aliases
        $cols = Indi::trail()->gridFields->column('alias');

        // Build an array containing sql-function calls for each column, that have a summary to be retrieved for
        foreach ($js2sql as $type => $fn)
            if ($summary[$type])
                foreach ($summary[$type] as $col)
                    if (in($col, $cols))
                        $sql[] = $fn . '(`' . $col . '`) AS `' . $col .'`';

        // If no sql-function calls collected - return
        if (!$sql) return;

        // Build basic sql query for summaries calculation
        $sql = 'SELECT ' . implode(', ', $sql) . ' FROM `' . Indi::trail()->model->table() . '`';

        // Declare array for WHERE clauses stack
        $where = array();

        // If current model has a tree column, and it is not forced to be ignored - append special
        // clause to WHERE-clauses stack for summaries to be calculated only for top-level entries
        if (Indi::trail()->model->treeColumn() && !$this->actionCfg['misc']['index']['ignoreTreeColumn'])
            $where[] = '`' . Indi::trail()->model->treeColumn() . '` = "0"';

        // Append scope's WHERE clause to the stack
        if (strlen(Indi::trail()->scope->WHERE)) $where[] = Indi::trail()->scope->WHERE;

        // Append WHERE clause to that query
        if ($where) $sql .= ' WHERE ' . im($where, ' AND ');

        // Fetch and return calculated summaries
        return Indi::db()->query($sql)->fetchObject();
    }

    /**
     * Adjust rowset, before using it as a basement of grid data. This function is empty here, but may be useful in
     * some situations
     */
    function adjustGridDataRowset() {

    }

    /**
     * Adjust data, that was already prepared for usage in grid. This function is for ability to post-adjustments
     *
     * @param array $data This param is passed by reference
     */
    function adjustGridData(&$data) {

    }

    /**
     * Render the output. If $return argument is true, builded output will be returned instead of flushing into the
     * browser
     *
     * @param bool $return
     * @return mixed|string|void
     */
    public function postDispatch($return = false) {

        // If we are in a root of interface, build the general layout
        if (Indi::uri('section') == 'index' && Indi::uri('action') == 'index') {

            // Setup the left menu
            Indi::view()->menu = Section::menu();

            // Setup info about current logged in cms user
            Indi::view()->admin = $_SESSION['admin']['title'] . ' [' . $_SESSION['admin']['profileTitle']  . ']';

            // Render the layout
            $out = Indi::view()->render('index.php');

        // Else, if we are doing something in a certain section
        } else {

            // Get the action
            $action = Indi::trail()->view(true);

            // If action is an object-instance of Indi_View_Action_Admin class, call render() method,
            // otherwise assume that action is just a view script
            $out = $action instanceof Indi_View_Action_Admin ? $action->render() : $action;
        }

        // Strip '/admin' from $out, if cms-only mode is enabled
        if (COM) $out = preg_replace('/("|\')\/admin/', '$1', $out);

        // Make a src|href replacements, if project is running in a subfolder of document root
        if (STD) {
            $out = preg_replace('/(<link[^>]+)(href)=("|\')\//', '$1$2=$3' . STD . '/', $out);
            $out = preg_replace('/(<a[^>]+)(href)=("|\')\//', '$1$2=$3' . STD . '/', $out);
            $out = preg_replace('/(<script[^>]+)(src)=("|\')\//', '$1$2=$3' . STD . '/', $out);
            $out = preg_replace('/(<img[^>]+)(src)=("|\')\//', '$1$2=$3' . STD . '/', $out);
            $out = preg_replace('/(<form[^>]+)(action)=("|\')\//', '$1$2=$3' . STD . '/', $out);
        }

        // If $return argument is true, return builded data, else
        if ($return) return $out; else {

            // If $out is a json-encoded data - flush content-type header
            if (preg_match('/^[\[{]/', $out)) header('Content-Type: application/json');

            // Flush output
            die($out);
        }
    }

    /**
     * Redirect from non-index action to index action, with taking in attention such things as
     * 1. Parentness
     * 2. Primary hash
     * 3. Row index
     * Or redirect to custom location, if $location argument is given
     *
     * @param string $location
     * @param bool $return
     * @return string
     */
    public function redirect($location = '', $return = false){

        // If $location argument is empty
        if (!$location) {

            // Setup parentness
            if (Indi::trail(1)->row) $id = Indi::trail(1)->row->id;

            // Get scope data
            if (Indi::uri()->ph) $scope = $_SESSION['indi']['admin'][Indi::uri('section')][Indi::uri()->ph];

            // Build uri location for redirect
            $location = '/' . Indi::trail()->section->alias  . '/' .
                ($id ? 'index/id/' . $id . '/' : ($scope ? 'index/' : '')) .
                ($scope['upperHash'] ? 'ph/' . $scope['upperHash'] . '/aix/' . $scope['upperAix'] . '/' : '');

        }

        // Redirect
        if ($return) return $location; else die('<script>window.parent.Indi.load("' . $location . '");</script>');
    }

    /**
     * Toggle current row, and redirect back
     */
    public function toggleAction() {

        // Toggle
        Indi::trail()->row->toggle();

        // Redirect
        $this->redirect();
    }

    /**
     * Do custom things before saveAction() call
     */
    public function postSave() {

    }

    /**
     * Do custom things after saveAction() call
     */
    public function preSave() {

    }

    /**
     * Do custom things before deleteAction() call
     */
    public function preDelete() {

    }

    /**
     * Do custom things after deleteAction() call
     *
     * @param $deleted
     */
    public function postDelete($deleted) {

    }

    /**
     * Save form data
     *
     * @param bool $redirect
     */
    public function saveAction($redirect = true) {

        // Get array of aliases of fields, that are actually represented in database table
        $possibleA = Indi::trail()->model->fields(null, 'columns');

        // Pick values from Indi::post()
        $data = array();
        foreach ($possibleA as $possibleI)
            if (array_key_exists($possibleI, Indi::post()))
                $data[$possibleI] = Indi::post($possibleI);

        // Unset 'move' key from data, because 'move' is a system field, and it's value will be set up automatically
        unset($data['move']);

        // If there was disabled fields defined for current section, we check if default value was additionally set up
        // and if so - assign that default value under that disabled field alias in $data array, or, if default value
        // was not set - drop corresponding key from $data array
        foreach (Indi::trail()->disabledFields as $disabledFieldR)
            foreach (Indi::trail()->fields as $fieldR)
                if ($fieldR->id == $disabledFieldR->fieldId)
                    if (!strlen($disabledFieldR->defaultValue)) unset($data[$fieldR->alias]);
                    else $data[$fieldR->alias] = $disabledFieldR->compiled('defaultValue');

        // If current cms user is an alternate, and if there is corresponding field within current entity structure
        if (Indi::admin()->alternate && in($aid = Indi::admin()->alternate . 'Id', $possibleA))

            // Prevent alternate field to be set via POST, as it was already (properly)
            // set at the stage of trail item row initialization
            unset($data[$aid]);

        // Update current row properties with values from $data array
        $this->row->assign($data);

        // If some of the fields are CKEditor-fields, we shoudl check whether they contain '<img>' and other tags
        // having STD injections at the beginning of 'src' or other same-aim html attributes, and if found - trim
        // it, for avoid problems while possible move from STD to non-STD, or other-STD directories
        $this->row->trimSTDfromCKEvalues();

        // Get the list of ids of fields, that are disabled
        $disabledA = Indi::trail()->disabledFields->column('fieldId');

        // Get the aliases of fields, that are file upload fields, and that are not disabled,
        // and are to be some changes applied on
        $filefields = array();
        foreach (Indi::trail()->fields as $fieldR)
            if ($fieldR->foreign('elementId')->alias == 'upload' && !in_array($fieldR->id, $disabledA))
                if (preg_match('/^m|d$/', Indi::post($fieldR->alias)) || preg_match(Indi::rex('url'), Indi::post($fieldR->alias)))
                    $filefields[] = $fieldR->alias;

        // If we're going to save new row - setup $updateAix flag
        if (!$this->row->id) $updateAix = true;

        // Prepare metadata, related to fileupload fields contents modifications
        $this->row->files($filefields);

        // Do pre-save operations
        $this->preSave();

        // Save the row
        $this->row->save();

        // If current row has been just successfully created
        if ($updateAix && $this->row->id) {

            // If $scope's WHERE clause is not empty
            if (Indi::trail()->scope->WHERE) {

                // Prepare WHERE clause to ensure that newly created row does match all the requirements, that are
                // used for fetching rows that are suitable for displaying in rowset (grid, calendar, etc) panel
                $where = '`id` = "' . $this->row->id . '" AND ' . Indi::trail()->scope->WHERE;

                // Do the check
                $R = Indi::trail()->model->fetchRow($where);

            // Else we assume that there are no requirements for current row to be displayed in rowset panel
            } else $R = $this->row;

            // Here we should do check for row existence, because there can be situation when we have just created
            // a row, but values of some of it's properties do not match the requirements of current scope, and in that
            // case current scope 'aix' and/or 'page' params should not be adjusted
            if ($R) Indi::uri()->aix = Indi::trail()->model
                ->detectOffset(Indi::trail()->scope->WHERE, Indi::trail()->scope->ORDER, $R->id);
        }

        // Setup row index
        $this->setScopeRow();

        // Do post-save operations
        $this->postSave();

        // If 'redirect-url' param exists within post data
        if ($location = Indi::post('redirect-url')) {

            // Get the primary hash either from $location, or from current trail item scope info
            $hash = ($inLocation = preg_match('#/ph/([0-9a-f]+)/#', $location, $matches))
                ? $matches[1]
                : Indi::trail()->scope->hash;

            // Remember the fact that save button was toggled on
            $_SESSION['indi']['admin'][Indi::uri()->section][$hash]['toggledSave'] = true;

            // Chech if $url contains primary hash value
            if ($inLocation) {

                // If it was a new row, that we've just saved
                if (!Indi::uri()->id) {

                    // Increment 'found' scope param
                    $_SESSION['indi']['admin'][Indi::uri()->section][$hash]['found']++;

                    // Replace the null id with id of newly created row
                    $location = str_replace(array('/null/', '//'), '/' . Indi::trail()->row->id . '/', $location);
                }

            // Replace the null id with id of newly created row
            } else if (!Indi::uri()->id) str_replace(array('/null/', '//'), '/' . Indi::trail()->row->id . '/', $location);
        }

        // Prepare response. Here we mention a number of properties, related to saved row, as a proof that row saved ok
        $response = array('title' => $this->row->title(), 'aix' => Indi::uri()->aix, 'id' => $this->row->id);

        // If redirect should be performed, include the location address under 'redirect' key within $response array
        if ($redirect) $response['redirect'] = $this->redirect($location, true);

        // Flush response
        jflush(true, $response);
    }

    /**
     * Function return a sql-string containing a WHERE clause, that do especially provide an ability to deal with
     * childs-by-parent logic, mean that if current section have parent section, we should fetch only records,
     * related to parent row, for example if we want to see cities, we must define in WHAT country these cities
     * are located
     *
     * @return string|null
     */
    public function parentWHERE() {

        // If current section does not have a parent section, or have, but is a root section - return
        if (!Indi::trail(1)->section->sectionId) return;

        // We check if a non-standard parent connector field name should be used to fetch childs
        // For example, if we have 'Countries' section (displayed rows a fetched from 'country' db table)
        // and 'Cities' section (displayed rows a fetched from 'city' db table) and 'city' table have a column
        // where country identifier of each city is specified, but this column is not named (for some reason)
        // as 'countryId', and we need it to have some another name - so in that cases we use parentSectionConnector
        // logic.
        $connectorAlias = Indi::trail()->section->parentSectionConnector
            ? Indi::trail()->section->foreign('parentSectionConnector')->alias
            : Indi::trail(1)->model->table() . 'Id';

        // Get the connector value. For 'jump' uris, we we are getting it as is - from a certain row's prop
        $connectorValue = Indi::uri('action') == 'index'
            ? Indi::uri('id')
            : (Indi::uri('jump')
                ? Indi::trail()->model->fetchRow('`id` = "' . Indi::uri('id') . '"')->$connectorAlias
                : $_SESSION['indi']['admin']['trail']['parentId'][Indi::trail(1)->section->id]);

        // Return clause
        return Indi::trail()->model->fields($connectorAlias)->storeRelationAbility == 'many'
            ? 'FIND_IN_SET("' . $connectorValue . '", `' . $connectorAlias . '`)'
            : '`' . $connectorAlias . '` = "' . $connectorValue . '"';
    }

    /**
     * Do custom actions configuration adjustments. This can be useful in case then there is a need in
     * some new action, so this function is useful to specify mode and view for that new action/actions
     */
    public function adjustActionCfg() {

    }

    /**
     * This function is an injection that allows to adjust disabled fields before they take effect,
     * so if there is a need to add some fields to the list of disabled, or exclude some - this should
     * be done within this function's body, using appendDisabledField() and excludeDisabledFields() methods
     */
    public function adjustDisabledFields() {

    }

    /**
     * Append the field, identified by $alias, to the list of disabled fields
     *
     * @param string $alias Field name/alias
     * @param bool $displayInForm Whether or not field should be totally disabled, or disabled but however visible
     * @param string $defaultValue The default value for the disabled field
     */
    public function appendDisabledField($alias, $displayInForm = false, $defaultValue = '') {

        // Append
        Indi::trail()->disabledFields->append(array(
            'id' => 0,
            'sectionId' => Indi::trail()->section->id,
            'fieldId' => Indi::trail()->model->fields($alias)->id,
            'defaultValue' => $defaultValue,
            'displayInForm' => $displayInForm ? 1 : 0,
        ));
    }

    /**
     * Exclude field/fields from the list of disabled fields by their aliases/names
     *
     * @param string $fields Comma-separated list of fields's aliases to be excluded from the list of disabled fields
     */
    public function excludeDisabledFields($fields) {

        // Convert $fields argument into an array
        $fieldA_alias = ar($fields);

        // Get the ids
        $fieldA_id = Indi::trail()->fields->select($fieldA_alias, 'alias')->column('id');

        // Exclude
        Indi::trail()->disabledFields->exclude($fieldA_id, 'fieldId');
    }

    /**
     * Do auth for selected row, assuming it is a row of `User` model
     */
    public function loginAction() {

        // Force signin for selected user
        if (Indi::trail()->model->table() == 'user') $_SESSION['user'] = $this->row->toArray();

        // Redirect
        $this->redirect();
    }

    /**
     * Default 'print' action
     */
    public function printAction() {
        Indi::trail()->view->mode = 'view';
    }

    /**
     * Call the desired action method
     */
    public function call($action) {

        // If no trail - call action and return
        if (!Indi::trail(true)) return $this->{$action . 'Action'}();

        // Adjust access rights ()
        $this->adjustAccess();

        // If only row creation is allowed, but now we deal with existing row - prevent it from being saved
        if (Indi::trail()->section->disableAdd == 2 && Indi::trail()->row->id) $this->deny('save');

        // If action was not excluded from the list of allowed actions - call it
        if (Indi::trail()->actions->select($action, 'alias')->at(0)) $this->{$action . 'Action'}();

        // Else flush an error message
        else jflush(false, sprintf(I_ACCESS_ERROR_ACTION_IS_OFF_DUETO_CIRCUMSTANCES, Indi::model('Action')->fetchRow('`alias` = "' . $action . '"')->title));
    }

    /**
     * Empty function. To be overridden in child classes, if there is a need to adjust
     * access mode consider to certain circumstances
     */
    public function adjustAccess() {

    }

    /**
     * Deny actions, enumerated within $actions argument, from being called
     *
     * @param $actions
     */
    public function deny($actions) {

        // If 'create' action is in the list of actions to be denied
        if (in('create', $actions)) {

            // Convert $actions arg to an array
            $actions = ar($actions);

            // Setup current section's `disableAdd` property as 1
            Indi::trail()->section->disableAdd = 1;

            // If we deal with a new row - ensure it wouldn't be saved, and creation form wouldn't even be displayed
            if (Indi::trail()->row && !Indi::trail()->row->id) {
                $actions[] = 'save';
                $actions[] = 'form';
            }
        }

        // Apply exclusions to the list of allowed actions
        Indi::trail()->actions->exclude($actions, 'alias');
    }

    /**
     * This function is used in case if user wants to jump from to some location (within the interface), that is
     * represented by a section, that can't be accessed via menu, and can only be accessed by step-by-step
     * navigation to a certain level of deepness within the system. For example, we have countries, regions, and cities
     * sections and each of them is a subsection within the previous. Usually, if user wants to go to the list of cities
     * within 'Region 1', he should click on the 'Countries' menu item at first, then select 'Country 1' in the list of
     * countries, then click 'Regions' (so all regions within 'Country 1' are displayed), then select 'Region 1',
     * then click 'Cities' - and only after that user will be able to view all cities within 'Region 1'. At all points
     * within this navigation history, system is remembering each list's parameters, such as sorting, index of a row for
     * 'Country 1' and 'Region 1' within the list they were displayed', and a number of other params, for ability,
     * for example, to highlight 'Country 1' if user occasionally will return back to countries list. But if user wants
     * to go directly to cities list within 'Region 1', system doesn't remembering all mentioned bread-crumb trail params,
     * because it usually do it separately at each of all those navigation steps, that are all skipped in our case. So,
     * this function allows to simulate these steps wouldn't be skipped
     *
     * @return mixed
     */
    public function jump() {

        // If uri has no 'jump' param - return
        if (!Indi::uri('jump')) return;

        // Backup current trail's items
        $ti = Indi_Trail_Admin::$items;

        // Get the navigation steps
        $nav = Indi::trail(true)->nav();

        // Set up $_GET 'jump' param, that will tell controllers to perform only preDispatch() call
        Indi::get('jump', 1); $ph = ''; $aix = '';

        // Walk through hierarchy, setup scopes for each step as if user would manually navigate to each uri subsequently
        for ($i = 0; $i < count($nav); $i++) {

            // Append primary hash and row index (none of them will be appended at first-iteration)
            $nav[$i] .=  $ph . $aix;

            // If current step is not a rowset step, and is the last step
            if (!preg_match('~/index/~', $nav[$i]) && $i == count($nav) - 1) {

                // Remove 'jump' param from $_GET
                Indi::get('jump', null);

                // Now we have proper (containing `ph` and `aix` params) uri, so we dispatch it
                $this->redirect(array_pop($nav));

            // Simulate as if rowset panel was loaded
            } else Indi::uri()->dispatch($nav[$i]);

            // Setup sorting
            if (Indi::trail()->section->defaultSortField)
                Indi::get('sort', json_encode(array(array(
                    'property' => Indi::trail()->section->foreign('defaultSortField')->alias,
                    'direction' => Indi::trail()->section->defaultSortDirection
                ))));

            // Simulate as if rowset data was loaded into rowset panel. This provide
            // Indi::trail()->scope's fulfilness with `found` and `ORDER` properties
            if (preg_match('~/index/~', $nav[$i])) Indi::uri()->dispatch($nav[$i]. 'format/json/');

            // Get the id of a row, that we will be simulating navigation
            // to subsection, there that row's nested entries are located
            preg_match('~/id/([0-9]+)/~', $nav[$i+1], $m); $id = $m[1];

            // If next url (within $nav) is related to non-same section
            if ($ti[$i+1]) {

                // Get row index
                $aix = $ti[$i+1]->model->detectOffset(Indi::trail()->scope->WHERE, Indi::trail()->scope->ORDER, $id);

                // Use the primary hash and row index for building corresponding parts of the uri for the next iteration
                $ph = 'ph/' . Indi::trail()->scope->hash . '/';
                $aix = 'aix/' . $aix . '/';
            }
        }

        // Remove 'jump' param from $_GET
        Indi::get('jump', null);

        // Now we have proper (containing `ph` and `aix` params) uri, so we dispatch it
        $this->redirect(array_pop($nav));
    }

    /**
     * This function is for adjusting params of a scope, identified by it's section and hash
     *
     * @return mixed
     */
    public function adjustCertainScope() {

        // If no `forScope` param exists within request data - return
        if (!$apply = Indi::post('forScope')) return;

        // If try to json-decode the value of the $apply variable has no success - return
        if (!$apply = json_decode($apply, true)) return;

        // If $apply object does not have either `section` or `hash` properties - return
        if (!$apply['section'] || !$apply['hash']) return;

        // If there is no such a scope - return
        if (!$_SESSION['indi']['admin'][$apply['section']][$apply['hash']]) return;

        // Preliminary unset 'section' and 'hash' params from $merge array, before merging with $_SESSION[...]
        $merge = $apply; unset($merge['section'], $merge['hash']);

        // Adjust scope, according to params
        $_SESSION['indi']['admin'][$apply['section']][$apply['hash']] = array_merge(
            $_SESSION['indi']['admin'][$apply['section']][$apply['hash']],
            $merge
        );
    }
}