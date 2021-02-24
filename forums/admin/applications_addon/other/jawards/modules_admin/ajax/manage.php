<?php
if (!defined('IN_ACP'))
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class admin_jawards_ajax_manage extends ipsAjaxCommand
{
	public function doExecute(ipsRegistry $registry)
	{
		//IPSDebug::fireBug( 'info', array( "admin_jawards_ajax_manage::doExecute(" . $this->request['do'] . ")" ) );
		switch($this->request['do'])
		{
			case "addCategory":
				$this->addCategory();
				break;
			case "alterCatName":
				$this->alterCatName();
				break;
			case "order":
				$this->order();
				break;
			case "visibility":
				$this->visibility();
				break;
			case "catshow":
				$this->catshow();
				break;
			default:
				$this->returnHTML(0);
				break;
		}
	}

	private function addCategory()
	{
		if( $this->DB->insert('jlogica_awards_cats', array( 'title' => $_POST['name'] ) ) )
		{
			$this->registry->jawards_core->rebuildAllCaches();
			$this->returnHTML(1);
		}
		else
		{
			$this->returnHTML(0);
		}
	}

	private function alterCatName()
	{
		if($_POST['catName'] && $_POST['cat'])
		{
			$this->registry->jawards_core->rebuildAllCaches();
			$this->DB->update('jlogica_awards_cats', array('title' => $_POST['catName']), '`cat_id` = "' . $_POST['cat'] . '"');
			$this->returnHTML(1);
		}
		else
		{
			$this->returnHTML(0);
		}
	}

	private function order()
	{
		if($this->request['for'] == "cats")
		{
			foreach($_POST['cats'] as $num => $id)
			{
				$num++;
				$this->DB->update('jlogica_awards_cats', array('placement' => $num), "cat_id = '" . $id . "'");
			}
		}
		elseif($this->request['for'] == "awds")
		{
			foreach($_POST['awds'] as $num => $id)
			{
				$num++;
				$this->DB->update('jlogica_awards', array('placement' => $num), "id = '" . $id . "' && parent= '" . $this->request['cat'] . "'");
			}
		}
		$this->registry->jawards_core->rebuildAllCaches();
	}

	private function visibility()
	{
		if($this->request['for'] == "cats")
		{
			$this->DB->update('jlogica_awards_cats', array('visible' => $_POST['status']), "cat_id = '" . $this->request['cat_id'] . "'");
			$this->registry->jawards_core->rebuildAllCaches();
			$this->returnHTML(1);
		}
		elseif($this->request['for'] == "awards")
		{
			$this->DB->update('jlogica_awards', array('visible' => $_POST['status']), "id = '" . $this->request['id'] . "'");
			$this->registry->jawards_core->rebuildAllCaches();
			$this->returnHTML(1);
		}
	}

	private function catshow()
	{
		$this->DB->update( 'jlogica_awards_cats', array( 'frontend' => $_POST['status'] ), "cat_id = '{$this->request['id']}'" );
		$this->registry->jawards_core->rebuildAllCaches();
		$this->returnHTML(1);
	}

}
