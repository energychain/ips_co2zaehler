<?php
class CO2EmissionStrom extends IPSModule {

		public function Create()
		{
			//Never delete this line!
			parent::Create();

			$meterid = bin2hex(random_bytes(40));
			$secret = bin2hex(random_bytes(40));

			// Register some required parameters for our Corrently CO2 Reading operation
			$this->RegisterPropertyString("Postleitzahl", "69256");
			$this->RegisterPropertyString("meterId", $meterid);
			$this->RegisterPropertyString("secret", $secret);
			$this->RegisterVariableInteger("co2g_standard", "CO2 (Standard)");
			$this->RegisterVariableInteger("co2g_oekostrom", "CO2 (Ökostrom)");
		}

		public function setReading($reading) {
			$ch = curl_init("https://api.corrently.io/core/reading");
	    curl_setopt($ch,CURLOPT_POST,true);
	    curl_setopt($ch,CURLOPT_POSTFIELDS,"&externalAccount=ips_".$this->ReadPropertyString("meterId")."_".$this->ReadPropertyString("Postleitzahl")."&secret=".$this->ReadPropertyString("secret")."&energy=".$reading."&zip=".$this->ReadPropertyString("Postleitzahl"));
	    curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
	    $result = json_decode(curl_exec($ch));
	    print_r($result);
		}


		public function Destroy()
		{
			//Never delete this line!
			parent::Destroy();
		}

		public function ApplyChanges()
		{
			//Never delete this line!
			parent::ApplyChanges();
			$eid = IPS_CreateEvent(0);
			IPS_SetEventTrigger($eid, 1, $this->ReadPropertyInteger("IPSMeter"));
			IPS_SetParent($eid, $_IPS['SELF']);
			IPS_SetEventActive($eid, true);
			IPS_SetEventScript($eid, "SDAO_Update(1234);");
		}

	}
