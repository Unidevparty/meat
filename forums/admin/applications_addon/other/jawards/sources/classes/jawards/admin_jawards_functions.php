<?php

if(!defined('IN_IPB'))
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class admin_jawards_functions extends class_jawards
{
	public function awardsMenu( $name="", $selected="" )
	{
		if( ! $name )
		{
			$name = "award";
		}

		# Get the awards
		$this->DB->build( array( 'select' => '*',
								 'from'   => 'jlogica_awards',
					));
		$res = $this->DB->execute();
		$hasSel = 0;
		$output = '';
		$defSel = '';
		$options = '';
		while( $a = $this->DB->fetch( $res ) )
		{
			$sel    = '';
			if( $selected == $a['id'] )
			{
				$sel    = ' selected';
				$hasSel = 1;
			}
			$options .= "<option value='{$a['id']}'{$sel}>{$a['name']}</option>";
		}
		if( ! $hasSel )
		{
			$defSel = " selected";
		}
		if( $options )
		{
			$output .= "<select name='{$name}' id='{$name}'>";
			$output .= "<option{$defSel} disabled>Choose an Award</option>";
			$output .= $options;
			$output .= "</select>";
		}
		return( $output);
	}

	public function giveAward($aid, $mid, $reason="")
	{
		# Update the user's awards
		$INSERT = array(
						'award_id'   => $aid,
						'user_id'    => $mid,
						'awarded_by' => $this->memberData['member_id'],
						'notes'      => $reason,
						'date'       => time(),
		);

		$this->DB->insert('jlogica_awards_awarded', $INSERT);
	}
}
