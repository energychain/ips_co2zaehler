<?php
class CO2 extends IPSModule {

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
			$this->RegisterPropertyInteger("IPSMeter", 0);
		}

		public function calculate() {
				print_r($_IPS);
				echo $this->InstanceID;
				echo $this->ReadPropertyString("Postleitzahl");
				echo $this->ReadPropertyString("meterId");

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
