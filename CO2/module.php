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
		public function Update() {
				echo "Update";
				echo $this->ReadPropertyString("Postleitzahl");
				echo $this->ReadPropertyString("meterId");
				echo $this->ReadPropertyInteger("IPSMeter");

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
