<?php

require_once('Admin/Admin/Confirmation.php');
require_once('SwatDB/SwatDB.php');
require_once('Admin/AdminDependency.php');

/**
 * Generic admin database confirmation page
 *
 * This class is intended to be a convenience base class. For a fully custom 
 * DB confirmation page, inherit directly from AdminConfirmation instead.
 *
 * @package Admin
 * @copyright silverorange 2004
 */
abstract class AdminDBConfirmation extends AdminConfirmation {

	protected function processResponse() {
		$form = $this->ui->getWidget('confirmation_form');

		if ($form->button->name == 'yes_button') {
			try {
				$this->app->db->beginTransaction();
				$this->processDBData();
				$this->app->db->commit();

			} catch (SwatDBException $e) {
				$this->app->db->rollback();
				$this->processGenerateMessage($e);
				$e->process();

			} catch (SwatException $e) {
				$this->processGenerateMessage($e);
				$e->process();
			}
		}
	}

	protected function processGenerateMessage(Exception $e) {
		if ($e instanceof SwatDBException)
			$msg = new SwatMessage(_S("A database error has occured."), SwatMessage::ERROR);
		else
			$msg = new SwatMessage(_S("An error has occured."), SwatMessage::ERROR);

		$this->app->addMessage($msg);
	}

	/**
	 * Process data in the database
	 *
	 * This method is called to process data after an affirmative confirmation response.
	 * Sub-classes should implement this method and perform whatever actions
	 * are necessary process the repsonse.
	 */
	abstract protected function processDBData();
}

?>