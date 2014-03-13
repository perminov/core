<?php
class SitemapController extends Indi_Controller_Front{
    public function indexAction(){
        foreach($this->rowset as $row) {
            $row->level = 1;
            foreach ($row->dependent['actions'] as $action) {
                if ($action->type == 'r') {
                    if ($action->foreign['factionId']->alias == 'index') {
                        $map[] = array('title' => $row->title, 'href' => '/' . $row->alias, 'level' => $row->level);
                        $row->hasIndexAction = true;
                    } else if ($action->foreign['factionId']->alias == 'details') {
                        $model = Indi::model($row->entityId);
                        $toggle = $model->fields('toggle');
                        $move = $model->fields('move');
                        $rs = $model->fetchAll($toggle ? '`toggle` = "y"' : null, $move ? '`move`' : null);
                        if ($model->name() == 'staticpage') {
                            foreach ($rs as $r) {
                                $map[] = array(
                                    'title' => $r->title,
                                    'href' => '/' . $r->alias,
                                    'level' => $row->level + ($row->hasIndexAction ? 1 : 0)
                                );
                            }
                        } else {
                            foreach ($rs as $r) {
                                $map[] = array(
                                    'title' => $r->title,
                                    'href' => '/' . $row->alias . '/details/id/' . $r->id . '/',
                                    'level' => $row->level + ($row->hasIndexAction ? 1 : 0)
                                );
                            }
                        }
                    }
                }
            }

        }
        $this->view->tree = $map;
        if(array_key_exists('update', $this->params)) {
            $xml = $this->view->siteMapXml();
            i($xml, 'w', 'sitemap.xml');
            die('Файл sitemap.xml обновлен');
        }
    }
}