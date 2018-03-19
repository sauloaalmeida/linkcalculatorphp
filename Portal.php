<?php

class Portal{
    
    private $resonators = null;
    private $mods = null;
    
    public const ModLA = 2.0;
    public const ModLAVR = 7.0; 
    public const ModULA = 5.0;
    
    
    public function __construct($portalSetup){
        
        //verify portal setup informed
        $this->parsePortalSetup(trim($portalSetup));
        
        //setRessonatorsSetup
        $this->resonators = array(8,8,8,8,8,8,8,8);
        
        //setModsSetup
        $this->mods = array(Portal::ModLAVR,
            Portal::ModLAVR,
            Portal::ModLAVR,
            Portal::ModLAVR);
    }
        
     private function parsePortalSetup($portalSetup){
         
         $success = preg_match("~([1|2|3|4|5|6|7|8]{8})(.*)~", $portalSetup, $match);
         if ($success) {
             echo "Match: ".$match[0]."<br />";
             echo "Group 1: ".$match[1]."<br />";
             echo "Group 2: ".$match[2]."<br />";
             die;
         }
         
         //[\s(1|2|3|4)(la|vrla|ula)]*3
         
         if(!preg_match("~[1|2|3|4|5|6|7|8]{8}(\s[1|2|3|4]\s(lavr|ula|la)){0,3}~i",$portalSetup)){
             throw new Exception("INVALID_PORTAL_SETUP");
         }
     }
       

    
    private function getResonatorsSum(){
        $total = 0.0;
        
        foreach ($this->resonators as $reso){
            $total += $reso;
        }
   
        return $total;
    }
    
    private function getExactPortalLevel() {
        return $this->getResonatorsSum()/8;
    }
    
    private function getLinkRangeForPortalLevelOnly() {
        $result = (160.0 * pow($this->getExactPortalLevel(), 4));
        
        return $result;
    }
    
    
    private function getMultiplierValueForMods(){
        
        $multiplierResult = 1.0;
        
        if(count($this->mods) > 0){
            $multiplierResult = $this->mods[0];
        }
        
        if(count($this->mods) > 1){
            $multiplierResult += $this->mods[1] * 0.25;
        }
        
        if(count($this->mods) > 2){
            $multiplierResult += $this->mods[2] * 0.125;
        }
        
        if(count($this->mods) > 3){
            $multiplierResult += $this->mods[3] * 0.125;
        }
        
        return $multiplierResult;
    }
    
    public  function getLinkRangeInMeters(){        
        return $this->getMultiplierValueForMods() * $this->getLinkRangeForPortalLevelOnly(); 
    }
    
    
    public function getPortalSetup(){
       return $this->resonators; 
    }
}

