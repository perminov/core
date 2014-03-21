<?php
class Indi_Trail_Admin{
    /**
     * Array of Indi_Trail_Admin_Item items
     *
     * @var array
     */
    public static $items = array();

    /**
     * Constructor
     *
     * @param array $routeA Array of section ids, starting from current section and up to the top
     */
    public function __construct($routeA) {

        // Performance detection
        $q = Indi_Db::$queryCount; mt();

        // Get all sections, starting from current and up to the most top
        $sectionRs = Indi::model('Section')->fetchAll(
            '`id` IN (' . $route = implode(',', $routeA) . ')',
            'FIND_IN_SET(`id`, "' . implode(',', $routeA) . '")'
        )->foreign('parentSectionConnector,defaultSortField');

        // Get the id of most top section (menu group)
        $top = $routeA[count($routeA) - 1];

        // Setup accessible actions
        $sectionRs->nested('section2action', array(
            'where' => array(
                '`sectionId` != "' . $top . '"',
                '`toggle` = "y"',
                'FIND_IN_SET("' . $_SESSION['admin']['profileId'] . '", `profileIds`)',
                '`actionId` IN (' . implode(',', Indi::db()
                    ->query('SELECT `id` FROM `action` WHERE `toggle` = "y"')
                    ->fetchAll(PDO::FETCH_COLUMN)) . ')'
            ),
            'order' => 'move',
            'foreign' => 'actionId'
        ));

        // Get the array of accessible sections ids
        $accessibleSectionIdA = array();
        foreach ($sectionRs->nested('section2action') as $sectionId => $section2actionRs)
            foreach ($section2actionRs->original() as $section2actionI)
                if ($section2actionI['actionId'] == 1)
                    $accessibleSectionIdA[] = $sectionId;

        // Get accessible nested sections for each section within the trail
        $sectionRs->nested('section', array(
            'where' => '`sectionId` IN (' . implode(',', $accessibleSectionIdA) . ')',
            'order' => 'move'
        ));

        // Get filters
        $sectionRs->nested('search', array(
            'where' => '`sectionId` = "' . $routeA[0] . '" AND `toggle` = "y"',
            'order' => 'move'
        ));

        // Setup a primary hash for current section
        $sectionRs->temporary($routeA[0], 'primaryHash', Indi::uri('ph'));

        // Setup grid columns
        $sectionRs->nested('grid', array(
            'where' => '`sectionId` = "' . $routeA[0] . '" AND `toggle` = "y"',
            'order' => 'move'
        ));

        // Setup disabled fields
        $sectionRs->nested('disabledField', array(
            'where' => '`sectionId` = "' . $routeA[0] . '"'
        ));

        // Setup initial set of properties
        foreach ($sectionRs as $sectionR)
            self::$items[] = new Indi_Trail_Admin_Item($sectionR);

        // If currently we are at at least 2-level section, assuming that
        // 0-level sections are the most top sections, e.g left menu groups,
        // 1-level sections are sections, that are nested to menu groups
        // For example, if we have the following structure:
        //
        // Geography   (0-level, menu group)
        //   Countries (1-level)
        //     Cities  (2-level)
        //
        // - example assumes, that we are viewing list of cities within sme certain country,
        // and url is like /cities/index/id/123/, where 123 - is the id of country.
        // So, in such situation we need to remember '123', because if user would like to add
        // a new city within that certain country, he will be at url /cities/form/, and it does not
        // contain any definition of country, that city should be added under. So, this solution
        // allow to get the id of country

        if (Indi::uri('section') != 'index' && Indi::uri('action') == 'index' && Indi::uri('id')) {

            // If there is no info about nesting yet, we create an array, where it will be stored
            if (!is_array($_SESSION['indi']['admin']['trail']['parentId']))
                $_SESSION['indi']['admin']['trail']['parentId'] = array();

            // Save id
            $_SESSION['indi']['admin']['trail']['parentId'][self::$items[0]->section->sectionId] = Indi::uri('id');
        }

        // Setup row for each trail item
        for ($i = 0; $i < count(self::$items); $i++)
            self::$items[$i]->row($i);

        // Reverse items
        self::$items = array_reverse(self::$items);
    }

    public function item($stepsUp = 0){
        return self::$items[count(self::$items) - 1 - $stepsUp];
    }

    public function setItemScopeHashes($hash, $aix, $index) {
        $i = -1 + ($index ? 1 : 0);
        do {
            $i++;
            $this->items[count($this->items) - 1 - $i]->section->primaryHash = $hash;
            $this->items[count($this->items) - 1 - $i]->section->rowIndex = $aix;
        } while (($hash = $_SESSION
            ['indi']
            ['admin']
            [$this->items[count($this->items) - 1 - $i]->section->alias]
            [$this->items[count($this->items) - 1 - $i]->section->primaryHash]
            ['upperHash'])
            &&
            (($aix = $_SESSION
            ['indi']
            ['admin']
            [$this->items[count($this->items) - 1 - $i]->section->alias]
            [$this->items[count($this->items) - 1 - $i]->section->primaryHash]
            ['upperAix']) || true)
        );
    }

    public function toString($imploded = true) {

         // Declare crumbs array and push the first item - section group
        $crumbA = array($this->items[0]->section->title);

        // For each remaining trail items
        for ($i = 1; $i < count($this->items); $i++) {

            // Define a shortcut for current trail item
            $item = $this->items[$i];

            // Append a current item section title
            $crumbA[] = $item->section->title;

            // If current trail item has a row
            if ($item->row) {

                // If that row has an id
                if ($item->row->id) {

                    // At first, we strip newline characters, html '<br>' tags
                    $title = preg_replace('<br(|\/)>', '', preg_replace('/[\n\r]/' , '', $item->row->title));

                    // Detect color
                    preg_match('/color[:=][ ]*[\'"]{0,1}([#a-zA-Z0-9]+)/i', $title, $color);

                    // Strip the html tags from title, and extract first 50 characters
                    $title = mb_substr(strip_tags($title), 0, 50, 'utf-8');

                    // Append current trail item row title, with color definition
                    $crumbA[] = '<i' . ($color ? ' style="color: ' . $color[1] . ';"' : '') . '>' . $title . '</i>';

                    // If current trail item is a last item, append current trail item action title
                    if ($i == count($this->items) - 1) $crumbA[] = $item->action->title;

                // Else if current trail item row does not have and id, and current action alias is 'form'
                } else if ($item->action->alias == 'form') {

                    // We append 'form' action title, but it' version for case then new row is going to be
                    // created, hovewer, got from localization object, instead of actual action title
                    $crumbA[] = ACTION_CREATE;
                }
            }
        }

        return $imploded ? implode(' » ', $crumbA) : $crumbA;
    }
}