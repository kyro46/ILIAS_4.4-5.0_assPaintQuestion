<?php

/**
 * Plugin configuration class
 * @author Christoph Jobst <cjobst@wifa.uni-leipzig.de>
 * @ilCtrl_IsCalledBy ilassPaintQuestionConfigGUI: ilObjComponentSettingsGUI
 */
class ilassPaintQuestionConfigGUI extends ilPluginConfigGUI
{       
    /**
     * Handles all commmands,
     * $cmd = functionName()
     */
    public function performCommand($cmd) : void
    {
        $this->plugin = $this->getPluginObject();
        
        switch ($cmd)
        {
            case "configure":
            case "save":
                $this->$cmd();
                break;
        }
    }
    
    /**
     * Save-Action
     * update values in DB
     */
    function save()
    {
        global $ilCtrl, $tpl, $ilDB;
        $form = $this->initConfigurationForm();
        // input ok? length<=max, not null, ...
        if ($form->checkInput())
        {
            // get Values
            $enableForUsers = $form->getInput("enableForUsers");
            $logCount = $form->getInput("logCountValue");
            $logBkgr = $form->getInput("logBkgrValue");
            
            // store values
            $ilDB->manipulate("DELETE FROM il_qpl_qst_paint_conf");
            $ilDB->insert("il_qpl_qst_paint_conf",
                array(
                    "id"                     	=>	array("integer", 0),
                    "enable_for_users_conf" 	=>	array("integer", $enableForUsers),
                    "log_count_conf" 	        =>  array("integer", $logCount),
                    "log_bkgr_conf" 	     	=>  array("integer", $logBkgr),
                    )
                );

            $ilCtrl->redirect($this, "configure");
        } else
        {
            // input not ok, then
            $form->setValuesByPost();
            $tpl->setContent($form->getHtml());
        }
    }
    
    /**
     * Configure screen
     */
    function configure()
    {
        global $tpl;
        $form = $this->initConfigurationForm();
        $tpl->setContent($form->getHTML());
        
    }
    
    /**
     * Init configuration form.
     *
     * @return object form object
     */
    public function initConfigurationForm()
    {       
        global $lng, $ilCtrl, $ilDB;

        $result = $ilDB->query("SELECT enable_for_users_conf, log_count_conf, log_bkgr_conf FROM il_qpl_qst_paint_conf where id = 0" );
        $configuration = $ilDB->fetchAssoc($result);		
        
        include_once("Services/Form/classes/class.ilPropertyFormGUI.php");
        $form = new ilPropertyFormGUI();

        //Enable for users
        $enableForUsers = new ilCheckboxInputGUI($this->plugin->txt("enableForUsers"), 'enableForUsers');
        $enableForUsers->setInfo($this->plugin->txt("enableForUsers_hint"));
        if ($configuration['enable_for_users_conf'])
            $enableForUsers->setChecked(true);
        $form->addItem($enableForUsers);
        
        //LogCount
        $logCountOption = new ilSelectInputGUI($this->plugin->txt("logCountOption"),"logCountValue");
        $logCountOption->setInfo($this->plugin->txt("logCountOption_hint"));
        $logCountOption->setOptions (Array ( "1" => $this->plugin->txt("logCountOption_off"), "3" => "3", "10" => "10", "50" => "50", "100" => "100"));
        $logCountOption->setValue($configuration['log_count_conf']);
        $form->addItem($logCountOption);
        
        //LogBkgr
        $logBkgrOption = new ilCheckboxInputGUI($this->plugin->txt("logBkgrOption"), 'logBkgrValue');
        $logBkgrOption->setInfo($this->plugin->txt("logBkgrOption_hint"));
        if ($configuration['log_bkgr_conf'])
            $logBkgrOption->setChecked(true);
         $form->addItem($logBkgrOption);
            
         $form->addCommandButton("save", $this->plugin->txt("save"));
            
            $form->setFormAction($ilCtrl->getFormAction($this));
            
            return $form;
    }
    
}
?>