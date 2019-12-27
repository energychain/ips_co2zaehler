<?php
class CO2EmissionDiscovergyMeter extends IPSModule {

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
			$this->RegisterVariableInteger("reading_in_wh", "Zählerstand (in Wh)");
			$this->RegisterVariableInteger("co2g_standard", "CO2 (Standard)");
			$this->RegisterVariableInteger("co2g_oekostrom", "CO2 (Ökostrom)");
			$this->RegisterVariableInteger("account", "Kompensations Account");
			if(!IPS_GetVariableProfile("co2gramm")) {
				IPS_CreateVariableProfile ("co2gramm", 1);
				IPS_SetVariableProfileText("co2gramm","","g");
				IPS_SetVariableProfileIcon("co2gramm",  "Flame");
			}
		}

		public function setReading($reading_in_wh) {
			$reading_in_wh = round($reading_in_wh); //ensure that we receive an integer
			$ch = curl_init("https://api.corrently.io/core/reading");
	    curl_setopt($ch,CURLOPT_POST,true);
	    curl_setopt($ch,CURLOPT_POSTFIELDS,"&externalAccount=ips_".$this->ReadPropertyString("meterId")."_".$this->ReadPropertyString("Postleitzahl")."&secret=".$this->ReadPropertyString("secret")."&energy=".$reading_in_wh."&zip=".$this->ReadPropertyString("Postleitzahl"));
	    curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
	    $result = json_decode(curl_exec($ch));
			if(isset($result->co2_g_standard)) {
					SetValue($this->GetIDForIdent("co2g_standard"), $result->co2_g_standard);
					SetValue($this->GetIDForIdent("co2g_oekostrom"), $result->co2_g_oekostrom);
					SetValue($this->GetIDForIdent("account"), $result->account);
					SetValue($this->GetIDForIdent("reading_in_wh"), $reading_in_wh);
					IPS_SetVariableCustomProfile ($this->GetIDForIdent("co2g_standard"), "co2gramm");
					IPS_SetVariableCustomProfile ($this->GetIDForIdent("co2g_oekostrom"), "co2gramm");
			}
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
		}

	}
