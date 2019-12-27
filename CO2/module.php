<?php
	class CO2 extends IPSModule {

		public function Create()
		{
			//Never delete this line!
			parent::Create();

			$randstr = bin2hex(random_bytes(40));
			$secret = bin2hex(random_bytes(40));

			// Register some required parameters for our Corrently CO2 Reading operation
			$this->RegisterVariableString("Postleitzahl", "Postleitzahl");
			$this->RegisterVariableString("meterId", "meterId");
			$this->RegisterVariableString("secret", "secret");


 			$this->SetValueString("Postleitzahl", "69256");
			$this->SetValueString("meterid", $meterid);
			$this->SetValueString("secret", secret);

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
