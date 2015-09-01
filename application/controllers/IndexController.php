<?php

class IndexController extends Star_Controller_Action
{
    public function init()
	{
		
    }
    
	public function indexAction()
	{
        $this->view->title = 'Hello World';
        $this->view->assign(array(
            'content' => 'Hello World.'
        ));
	}
 
}

?>