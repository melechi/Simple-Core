<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
/**
 * Feedback Handler.
 * 
 * This is a useful class for managing feedback within your applications.
 * A useful example would be form submission. If a submission was
 * successful, you could do something like this:
 * $this->component->feedback->message('Submission was successful!');
 * And alternatively, if there was an error.
 * $this->component->feedback->error('Please fill out all required fields.');
 * 
 * These feedbacks can be retreived with the {@link getMessages()} and
 * {@link getErrors()} methods. This is useful because you know what kind
 * of feedback you're getting and can style the errors on the page
 * appropriately.
 * 
 * @author Timothy Chandler
 * @version 2.0
 * @copyright Simple Site Solutions 26/11/2007
 */
class component_feedback extends component
{
	const EXCEPTION_NAME='Feedback Exception';
	
	private $feedback=array();
	
	/**
	 * Initiation method to sync the local feedback property with
	 * the session feedback data.
	 * @access public
	 * @return bool
	 */
	
	public function initiate()
	{
		$this->feedback=$this->component->session->feedback;
		if (!isset($this->feedback['message']))$this->feedback['message']=array();
		if (!isset($this->feedback['error']))$this->feedback['error']=array();
		return true;
	}
	
	/**
	 * Commits changes to the local feedback property.
	 * 
	 * This method will assure that the feedback is saved
	 * in the user's session by commiting it to the feedback
	 * value in the session handler.
	 * @access private
	 * @return bool
	 */
	
	private function commit()
	{
		$this->component->session->feedback=$this->feedback;
		return true;
	}
	
	/**
	 * Search method for finding a specific type of feedback.
	 * 
	 * This method will return an array of the requested feedback
	 * item. Any feedbacks that it returns it will mark off as returned
	 * (status 1). At the end of the search, this method commits its
	 * changes of the local feedback property to the session handler.
	 * 
	 * @param string $type
	 * @access private
	 * @return array
	 */
	
	private function search($type=null)
	{
		$return=array();
		if ($type=='message' || $type=='error')
		{
			for ($i=0,$j=count($this->feedback[$type]); $i<$j; $i++)
			{
				if ((int)$this->feedback[$type][$i]['status']==0)
				{
					$return[]=stripslashes($this->feedback[$type][$i]['string']);
					$this->feedback[$type][$i]['status']=1;
				}
			}
			$this->commit();
		}
		return $return;
	}
	
	/**
	 * Records a message.
	 * @access public
	 * @return bool
	 */
	
	public function message($string=null)
	{
		$return=false;
		if (!empty($string))
		{
			$this->feedback['message'][]=array('status'=>'0','string'=>$string);
			$this->commit();
			$return=true;
		}
		return $return;
	}
	
	/**
	 * Gets all messages.
	 * @access public
	 * @return array
	 */
	
	public function getMessages()
	{
		return $this->search('message');
	}
	
	/**
	 * Returns true if a message has been sent to the feedback handler.
	 * @access public
	 * @return bool
	 */
	
	public function isMessage()
	{
		$return=false;
		for ($i=0,$j=count($this->feedback['message']); $i<$j; $i++)
		{
			if ((int)$this->feedback['message'][$i]['status']==0)
			{
				$return=true;
				break;
			}
		}
		return $return;
	}
	
	/**
	 * Records an error.
	 * @access public
	 * @return bool
	 */
	
	public function error($string=null)
	{
		$return=false;
		if (!empty($string))
		{
			$this->feedback['error'][]=array('status'=>'0','string'=>$string);
			$this->commit();
			$return=true;
		}
		return $return;
	}
	
	/**
	 * Gets all errors.
	 * @access public
	 * @return array
	 */
	
	public function getErrors()
	{
		return $this->search('error');
	}
	
	/**
	 * Returns true if an error has been sent to the feedback handler.
	 * @access public
	 * @return bool
	 */
	
	public function isError()
	{
		$return=false;
		for ($i=0,$j=count($this->feedback['error']); $i<$j; $i++)
		{
			if ((int)$this->feedback['error'][$i]['status']==0)
			{
				$return=true;
				break;
			}
		}
		return $return;
	}
}
?>