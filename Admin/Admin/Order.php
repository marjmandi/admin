<?php
require_once('Admin/AdminUI.php');
require_once('Admin/AdminPage.php');
require_once("MDB2.php");

/**
 * Generic admin ordering page
 *
 * This class is intended to be a convenience base class. For a fully custom 
 * ordering page, inherit directly from AdminPage instead.
 *
 * @package Admin
 * @copyright silverorange 2004
 */
abstract class AdminOrder extends AdminPage {

	public function init() {
		$this->ui = new AdminUI();
		$this->ui->loadFromXML('Admin/Admin/order.xml');
	}

	public function process() {
		$form = $this->ui->getWidget('orderform');
		if ($form->process()) {
			$this->saveData();
			$this->app->relocate($this->app->getHistory());
		}
	}

	public function display() {
		$radio_list = $this->ui->getWidget('options');
		$radio_list->options = array('auto'=>_S("Automatically"), 'custom'=>_("Custom"));
			
		$order_list = $this->ui->getWidget('order');
		$order_list->onclick = 'document.getElementById(\'options_custom\').checked = true;';
		
		$this->loadData();
	
		$btn_submit = $this->ui->getWidget('btn_submit');
		$btn_submit->title = _S("Update Order");
		
		$form = $this->ui->getWidget('orderform');
		$form->action = $this->source;

		$root = $this->ui->getRoot();
		$root->display();
	}
	
	protected function saveData() {
		$count = 0;
		$order_list = $this->ui->getWidget('order');
		$radio_list = $this->ui->getWidget('options');

		foreach ($order_list->values as $id) {
			if ($radio_list->value == 'custom')
				$count++;

			$this->saveIndex($id, $count);
		}
	}

	/**
	 * Save index
	 *
	 * This method is called by {@link AdminOrder::saveData()} to save a single 
	 * ordering index. Sub-classes should implement this method and perform 
	 * whatever actions are necessary to store the ordering index. Widgets can
	 * be accessed through the $ui class variable.
	 *
	 * @param integer $id An integer identifier of the data to store.
	 * @param integer $index The ordering index to store.
	 */
	abstract protected function saveIndex($id, $index);

	/**
	 * Load the data
	 *
	 * This method is called to load data to be edited into the widgets.
	 * Sub-classes should implement this method and perform whatever actions
	 * are necessary to obtain the data. Widgets can be accessed through the
	 * $ui class variable.
	 */
	abstract protected function loadData();
}
?>