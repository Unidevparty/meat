<?php
if (!defined('IN_IPB'))
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class version_upgrade
{
	public function doExecute(ipsRegistry $registry)
	{
		$this->registry =  $registry;
		$this->DB       =  $this->registry->DB();

		if($this->request['workact'] == "sql")
		{
			# Get stuff ready!
			$this->registry =  $registry;
			$this->DB       =  $this->registry->DB();

			# Check the members table for awards in it....
			$this->DB->build(array(
								   'select' => 'member_id, awards',
								   'from'   => 'members',
								   'where'  => 'awards != ""',
			));
			$exec = $this->DB->execute();

			if($this->DB->getTotalRows($exec))
			{
				while($m = $this->DB->fetch($exec))
				{
					$exp = explode(',', $m['awards']);

					foreach($exp as $aid)
					{
						if($aid)
						{
							$INSERT = array(
											'award_id' => $aid,
											'user_id'  => $m['member_id'],
							);

							$this->DB->insert('inv_awards_awarded', $INSERT);
						}
					}

					$this->DB->update('members', array('awards' => ''), '`member_id` = "' . $m['member_id'] . '"');
				}
			}

			$this->registry->output->addMessage("Awarded users information updated!");

			return false;
		}
		else
		{
			return true;
		}
	}

	public function fetchOutput()
	{
		return "";
	}
}

