<?php

class Portal{

    private $portalSetup = null; 
    private $resonators = array(1,1,1,1,1,1,1,1);
    private $mods = array();
    private $regexPatternPortalSetup = "~^[1-8]{8}($|(\s[1-4]\s(LAVR|LAVRS|LINKAMPVR|LINKAMPVRS|LA|LAS|LINKAMP|LINKAMPS|ULA|ULAS|UL|ULS|SBUL|SBULS|ULTRALINK|ULTRALINKS)){1,3}$)~i";
    
    public const ValueModLA = 2.0;
    public const ValueModLAVR = 7.0; 
    public const ValueModULA = 5.0;
    public const NameModLA = "la";
    public const NameModLAVR = "vr";
    public const NameModULA ="ul";
    public const EXCEPTION_INVALID_PORTAL_SETUP ="INVALID_PORTAL_SETUP";
    public const EXCEPTION_INVALID_AMOUNT_OF_MODS ="INVALID_AMOUNT_OF_MODS";
    
    
    public function __construct($portalSetup){
        
       $this->portalSetup = trim(strtolower($portalSetup));
       
        //Try to load portal
        $this->initPortalSetup();
        
    }

    private function loadPortalResonators(){
        
        preg_match("~^[1-8]{8}~",$this->portalSetup,$match);
            $this->resonators = str_split($match[0]);        
    }

    private function initPortalSetup(){
        
        //validate portal setup input
        $success = preg_match($this->regexPatternPortalSetup,$this->portalSetup,$match);
        
        //if not success
        if(!$success){
            throw new Exception(Portal::EXCEPTION_INVALID_PORTAL_SETUP);
        }
        
        //if input is valid, load resonators
        $this->loadPortalResonators();
        
        //and try to load mods
        $this->loadPortalMods($match);

    }
        
    private function loadPortalMods($regexMatch){
         
       //if exists mods, validate mods setup
       if(count($regexMatch) >= 2){
           
           //get Mods Setup from Original regex
           $modsSetup = $regexMatch[1];

          //and get numeric part of mods 
           preg_match_all("~\d~",$modsSetup,$matchMods);
           
           //if sum of mods is bigger then 4, cancel portal loading
           if(array_sum($matchMods[0]) > 4){
               throw new Exception(Portal::EXCEPTION_INVALID_AMOUNT_OF_MODS);
           }
           
           //if still here, finally load Mods setup
           $this->loadMods($modsSetup);
           
       }
    
    }
    
    
    private function translateModsSetup($modsSetup){
        
        $result = $modsSetup;
        
        //VRs
        $result = str_replace("linkampvrs", Portal::NameModLAVR , $result);
        $result = str_replace("linkampvr", Portal::NameModLAVR , $result);
        $result = str_replace("lavrs", Portal::NameModLAVR , $result);
        $result = str_replace("lavr", Portal::NameModLAVR , $result);
        
        //SBULs
        $result = str_replace("ultralinks", Portal::NameModULA , $result);
        $result = str_replace("ultralink", Portal::NameModULA , $result);
        $result = str_replace("sbuls", Portal::NameModULA , $result);
        $result = str_replace("sbul", Portal::NameModULA , $result);
        $result = str_replace("uls", Portal::NameModULA , $result);
        $result = str_replace("ulas", Portal::NameModULA , $result);
        $result = str_replace("ula", Portal::NameModULA , $result);
        
        //LAs
        $result = str_replace("linkamps", Portal::NameModLA , $result);
        $result = str_replace("linkamp", Portal::NameModLA , $result);
        $result = str_replace("las", Portal::NameModLA , $result);
        
        return $result;
    }
    
    private function loadMods($modsSetup){
        
        $modsSetupTranslated = $this->translateModsSetup($modsSetup);
        
        $countLAVR = $this->getModsCount($modsSetupTranslated, Portal::NameModLAVR);
        for($i=0;$i<=$countLAVR-1;$i++){
            array_push($this->mods, Portal::ValueModLAVR);
        }
        
        $countULA = $this->getModsCount($modsSetupTranslated, Portal::NameModULA);
        for($i=0;$i<=$countULA-1;$i++){
            array_push($this->mods, Portal::ValueModULA);
        }
        
        $countLA = $this->getModsCount($modsSetupTranslated, Portal::NameModLA);
        for($i=0;$i<=$countLA-1;$i++){
            array_push($this->mods, Portal::ValueModLA);
        }
    }
    
    private function getModsCount($modsSetup, $modType){
        
        $regexModCount = "~\d\s" . $modType . "~";
        
        preg_match_all($regexModCount, $modsSetup, $matchMods);
        
        $countMods = 0;
        
        foreach ($matchMods[0] as $mod){
            $countMods += intval(substr($mod, 0, 1));    
        }
        
        return  $countMods;
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
    
    
    public  function getLinkRangeInKilometers(){
        return $this->getLinkRangeInMeters() / 1000;
    }
}