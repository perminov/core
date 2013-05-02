<?php
class Indi_View_Helper_SeoTDK extends Indi_View_Helper_Abstract{
	public $title = array();
    public static $exclusion = null;
	public function seoTDK($what = 'title'){
		$this->rowBackup = $this->view->row;
		if ($this->view->row) {
            if ($this->view->trail->getItem()->section->entityId) {
                if (is_object($this->exclusion)) {
                    return $this->exclusion->{'seo' . ucfirst($what)};
                } else if ($this->exclusion === null){
                    $exclusionM = Misc::loadModel('metaExclusion');
                    if ($this->exclusion = $exclusionM->fetchRow('`entityId`="' . $this->view->trail->getItem()->section->entityId . '" AND `identifier` = "' . $this->view->row->id . '" AND `toggle` = "y"')){
                        return $this->exclusion->{'seo' . ucfirst($what)};
                    } else {
                        $this->exclusion = false;
                    }
                }
            }
            $title = array();
            if ($this->view->row->useSystemSeoSolution == 'n') {
				return $this->view->row->{'seo' . ucfirst($what)};
			} else {
				$parts = Misc::loadModel('Seo'. ucfirst($what))->fetchAll('`fsection2factionId`="' . $this->view->section2actionId . '"', 'move');
				$parts->setForeignRowsbyForeignKeys('fieldId,sibling');
				$this->title = array();
				static $siblingRow;
				if (!is_array($siblingRow)) $siblingRow = array();
				if ($parts->count()) {
					foreach ($parts as $part) {
						if ($part->type == 's') {
							$title[] = $part->prefix . $part->static . $part->postfix;
						} else if ($part->type == 'level') {
							$title[] = $part->prefix . $this->view->trail->getItem($part->level)->section->getTitle() . $part->postfix;
						} else {
							if (!$part->stepsUp) $part->stepsUp = 0;
							$this->view->row = $this->view->trail->getItem($part->stepsUp)->row;
							if ($part->where == 'c') {
								if ($this->view->trail->getItem($part->stepsUp)->section->entityId == $part->entityId) {
									if ($part->foreign['fieldId']['relation']) {
										$title[] = $part->prefix . $this->view->row->getForeignRowByForeignKey($part->foreign['fieldId']['alias'])->getTitle() . $part->postfix;
									} else {
										$title[] = $part->prefix . $this->view->row->{$part->foreign['fieldId']['alias']} . $part->postfix;
									}
									$siblingRow[$this->view->trail->getItem()->model->info('name') . 'Id'] = $this->view->row;
								} else if ($part->entityId == '101') {
									if ($part->foreign['fieldId']['relation']) {
										$title[] = $part->prefix . $this->view->trail->getItem($part->stepsUp)->section->getForeignRowByForeignKey($part->foreign['fieldId']['alias'])->getTitle() . $part->postfix;
									} else {
										$title[] = $part->prefix . $this->view->trail->getItem($part->stepsUp)->section->{$part->foreign['fieldId']['alias']} . $part->postfix;
									}
                                } else {
									$model = Entity::getModelById($part->entityId);
									$pkn = $model->info('name') . 'Id';
									$pkv = $this->view->row->$pkn;
									if (!$siblingRow[$pkn]) {
										$row = $model->fetchRow('`id` = "' . $pkv . '"');
										$siblingRow[$pkn] = $row;
									} else {
										$row = $siblingRow[$pkn];
									}
                                    if ($part->foreign['fieldId']['relation']) {
                                        $title[] = $part->prefix . $row->getForeignRowByForeignKey($part->foreign['fieldId']['alias'])->getTitle() . $part->postfix;
                                    } else {
                                        $title[] = $part->prefix . $row->{$part->foreign['fieldId']['alias']} . $part->postfix;
                                    }
								}
							} else if ($part->where == 's'){
								$model = Entity::getModelById($part->entityId);
								$siblingModel = Entity::getModelById($part->foreign['sibling']['entityId']);
								$pkn = $model->info('name') . 'Id';
								$pkv = $siblingRow[$siblingModel->info('name') . 'Id']->$pkn;
								$row = $model->fetchRow('`id` = "' . $pkv . '"');
								if ($part->foreign['fieldId']['relation']) {
									$title[] = $part->prefix . $row->getForeignRowByForeignKey($part->foreign['fieldId']['alias'])->getTitle() . $part->postfix;
								} else {
									$title[] = $part->prefix . $row->{$part->foreign['fieldId']['alias']} . $part->postfix;
								}
							}
						}
					}
				}
				$this->title = $title;
				$xhtml = implode(' ', $title);
			}
		} else {

			$this->title = array();
			$this->cr = Indi_Uri::sys2seo('href="' . $_SERVER['REQUEST_URI'] . '"', true);
			$this->view->trail->contextRows = $this->cr;
			if( ! is_array($this->cr)) {
				$this->cr = array();
				$entity = Entity::getInstance();
				for($i = 0; $i < count($this->view->trail->items); $i++){
					$item = $this->view->trail->getItem($i);
					if ($item->row) $this->cr[$entity->fetchRow('`table` = "' . $item->model->info('name') . '"')->id] = $item->row;
				}
			}
			$this->constructSeoForRowsetActions($what, 0);
			$xhtml = implode(' ', $this->title);
		}
		$this->view->row = $this->rowBackup;
		return str_replace('<br>', ' ', $xhtml);
	}
	function constructSeoForRowsetActions($what, $parentId = 0){
		$parts = Misc::loadModel('Seo'. ucfirst($what))->fetchAll('`seo'. ucfirst($what) . 'Id` = "' . $parentId . '" AND `fsection2factionId`="' . $this->view->section2actionId . '"', 'move');
		$parts->setForeignRowsbyForeignKeys('fieldId,sibling');
		static $siblingRow;
		if (!is_array($siblingRow)) $siblingRow = array();
		if ($parts->count()) {
			$orNotYetFound = true;
			foreach ($parts as $part) {
				$row = null;
				if (!$part->stepsUp) $part->stepsUp = 0;
				// ���������������� ���� '�' (�� ���� 'AND')
				if ($part->need == 'a') {
					// ���� ��������� - �����������
					if ($part->type == 's') {
						$this->title[] = $part->prefix . $part->static . $part->postfix;
					// ���� ��������� - ������������
					} else if ($part->type == 'level') {
                        $this->title[] = $part->prefix . $this->view->trail->getItem($part->level)->section->getTitle() . $part->postfix;
                    } else if ($part->type == 'd') {
						// ���� ������������� ����� ������ � ���������
						if ($part->where == 'c') {
                            // ���� �� ��� ������
							if ($this->cr[$part->entityId]) {
								$model = Entity::getModelById($part->entityId);
								$pkn = $model->info('name') . 'Id';
								$row = $this->cr[$part->entityId];
								$siblingRow[$pkn] = $row;
								if ($row) {
                                    if ($part->foreign['fieldId']['relation']) {
                                        $this->title[] = $part->prefix . $row->getForeignRowByForeignKey($part->foreign['fieldId']['alias'])->getTitle() . $part->postfix;
                                    } else {
                                        $this->title[] = $part->prefix . $row->{$part->foreign['fieldId']['alias']} . $part->postfix;
                                    }
                                }
							} else if ($part->entityId == '101') {
								if ($part->foreign['fieldId']['relation']) {
									$this->title[] = $part->prefix . $this->view->trail->getItem($part->stepsUp)->section->getForeignRowByForeignKey($part->foreign['fieldId']['alias'])->getTitle() . $part->postfix;
								} else {
									$this->title[] = $part->prefix . $this->view->trail->getItem($part->stepsUp)->section->{$part->foreign['fieldId']['alias']} . $part->postfix;
								}
                            }
						// ���� ������������� ����� ������ � sibling ����������
						} else if ($part->where == 's') {
							$model = Entity::getModelById($part->entityId);
							$siblingModel = Entity::getModelById($part->foreign['sibling']['entityId']);
							$pkn = $model->info('name') . 'Id';
							$pkv = $siblingRow[$siblingModel->info('name') . 'Id']->$pkn;
							$row = $model->fetchRow('`id` = "' . $pkv . '"');
							if ($row) {
								if ($part->foreign['fieldId']['relation']) {
									$this->title[] = $part->prefix . $row->getForeignRowByForeignKey($part->foreign['fieldId']['alias'])->getTitle() . $part->postfix;
								} else {
									$this->title[] = $part->prefix . $row->{$part->foreign['fieldId']['alias']} . $part->postfix;
								}
							}
						}
					}
				} else if($part->need == 'o') {
					if ($part->type == 's' && $orNotYetFound) {
						$this->title[] = $part->prefix . $part->static . $part->postfix;
					// ���� ��������� - ������������
					} else if ($part->type == 'd' && is_array($this->cr) && in_array($part->entityId, array_keys($this->cr)) && $orNotYetFound) {
						$orNotYetFound = false;
						$model = Entity::getModelById($part->entityId);
						$pkn = $model->info('name') . 'Id';
						// ���� ������������� ����� ������ � ���������
						if ($part->where == 'c') {
							// ���� �� ��� ������
							if ($this->cr[$part->entityId]) {
								$model = Entity::getModelById($part->entityId);
								$pkn = $model->info('name') . 'Id';
								$row = $this->cr[$part->entityId];
								$siblingRow[$pkn] = $row;
								if ($row) {
									if ($part->foreign['fieldId']['relation']) {
										$this->title[] = $part->prefix . $row->getForeignRowByForeignKey($part->foreign['fieldId']['alias'])->getTitle() . $part->postfix;
									} else {
										$this->title[] = $part->prefix . $row->{$part->foreign['fieldId']['alias']} . $part->postfix;
									}
								}
							} else if ($part->entityId == '101') {
								if ($part->foreign['fieldId']['relation']) {
									$this->title[] = $part->prefix . $this->view->trail->getItem($part->stepsUp)->section->getForeignRowByForeignKey($part->foreign['fieldId']['alias'])->getTitle() . $part->postfix;
								} else {
									$this->title[] = $part->prefix . $this->view->trail->getItem($part->stepsUp)->section->{$part->foreign['fieldId']['alias']} . $part->postfix;
								}
                            }
						// ���� ������������� ����� ������ � sibling ����������
						} else if ($part->where == 's') {
							$model = Entity::getModelById($part->entityId);
							$siblingModel = Entity::getModelById($part->foreign['sibling']['entityId']);
							$pkn = $model->info('name') . 'Id';
							$pkv = $siblingRow[$siblingModel->info('name') . 'Id']->$pkn;
							$row = $model->fetchRow('`id` = "' . $pkv . '"');
							if ($row) {
								if ($part->foreign['fieldId']['relation']) {
									$this->title[] = $part->prefix . $row->getForeignRowByForeignKey($part->foreign['fieldId']['alias'])->getTitle() . $part->postfix;
								} else {
									$this->title[] = $part->prefix . $row->{$part->foreign['fieldId']['alias']} . $part->postfix;
								}
							}
						}
					}
				}
				$prevNeed = $part->need;
				if ($prevNeed == 'a') $orNotYetFound = true;
				if ($row) $this->constructSeoForRowsetActions($what, $part->id);
			}
		}
	}
}
