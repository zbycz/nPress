<?php
/**
 * nPress - opensource cms
 *
 * @copyright  (c) 2012 Pavel Zbytovský (pavel@zby.cz)
 * @link       http://npress.info/
 * @package    nPress
 */


abstract class Admin_BasePresenter extends CommonBasePresenter
{
	public function startup() {
		parent::startup();

		//check permission
		if(!$this->user->isLoggedIn()){
			$backlink = $this->application->storeRequest();
			$this->redirect(':Front:Login:', array('backlink' => $backlink));
		}

		//admin things
		PagesModel::$showUnpublished = true;
		$this->template->wysiwygConfig = $this->context->params['npress']['wysiwyg'];
	}


	//not in use for now, TODO should be and action
	public function handleGetPagesFlatJson(){
		$array = PagesModel::getPagesFlat()->getPairs();
		$this->sendResponse(new JsonResponse($array));
	}


	//send flashes with every AJAX response
	public function afterRender(){
	    if ($this->isAjax() && $this->hasFlashSession())
	        $this->invalidateControl('flashes');
	}


	//working expandable tree view components (not in use now)
	protected function createComponentTreeView(){
		$CategoriesTree = new ExpandableTreeView;
		$CategoriesTree->hideRootNode = true;
		$CategoriesTree->treeViewNode($this->pages);
		$CategoriesTree->expandNode();
		return $CategoriesTree;
	}


	//Allow to use helpers as a latte macros
	public function templatePrepareFilters($template) {
		$template->registerFilter($e = new Nette\Latte\Engine());
		$s = new Nette\Latte\Macros\MacroSet($e->compiler);
		$s->addMacro('helper', 'ob_start()',
			function($n) {
				$w = new \Nette\Latte\PhpWriter($n->tokenizer, $n->args);
				return $w->write('echo %modify(ob_get_clean())');
			}
		);
	}

}
